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
    /**
     * Récupère les clés CinetPay de l'agence propriétaire du bien
     */
    private function getAgenceKeys(Bien $bien): ?Agence
    {
        return Agence::find($bien->id_agence);
    }

    /**
     * Initialise une transaction CinetPay et retourne l'URL de paiement
     */
    private function initTransaction(array $params): array
    {
        $response = Http::post('https://api-checkout.cinetpay.com/v2/payment', $params);

        if ($response->failed()) {
            return ['success' => false, 'message' => 'Erreur de connexion CinetPay'];
        }

        $data = $response->json();

        if (($data['code'] ?? '') !== '201') {
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Erreur CinetPay',
            ];
        }

        return [
            'success'         => true,
            'payment_url'     => $data['data']['payment_url'] ?? null,
            'transaction_id'  => $params['transaction_id'],
        ];
    }

    // ═══ PAIEMENT ACOMPTE (10%) ═══
    public function payerAcompte(Request $request)
    {
        if (!session('client')) {
            return redirect()->route('login');
        }

        $request->validate([
            'id_bien'      => 'required|exists:biens,id_bien',
            'type_contrat' => 'required|in:location,vente',
        ]);

        $bien    = Bien::findOrFail($request->id_bien);
        $agence  = $this->getAgenceKeys($bien);
        $client  = session('client');

        if (!$agence || !$agence->cinetpay_site_id || !$agence->cinetpay_api_key) {
            return back()->with('error',
                'Le paiement en ligne n\'est pas encore configuré pour cette agence. '.
                'Veuillez contacter l\'agence directement.'
            );
        }

        $montant       = (int) round($bien->prix * 0.10);
        $transactionId = 'ACP-' . strtoupper(Str::random(12));

        // Sauvegarder en session avant redirection
        session([
            'cinetpay_transaction_id' => $transactionId,
            'cinetpay_id_bien'        => $bien->id_bien,
            'cinetpay_type_contrat'   => $request->type_contrat,
            'cinetpay_type_paiement'  => 'acompte',
            'cinetpay_montant'        => $montant,
            'cinetpay_id_agence'      => $agence->id_agence,
        ]);

        $result = $this->initTransaction([
            'apikey'            => $agence->cinetpay_api_key,
            'site_id'           => $agence->cinetpay_site_id,
            'transaction_id'    => $transactionId,
            'amount'            => $montant,
            'currency'          => 'XOF',
            'description'       => 'Acompte 10% - ' . $bien->titre_bien,
            'return_url'        => route('cinetpay.callback'),
            'notify_url'        => route('cinetpay.notify'),
            'customer_name'     => $client->nom_client,
            'customer_surname'  => $client->prenom_client,
            'customer_email'    => $client->email,
            'customer_phone_number' => $client->tel_client,
            'customer_address'  => 'Mali',
            'customer_city'     => 'Bamako',
            'customer_country'  => 'ML',
            'customer_state'    => 'ML',
            'customer_zip_code' => '00000',
            'channels'          => 'ALL',
            'metadata'          => json_encode([
                'type'          => 'acompte',
                'id_bien'       => $bien->id_bien,
                'id_client'     => $client->id_client,
            ]),
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Erreur paiement : ' . $result['message']);
        }

        return redirect($result['payment_url']);
    }

    // ═══ PAIEMENT TOTAL ═══
    public function payerTotal(Request $request)
    {
        if (!session('client')) {
            return redirect()->route('login');
        }

        $request->validate([
            'id_bien'      => 'required|exists:biens,id_bien',
            'type_contrat' => 'required|in:location,vente',
        ]);

        $bien    = Bien::findOrFail($request->id_bien);
        $agence  = $this->getAgenceKeys($bien);
        $client  = session('client');

        if (!$agence || !$agence->cinetpay_site_id || !$agence->cinetpay_api_key) {
            return back()->with('error',
                'Le paiement en ligne n\'est pas encore configuré pour cette agence.'
            );
        }

        $montant       = (int) $bien->prix;
        $transactionId = 'TOT-' . strtoupper(Str::random(12));

        session([
            'cinetpay_transaction_id' => $transactionId,
            'cinetpay_id_bien'        => $bien->id_bien,
            'cinetpay_type_contrat'   => $request->type_contrat,
            'cinetpay_type_paiement'  => 'complet',
            'cinetpay_montant'        => $montant,
            'cinetpay_id_agence'      => $agence->id_agence,
        ]);

        $result = $this->initTransaction([
            'apikey'            => $agence->cinetpay_api_key,
            'site_id'           => $agence->cinetpay_site_id,
            'transaction_id'    => $transactionId,
            'amount'            => $montant,
            'currency'          => 'XOF',
            'description'       => 'Paiement total - ' . $bien->titre_bien,
            'return_url'        => route('cinetpay.callback'),
            'notify_url'        => route('cinetpay.notify'),
            'customer_name'     => $client->nom_client,
            'customer_surname'  => $client->prenom_client,
            'customer_email'    => $client->email,
            'customer_phone_number' => $client->tel_client,
            'customer_address'  => 'Mali',
            'customer_city'     => 'Bamako',
            'customer_country'  => 'ML',
            'customer_state'    => 'ML',
            'customer_zip_code' => '00000',
            'channels'          => 'ALL',
            'metadata'          => json_encode([
                'type'          => 'complet',
                'id_bien'       => $bien->id_bien,
                'id_client'     => $client->id_client,
            ]),
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Erreur paiement : ' . $result['message']);
        }

        return redirect($result['payment_url']);
    }

    // ═══ PAIEMENT SOLDE ═══
    public function payerSolde(Request $request)
    {
        if (!session('client')) {
            return redirect()->route('login');
        }

        $request->validate([
            'id_contrat' => 'required|exists:contrats,id_contrat',
        ]);

        $contrat = Contrat::with(['bien', 'paiements', 'location', 'vente'])
                          ->findOrFail($request->id_contrat);

        $bien   = $contrat->bien;
        $agence = $this->getAgenceKeys($bien);
        $client = session('client');

        if (!$agence || !$agence->cinetpay_site_id || !$agence->cinetpay_api_key) {
            return back()->with('error',
                'Le paiement en ligne n\'est pas encore configuré pour cette agence.'
            );
        }

        $montantTotal = $contrat->type_contrat === 'location'
            ? $contrat->location->montant_total_location
            : $contrat->vente->montant_total_vente;

        $dejaPaye = $contrat->paiements->sum('montant');
        $solde    = (int) max(0, $montantTotal - $dejaPaye);

        if ($solde <= 0) {
            return back()->with('error', 'Ce contrat est déjà entièrement payé.');
        }

        $transactionId = 'SOL-' . strtoupper(Str::random(12));

        session([
            'cinetpay_transaction_id' => $transactionId,
            'cinetpay_id_contrat'     => $contrat->id_contrat,
            'cinetpay_type_paiement'  => 'solde',
            'cinetpay_montant'        => $solde,
            'cinetpay_id_agence'      => $agence->id_agence,
        ]);

        $result = $this->initTransaction([
            'apikey'            => $agence->cinetpay_api_key,
            'site_id'           => $agence->cinetpay_site_id,
            'transaction_id'    => $transactionId,
            'amount'            => $solde,
            'currency'          => 'XOF',
            'description'       => 'Solde restant - ' . $bien->titre_bien,
            'return_url'        => route('cinetpay.callback'),
            'notify_url'        => route('cinetpay.notify'),
            'customer_name'     => $client->nom_client,
            'customer_surname'  => $client->prenom_client,
            'customer_email'    => $client->email,
            'customer_phone_number' => $client->tel_client,
            'customer_address'  => 'Mali',
            'customer_city'     => 'Bamako',
            'customer_country'  => 'ML',
            'customer_state'    => 'ML',
            'customer_zip_code' => '00000',
            'channels'          => 'ALL',
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Erreur paiement : ' . $result['message']);
        }

        return redirect($result['payment_url']);
    }

    // ═══ CALLBACK — CinetPay redirige ici après paiement ═══
    public function callback(Request $request)
    {
        $transactionId = session('cinetpay_transaction_id');

        if (!$transactionId) {
            return redirect()->route('home')
                             ->with('error', 'Transaction introuvable.');
        }

        // Vérifier le statut via l'API CinetPay
        $agence = Agence::find(session('cinetpay_id_agence'));

        if (!$agence) {
            return redirect()->route('client.reservations')
                             ->with('error', 'Agence introuvable.');
        }

        $verif = Http::post('https://api-checkout.cinetpay.com/v2/payment/check', [
            'apikey'         => $agence->cinetpay_api_key,
            'site_id'        => $agence->cinetpay_site_id,
            'transaction_id' => $transactionId,
        ]);

        $data = $verif->json();

        if (($data['code'] ?? '') !== '00' || ($data['data']['status'] ?? '') !== 'ACCEPTED') {
            session()->forget([
                'cinetpay_transaction_id', 'cinetpay_id_bien',
                'cinetpay_id_contrat', 'cinetpay_type_contrat',
                'cinetpay_type_paiement', 'cinetpay_montant', 'cinetpay_id_agence',
            ]);
            return redirect()->route('client.reservations')
                             ->with('error', 'Paiement échoué ou annulé.');
        }

        return $this->traiterPaiementValide($transactionId);
    }

    // ═══ NOTIFY — webhook CinetPay (appelé côté serveur) ═══
    public function notify(Request $request)
    {
        // CinetPay envoie cid & transaction_id en POST
        $transactionId = $request->input('cpm_trans_id') ?? $request->input('transaction_id');

        if (!$transactionId) {
            return response()->json(['status' => 'error', 'message' => 'transaction_id manquant'], 400);
        }

        // On ne peut pas utiliser la session côté webhook
        // On cherche le paiement existant pour éviter le doublon
        $paiementExistant = Paiement::where('reference', 'CPAY-' . $transactionId)->first();
        if ($paiementExistant) {
            return response()->json(['status' => 'ok', 'message' => 'déjà traité']);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Traitement commun après validation du paiement
     */
    private function traiterPaiementValide(string $transactionId)
    {
        $typePaiement = session('cinetpay_type_paiement');
        $montant      = session('cinetpay_montant');
        $client       = session('client');

        $modePaiement = ModePaiement::firstOrCreate(
            ['nom_mode_paiement' => 'CinetPay']
        );

        if (in_array($typePaiement, ['acompte', 'complet'])) {
            $bien        = Bien::find(session('cinetpay_id_bien'));
            $typeContrat = session('cinetpay_type_contrat');

            if (!$bien) {
                return redirect()->route('client.reservations')
                                 ->with('error', 'Bien introuvable.');
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
                    'montant_total_location'     => $bien->prix,
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

            if ($typePaiement === 'complet') {
                $statut = $typeContrat === 'location' ? 'loue' : 'vendu';
                $bien->update(['statut' => $statut]);
            } else {
                $bien->update(['statut' => 'reserve']);
            }

        } elseif ($typePaiement === 'solde') {
            $contrat = Contrat::with(['paiements', 'bien', 'location', 'vente'])
                              ->find(session('cinetpay_id_contrat'));

            if (!$contrat) {
                return redirect()->route('client.reservations')
                                 ->with('error', 'Contrat introuvable.');
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
            $statut = $contrat->type_contrat === 'location' ? 'loue' : 'vendu';
            $contrat->bien->update(['statut' => $statut]);
        }

        // Nettoyer session
        session()->forget([
            'cinetpay_transaction_id', 'cinetpay_id_bien',
            'cinetpay_id_contrat', 'cinetpay_type_contrat',
            'cinetpay_type_paiement', 'cinetpay_montant', 'cinetpay_id_agence',
        ]);

        return redirect()->route('client.reservations')
                         ->with('success',
                                '✅ Paiement CinetPay confirmé ! Référence : CPAY-' .
                                $transactionId);
    }
}
