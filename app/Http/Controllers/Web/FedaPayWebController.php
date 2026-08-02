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
use Illuminate\Support\Str;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class FedaPayWebController extends Controller
{
    // ─── Configure le SDK FedaPay avec les clés de l'agence ──────────────────
    private function configure(Agence $agence): void
    {
        FedaPay::setApiKey($agence->fedapay_secret_key);
        FedaPay::setEnvironment($agence->fedapay_env ?? 'sandbox');
    }

    // ─── Récupère l'agence d'un bien ─────────────────────────────────────────
    private function getAgence(Bien $bien): ?Agence
    {
        return Agence::find($bien->id_agence);
    }

    // ─── Check clés configurées ──────────────────────────────────────────────
    private function checkKeys(?Agence $agence): bool
    {
        return $agence && $agence->fedapay_secret_key;
    }

    // ─── Calcul montant total ────────────────────────────────────────────────
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

    // ═════════════════════════════════════════════════════════════════════════
    // PAIEMENT ACOMPTE (10%)
    // ═════════════════════════════════════════════════════════════════════════
    public function payerAcompte(Request $request)
    {
        if (!session('client')) return redirect()->route('login');

        $request->validate([
            'id_bien'      => 'required|exists:biens,id_bien',
            'type_contrat' => 'required|in:location,vente',
        ]);

        $bien   = Bien::findOrFail($request->id_bien);
        $agence = $this->getAgence($bien);
        $client = session('client');

        if (!$this->checkKeys($agence)) {
            return back()->with('error', 'Le paiement en ligne n\'est pas encore configuré pour cette agence. Contactez-les directement.');
        }

        $montantTotal  = $this->getMontantTotal($bien);
        $montant       = (int) round($montantTotal * 0.10);
        $transactionId = 'ACP-' . strtoupper(Str::random(10));

        try {
            $this->configure($agence);

            $transaction = Transaction::create([
                'description'  => 'Acompte 10% - ' . $bien->titre_bien,
                'amount'       => $montant,
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('fedapay.callback'),
                'customer'     => [
                    'firstname'    => $client->prenom_client,
                    'lastname'     => $client->nom_client,
                    'email'        => $client->email,
                    'phone_number' => [
                        'number'  => $client->tel_client ?? '00000000',
                        'country' => 'BJ',
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            session([
                'fedapay_transaction_id' => $transaction->id,
                'fedapay_id_bien'        => $bien->id_bien,
                'fedapay_type_contrat'   => $request->type_contrat,
                'fedapay_type_paiement'  => 'acompte',
                'fedapay_montant'        => $montant,
                'fedapay_id_agence'      => $agence->id_agence,
            ]);

            return redirect($token->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PAIEMENT TOTAL
    // ═════════════════════════════════════════════════════════════════════════
    public function payerTotal(Request $request)
    {
        if (!session('client')) return redirect()->route('login');

        $request->validate([
            'id_bien'      => 'required|exists:biens,id_bien',
            'type_contrat' => 'required|in:location,vente',
        ]);

        $bien   = Bien::findOrFail($request->id_bien);
        $agence = $this->getAgence($bien);
        $client = session('client');

        if (!$this->checkKeys($agence)) {
            return back()->with('error', 'Le paiement en ligne n\'est pas encore configuré pour cette agence. Contactez-les directement.');
        }

        $montant = $this->getMontantTotal($bien);

        try {
            $this->configure($agence);

            $transaction = Transaction::create([
                'description'  => 'Paiement total - ' . $bien->titre_bien,
                'amount'       => $montant,
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('fedapay.callback'),
                'customer'     => [
                    'firstname'    => $client->prenom_client,
                    'lastname'     => $client->nom_client,
                    'email'        => $client->email,
                    'phone_number' => [
                        'number'  => $client->tel_client ?? '00000000',
                        'country' => 'BJ',
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            session([
                'fedapay_transaction_id' => $transaction->id,
                'fedapay_id_bien'        => $bien->id_bien,
                'fedapay_type_contrat'   => $request->type_contrat,
                'fedapay_type_paiement'  => 'complet',
                'fedapay_montant'        => $montant,
                'fedapay_id_agence'      => $agence->id_agence,
            ]);

            return redirect($token->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PAIEMENT SOLDE RESTANT
    // ═════════════════════════════════════════════════════════════════════════
    public function payerSolde(Request $request)
    {
        if (!session('client')) return redirect()->route('login');

        $request->validate(['id_contrat' => 'required|exists:contrats,id_contrat']);

        $contrat = Contrat::with(['bien', 'paiements', 'location', 'vente'])
                          ->findOrFail($request->id_contrat);
        $bien    = $contrat->bien;
        $agence  = $this->getAgence($bien);
        $client  = session('client');

        if (!$this->checkKeys($agence)) {
            return back()->with('error', 'Le paiement en ligne n\'est pas encore configuré pour cette agence.');
        }

        $montantTotal = $contrat->type_contrat === 'location'
            ? $contrat->location->montant_total_location
            : $contrat->vente->montant_total_vente;
        $solde = (int) max(0, $montantTotal - $contrat->paiements->sum('montant'));

        if ($solde <= 0) {
            return back()->with('error', 'Ce contrat est déjà entièrement payé.');
        }

        try {
            $this->configure($agence);

            $transaction = Transaction::create([
                'description'  => 'Solde restant - ' . $bien->titre_bien,
                'amount'       => $solde,
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('fedapay.callback'),
                'customer'     => [
                    'firstname'    => $client->prenom_client,
                    'lastname'     => $client->nom_client,
                    'email'        => $client->email,
                    'phone_number' => [
                        'number'  => $client->tel_client ?? '00000000',
                        'country' => 'BJ',
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            session([
                'fedapay_transaction_id' => $transaction->id,
                'fedapay_id_contrat'     => $contrat->id_contrat,
                'fedapay_type_paiement'  => 'solde',
                'fedapay_montant'        => $solde,
                'fedapay_id_agence'      => $agence->id_agence,
            ]);

            return redirect($token->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CALLBACK — FedaPay redirige ici après paiement
    // ═════════════════════════════════════════════════════════════════════════
    public function callback(Request $request)
    {
        $transactionId = session('fedapay_transaction_id');

        if (!$transactionId) {
            return redirect()->route('home')->with('error', 'Transaction introuvable.');
        }

        $agence = Agence::find(session('fedapay_id_agence'));
        if (!$agence) {
            return redirect()->route('client.reservations')->with('error', 'Agence introuvable.');
        }

        try {
            $this->configure($agence);
            $transaction = Transaction::retrieve($transactionId);
        } catch (\Exception $e) {
            return redirect()->route('client.reservations')->with('error', 'Erreur vérification : ' . $e->getMessage());
        }

        if ($transaction->status !== 'approved') {
            $this->clearSession();
            return redirect()->route('client.reservations')->with('error', 'Paiement échoué ou annulé (statut : ' . $transaction->status . ').');
        }

        return $this->traiterPaiementValide((string) $transactionId);
    }

    // ─── Traitement après paiement validé ────────────────────────────────────
    private function traiterPaiementValide(string $transactionId)
    {
        $typePaiement = session('fedapay_type_paiement');
        $montant      = session('fedapay_montant');
        $client       = session('client');

        $modePaiement = ModePaiement::firstOrCreate(['nom_mode_paiement' => 'FedaPay']);

        if (in_array($typePaiement, ['acompte', 'complet'])) {
            $bien        = Bien::find(session('fedapay_id_bien'));
            $typeContrat = session('fedapay_type_contrat');

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
                'reference'        => 'FEDA-' . $transactionId,
            ]);

            $bien->update(['statut' => $typePaiement === 'complet'
                ? ($typeContrat === 'location' ? 'loue' : 'vendu')
                : 'reserve'
            ]);

        } elseif ($typePaiement === 'solde') {
            $contrat = Contrat::with(['paiements', 'bien', 'location', 'vente'])
                              ->find(session('fedapay_id_contrat'));

            if (!$contrat) {
                return redirect()->route('client.reservations')->with('error', 'Contrat introuvable.');
            }

            Paiement::create([
                'id_contrat'       => $contrat->id_contrat,
                'id_mode_paiement' => $modePaiement->id_mode_paiement,
                'montant'          => $montant,
                'date_paiement'    => now(),
                'type_paiement'    => 'solde',
                'reference'        => 'FEDA-' . $transactionId,
            ]);

            $contrat->update(['statut_contrat' => 'confirme']);
            $contrat->bien->update(['statut' => $contrat->type_contrat === 'location' ? 'loue' : 'vendu']);
        }

        $this->clearSession();

        return redirect()->route('client.reservations')
                         ->with('success', '✅ Paiement FedaPay confirmé ! Référence : FEDA-' . $transactionId);
    }

    private function clearSession(): void
    {
        session()->forget([
            'fedapay_transaction_id', 'fedapay_id_bien', 'fedapay_id_contrat',
            'fedapay_type_contrat', 'fedapay_type_paiement',
            'fedapay_montant', 'fedapay_id_agence',
        ]);
    }
}
