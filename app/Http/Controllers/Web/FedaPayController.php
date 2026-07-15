<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Location;
use App\Models\Vente;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class FedaPayController extends Controller
{
    public function __construct()
    {
        // Initialiser FedaPay
        FedaPay::setApiKey(env('FEDAPAY_SECRET_KEY'));
        FedaPay::setEnvironment(env('FEDAPAY_ENV', 'sandbox'));
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

        $bien   = Bien::find($request->id_bien);
        $client = session('client');
        $montantAcompte = $bien->prix * 0.10;

        // Créer la transaction FedaPay
        $transaction = Transaction::create([
            'description' => 'Acompte 10% - ' . $bien->titre_bien,
            'amount'      => $montantAcompte,
            'currency'    => ['iso' => 'XOF'],
            'callback_url'=> route('fedapay.callback'),
            'customer'    => [
                'firstname' => $client->prenom_client,
                'lastname'  => $client->nom_client,
                'email'     => $client->email,
                'phone_number' => [
                    'number'  => $client->tel_client,
                    'country' => 'ML',
                ],
            ],
        ]);

        // Stocker en session pour callback
        session([
            'fedapay_transaction_id' => $transaction->id,
            'fedapay_id_bien'        => $request->id_bien,
            'fedapay_type_contrat'   => $request->type_contrat,
            'fedapay_type_paiement'  => 'acompte',
            'fedapay_montant'        => $montantAcompte,
        ]);

        // Rediriger vers la page de paiement FedaPay
        $token = $transaction->generateToken();
        return redirect($token->url);
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

        $bien   = Bien::find($request->id_bien);
        $client = session('client');

        $transaction = Transaction::create([
            'description' => 'Paiement total - ' . $bien->titre_bien,
            'amount'      => $bien->prix,
            'currency'    => ['iso' => 'XOF'],
            'callback_url'=> route('fedapay.callback'),
            'customer'    => [
                'firstname' => $client->prenom_client,
                'lastname'  => $client->nom_client,
                'email'     => $client->email,
                'phone_number' => [
                    'number'  => $client->tel_client,
                    'country' => 'ML',
                ],
            ],
        ]);

        session([
            'fedapay_transaction_id' => $transaction->id,
            'fedapay_id_bien'        => $request->id_bien,
            'fedapay_type_contrat'   => $request->type_contrat,
            'fedapay_type_paiement'  => 'complet',
            'fedapay_montant'        => $bien->prix,
        ]);

        $token = $transaction->generateToken();
        return redirect($token->url);
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
                          ->find($request->id_contrat);

        $montantTotal = $contrat->type_contrat == 'location'
            ? $contrat->location->montant_total_location
            : $contrat->vente->montant_total_vente;

        $dejaPaye = $contrat->paiements->sum('montant');
        $solde    = $montantTotal - $dejaPaye;

        $client = session('client');

        $transaction = Transaction::create([
            'description' => 'Solde restant - ' . $contrat->bien->titre_bien,
            'amount'      => $solde,
            'currency'    => ['iso' => 'XOF'],
            'callback_url'=> route('fedapay.callback'),
            'customer'    => [
                'firstname' => $client->prenom_client,
                'lastname'  => $client->nom_client,
                'email'     => $client->email,
                'phone_number' => [
                    'number'  => $client->tel_client,
                    'country' => 'ML',
                ],
            ],
        ]);

        session([
            'fedapay_transaction_id' => $transaction->id,
            'fedapay_id_contrat'     => $request->id_contrat,
            'fedapay_type_paiement'  => 'solde',
            'fedapay_montant'        => $solde,
        ]);

        $token = $transaction->generateToken();
        return redirect($token->url);
    }

    // ═══ CALLBACK — FedaPay redirige ici après paiement ═══
    public function callback(Request $request)
    {
        $transactionId = session('fedapay_transaction_id');

        if (!$transactionId) {
            return redirect()->route('home')
                            ->with('error', 'Transaction introuvable');
        }

        // Vérifier le statut de la transaction
        $transaction = Transaction::retrieve($transactionId);

        if ($transaction->status !== 'approved') {
            return redirect()->route('client.reservations')
                            ->with('error', 'Paiement échoué ou annulé');
        }

        $typePaiement = session('fedapay_type_paiement');
        $montant      = session('fedapay_montant');
        $client       = session('client');

        // Trouver ou créer le mode paiement FedaPay
        $modePaiement = ModePaiement::firstOrCreate(
            ['nom_mode_paiement' => 'FedaPay'],
        );

        if ($typePaiement === 'acompte' || $typePaiement === 'complet') {
            $bien = Bien::find(session('fedapay_id_bien'));
            $typeContrat = session('fedapay_type_contrat');

            // Créer le contrat
            $contrat = Contrat::create([
                'id_client'      => $client->id_client,
                'id_bien'        => $bien->id_bien,
                'type_contrat'   => $typeContrat,
                'statut_contrat' => $typePaiement === 'complet' ? 'confirme' : 'en_attente',
                'date_location'  => now(),
            ]);

            // Créer location ou vente
            if ($typeContrat === 'location') {
                Location::create([
                    'id_contrat'                => $contrat->id_contrat,
                    'montant_total_location'    => $bien->prix,
                    'date_reserv_location'      => now(),
                    'date_limite_solde_location'=> now()->addDays(30),
                ]);
            } else {
                Vente::create([
                    'id_contrat'              => $contrat->id_contrat,
                    'montant_total_vente'     => $bien->prix,
                    'date_reserv_vente'       => now(),
                    'date_limite_solde_vente' => now()->addDays(30),
                ]);
            }

            // Créer le paiement
            Paiement::create([
                'id_contrat'       => $contrat->id_contrat,
                'id_mode_paiement' => $modePaiement->id_mode_paiement,
                'montant'          => $montant,
                'date_paiement'    => now(),
                'type_paiement'    => $typePaiement,
                'reference'        => 'FEDA-' . $transaction->id,
            ]);

            // Mettre à jour statut bien
            if ($typePaiement === 'complet') {
                $statut = $typeContrat === 'location' ? 'loue' : 'vendu';
                $bien->update(['statut' => $statut]);
            } else {
                $bien->update(['statut' => 'reserve']);
            }

        } elseif ($typePaiement === 'solde') {
            $contrat = Contrat::with(['paiements', 'bien', 'location', 'vente'])
                              ->find(session('fedapay_id_contrat'));

            Paiement::create([
                'id_contrat'       => $contrat->id_contrat,
                'id_mode_paiement' => $modePaiement->id_mode_paiement,
                'montant'          => $montant,
                'date_paiement'    => now(),
                'type_paiement'    => 'solde',
                'reference'        => 'FEDA-' . $transaction->id,
            ]);

            $contrat->update(['statut_contrat' => 'confirme']);
            $statut = $contrat->type_contrat === 'location' ? 'loue' : 'vendu';
            $contrat->bien->update(['statut' => $statut]);
        }

        // Nettoyer la session
        session()->forget([
            'fedapay_transaction_id',
            'fedapay_id_bien',
            'fedapay_id_contrat',
            'fedapay_type_contrat',
            'fedapay_type_paiement',
            'fedapay_montant',
        ]);

        return redirect()->route('client.reservations')
                        ->with('success',
                               '✅ Paiement réussi ! Référence : FEDA-' .
                               $transaction->id);
    }
}

