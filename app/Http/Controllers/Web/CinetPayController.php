<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Agence;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Location;
use App\Models\Vente;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CinetPayController extends Controller
{
    // ─── Infos client à envoyer à CinetPay ───────────────────────────────────
    private function customerInfo($client): array
    {
        return [
            'customer_name'         => $client->nom_client,
            'customer_surname'      => $client->prenom_client,
            'customer_email'        => $client->email,
            'customer_phone_number' => $client->tel_client ?? '',
            'customer_address'      => 'Bénin',
            'customer_city'         => 'Cotonou',
            'customer_country'      => 'BJ',
            'customer_state'        => 'BJ',
            'customer_zip_code'     => '00000',
        ];
    }

    // ─── Clés de l'agence ────────────────────────────────────────────────────
    private function getAgenceKeys(Bien $bien): ?Agence
    {
        return Agence::find($bien->id_agence);
    }

    // ─── Montant total pour un bien ──────────────────────────────────────────
    private function getMontantTotal(Bien $bien): int
    {
        if ($bien->type_contrat === 'location') {
            $loyer  = $bien->prix;
            $avance = $loyer * ($bien->nb_mois_avance ?? 0);
            $eau    = (float) ($bien->caution_eau ?? 0);
            $elec   = (float) ($bien->caution_electricite ?? 0);
            return (int) round($loyer + $avance + $eau + $elec);
        }
        return (int) round($bien->prix);
    }

    // ─── Check clés configurées ──────────────────────────────────────────────
    private function checkKeys(?Agence $agence): bool
    {
        return $agence && $agence->cinetpay_site_id && $agence->cinetpay_api_key;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // INIT AJAX — SDK JS CinetPay (acompte ou complet)
    // ═════════════════════════════════════════════════════════════════════════
    public function initAjax(Request $request)
    {
        if (!session('client')) {
            return response()->json(['success' => false, 'message' => 'Non connecté'], 401);
        }

        $request->validate([
            'id_bien'       => 'required|exists:biens,id_bien',
            'type_contrat'  => 'required|in:location,vente',
            'type_paiement' => 'required|in:acompte,complet',
        ]);

        $bien   = Bien::findOrFail($request->id_bien);
        $agence = $this->getAgenceKeys($bien);
        $client = session('client');

        if (!$this->checkKeys($agence)) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non configuré pour cette agence. Contactez-les directement.',
            ]);
        }

        $montantTotal  = $this->getMontantTotal($bien);
        $montant       = $request->type_paiement === 'acompte'
                         ? (int) round($montantTotal * 0.10)
                         : $montantTotal;
        $transactionId = ($request->type_paiement === 'acompte' ? 'ACP' : 'TOT')
                         . '-' . strtoupper(Str::random(12));

        session([
            'cinetpay_transaction_id' => $transactionId,
            'cinetpay_id_bien'        => $bien->id_bien,
            'cinetpay_type_contrat'   => $request->type_contrat,
            'cinetpay_type_paiement'  => $request->type_paiement === 'acompte' ? 'acompte' : 'complet',
            'cinetpay_montant'        => $montant,
            'cinetpay_id_agence'      => $agence->id_agence,
        ]);

        return response()->json(array_merge([
            'success'        => true,
            'site_id'        => $agence->cinetpay_site_id,
            'apikey'         => $agence->cinetpay_api_key,
            'transaction_id' => $transactionId,
            'amount'         => $montant,
            'currency'       => 'XOF',
            'channels'       => 'ALL',
            'description'    => ($request->type_paiement === 'acompte' ? 'Acompte 10% - ' : 'Paiement total - ')
                                . $bien->titre_bien,
            'notify_url'     => route('cinetpay.notify'),
            'return_url'     => route('cinetpay.callback'),
        ], $this->customerInfo($client)));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // INIT AJAX SOLDE — SDK JS CinetPay
    // ═════════════════════════════════════════════════════════════════════════
    public function initAjaxSolde(Request $request)
    {
        if (!session('client')) {
            return response()->json(['success' => false, 'message' => 'Non connecté'], 401);
        }

        $request->validate(['id_contrat' => 'required|exists:contrats,id_contrat']);

        $contrat = Contrat::with(['bien', 'paiements', 'location', 'vente'])
                          ->findOrFail($request->id_contrat);
        $bien    = $contrat->bien;
        $agence  = $this->getAgenceKeys($bien);
        $client  = session('client');

        if (!$this->checkKeys($agence)) {
            return response()->json(['success' => false, 'message' => 'Paiement non configuré pour cette agence.']);
        }

        $montantTotal = $contrat->type_contrat === 'location'
            ? $contrat->location->montant_total_location
            : $contrat->vente->montant_total_vente;
        $solde = (int) max(0, $montantTotal - $contrat->paiements->sum('montant'));

        if ($solde <= 0) {
            return response()->json(['success' => false, 'message' => 'Contrat déjà entièrement payé.']);
        }

        $transactionId = 'SOL-' . strtoupper(Str::random(12));

        session([
            'cinetpay_transaction_id' => $transactionId,
            'cinetpay_id_contrat'     => $contrat->id_contrat,
            'cinetpay_type_paiement'  => 'solde',
            'cinetpay_montant'        => $solde,
            'cinetpay_id_agence'      => $agence->id_agence,
        ]);

        return response()->json(array_merge([
            'success'        => true,
            'site_id'        => $agence->cinetpay_site_id,
            'apikey'         => $agence->cinetpay_api_key,
            'transaction_id' => $transactionId,
            'amount'         => $solde,
            'currency'       => 'XOF',
            'channels'       => 'ALL',
            'description'    => 'Solde restant - ' . $bien->titre_bien,
            'notify_url'     => route('cinetpay.notify'),
            'return_url'     => route('cinetpay.callback'),
        ], $this->customerInfo($client)));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CALLBACK — CinetPay redirige ici après paiement
    // ═════════════════════════════════════════════════════════════════════════
    public function callback(Request $request)
    {
        $transactionId = $request->query('transaction_id')
                      ?? session('cinetpay_transaction_id');

        if (!$transactionId) {
            return redirect()->route('home')->with('error', 'Transaction introuvable.');
        }

        $agence = Agence::find(session('cinetpay_id_agence'));
        if (!$agence) {
            return redirect()->route('client.reservations')->with('error', 'Agence introuvable.');
        }

        try {
            $verif = Http::timeout(15)->post('https://new-api.cinetpay.ci/v2/payment/check', [
                'apikey'         => $agence->cinetpay_api_key,
                'site_id'        => $agence->cinetpay_site_id,
                'transaction_id' => $transactionId,
            ]);
            $data = $verif->json();
        } catch (\Exception $e) {
            return redirect()->route('client.reservations')
                             ->with('error', 'Impossible de vérifier le paiement : ' . $e->getMessage());
        }

        if (($data['code'] ?? '') !== '00' || ($data['data']['status'] ?? '') !== 'ACCEPTED') {
            session()->forget([
                'cinetpay_transaction_id','cinetpay_id_bien','cinetpay_id_contrat',
                'cinetpay_type_contrat','cinetpay_type_paiement','cinetpay_montant','cinetpay_id_agence',
            ]);
            return redirect()->route('client.reservations')->with('error', 'Paiement échoué ou annulé.');
        }

        return $this->traiterPaiementValide($transactionId);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // NOTIFY — webhook serveur-à-serveur
    // ═════════════════════════════════════════════════════════════════════════
    public function notify(Request $request)
    {
        $transactionId = $request->input('cpm_trans_id') ?? $request->input('transaction_id');
        if (!$transactionId) {
            return response()->json(['status' => 'error', 'message' => 'transaction_id manquant'], 400);
        }
        if (Paiement::where('reference', 'CPAY-' . $transactionId)->exists()) {
            return response()->json(['status' => 'ok', 'message' => 'déjà traité']);
        }
        return response()->json(['status' => 'ok']);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // TRAITEMENT PAIEMENT VALIDÉ
    // ═════════════════════════════════════════════════════════════════════════
    private function traiterPaiementValide(string $transactionId)
    {
        $typePaiement = session('cinetpay_type_paiement');
        $montant      = session('cinetpay_montant');
        $client       = session('client');

        $modePaiement = ModePaiement::firstOrCreate(['nom_mode_paiement' => 'CinetPay']);

        if (in_array($typePaiement, ['acompte', 'complet'])) {
            $bien        = Bien::find(session('cinetpay_id_bien'));
            $typeContrat = session('cinetpay_type_contrat');

            if (!$bien) {
                return redirect()->route('client.reservations')->with('error', 'Bien introuvable.');
            }

            $contrat = Contrat::create([
                'id_client'      => $client->id_client,
                'id_bien'        => $bien->id_bien,
                'type_contrat'   => $typeContrat,
                'statut_contrat' => $typePaiement === 'complet' ? 'confirme' : 'en_attente',
                'date_location'  => now(),
            ]);

            if ($typeContrat === 'location') {
                Location::create([
                    'id_contrat'                 => $contrat->id_contrat,
                    'montant_total_location'     => $this->getMontantTotal($bien),
                    'date_reserv_location'       => now(),
                    'date_limite_solde_location' => now()->addDays(30),
                ]);
            } else {
                Vente::create([
                    'id_contrat'              => $contrat->id_contrat,
                    'montant_total_vente'     => $bien->prix,
                    'date_reserv_vente'       => now(),
                    'date_limite_solde_vente' => now()->addDays(30),
                ]);
            }

            Paiement::create([
                'id_contrat'       => $contrat->id_contrat,
                'id_mode_paiement' => $modePaiement->id_mode_paiement,
                'montant'          => $montant,
                'date_paiement'    => now(),
                'type_paiement'    => $typePaiement,
                'reference'        => 'CPAY-' . $transactionId,
            ]);

            $bien->update(['statut' => $typePaiement === 'complet'
                ? ($typeContrat === 'location' ? 'loue' : 'vendu')
                : 'reserve'
            ]);

        } elseif ($typePaiement === 'solde') {
            $contrat = Contrat::with(['paiements', 'bien', 'location', 'vente'])
                              ->find(session('cinetpay_id_contrat'));

            if (!$contrat) {
                return redirect()->route('client.reservations')->with('error', 'Contrat introuvable.');
            }

            Paiement::create([
                'id_contrat'       => $contrat->id_contrat,
                'id_mode_paiement' => $modePaiement->id_mode_paiement,
                'montant'          => $montant,
                'date_paiement'    => now(),
                'type_paiement'    => 'solde',
                'reference'        => 'CPAY-' . $transactionId,
            ]);

            $contrat->update(['statut_contrat' => 'confirme']);
            $contrat->bien->update(['statut' => $contrat->type_contrat === 'location' ? 'loue' : 'vendu']);
        }

        session()->forget([
            'cinetpay_transaction_id','cinetpay_id_bien','cinetpay_id_contrat',
            'cinetpay_type_contrat','cinetpay_type_paiement','cinetpay_montant','cinetpay_id_agence',
        ]);

        return redirect()->route('client.reservations')
                         ->with('success', '✅ Paiement confirmé ! Référence : CPAY-' . $transactionId);
    }
}
