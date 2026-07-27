<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Administrateur;
use App\Models\TypeBien;
use App\Models\Departement;
use App\Models\Ville;
use App\Models\Quartier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    private function checkAuth()
    {
        if (!session('admin')) {
            return redirect()->route('login')
                           ->with('error', 'Accès réservé aux administrateurs');
        }
        return null;
    }

    // ═══ DASHBOARD ═══
    public function dashboard()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $idAgence = session('admin')->id_agence;

        $totalBiens      = Bien::where('id_agence', $idAgence)->count();
        $biensDisponibles= Bien::where('id_agence', $idAgence)
                               ->where('statut', 'disponible')->count();
        $biensReserves   = Bien::where('id_agence', $idAgence)
                               ->where('statut', 'reserve')->count();
        $totalContrats   = Contrat::whereHas('bien', function($q) use ($idAgence) {
                               $q->where('id_agence', $idAgence);
                           })->count();

        $derniersContrats = Contrat::with(['bien', 'client'])
                                   ->whereHas('bien', function($q) use ($idAgence) {
                                       $q->where('id_agence', $idAgence);
                                   })->latest()->take(5)->get();

        $derniersBiens = Bien::with(['typeBien', 'ville'])
                             ->where('id_agence', $idAgence)
                             ->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBiens', 'biensDisponibles',
            'biensReserves', 'totalContrats',
            'derniersContrats', 'derniersBiens'
        ));
    }

    // ═══ GESTION BIENS ═══
    public function biens()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $biens = Bien::with(['typeBien', 'ville', 'departement'])
                     ->where('id_agence', session('admin')->id_agence)
                     ->latest()->paginate(10);

        $typesBiens   = TypeBien::all();
        $departements = Departement::orderBy('nom_departement')->get();

        return view('admin.biens', compact('biens', 'typesBiens', 'departements'));
    }

    public function creerBien(Request $request)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $request->validate([
            'id_typebien'      => 'required|exists:type_biens,id_typebien',
            'titre_bien'       => 'required|string',
            'description_bien' => 'required|string',
            'superficie'       => 'required|numeric',
            'localisation'     => 'required|string',
            'type_contrat'     => 'required|in:vente,location',
            'id_departement'   => 'required|exists:departements,id_departement',
            'id_ville'         => 'required|exists:villes,id_ville',
            'id_quartier'      => 'nullable|exists:quartiers,id_quartier',
            'photos'           => 'nullable|array|max:5',
            'photos.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
            // vente
            'prix'             => 'required_if:type_contrat,vente|nullable|numeric|min:0',
            // location
            'loyer'            => 'required_if:type_contrat,location|nullable|numeric|min:0',
            'nb_mois_avance'   => 'nullable|integer|min:0|max:24',
            'caution_eau'      => 'nullable|numeric|min:0',
            'caution_electricite' => 'nullable|numeric|min:0',
        ]);

        // Le champ prix vient soit de 'prix' (vente) soit de 'loyer' (location)
        $prix = $request->type_contrat === 'location'
            ? $request->loyer
            : $request->prix;

        // Upload des photos
        $photosPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('biens', 'public');
                $photosPaths[] = $path;
            }
        }

        Bien::create([
            'id_agence'          => session('admin')->id_agence,
            'id_admin'           => session('admin')->id_admin,
            'id_typebien'        => $request->id_typebien,
            'titre_bien'         => $request->titre_bien,
            'description_bien'   => $request->description_bien,
            'prix'               => $prix,
            'superficie'         => $request->superficie,
            'localisation'       => $request->localisation,
            'id_departement'     => $request->id_departement,
            'id_ville'           => $request->id_ville,
            'id_quartier'        => $request->id_quartier,
            'statut'             => 'disponible',
            'type_contrat'       => $request->type_contrat,
            'nb_mois_avance'     => $request->type_contrat === 'location' ? $request->nb_mois_avance : null,
            'caution_eau'        => $request->type_contrat === 'location' ? $request->caution_eau : null,
            'caution_electricite'=> $request->type_contrat === 'location' ? $request->caution_electricite : null,
            'photos'             => !empty($photosPaths) ? $photosPaths : null,
        ]);

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien créé avec succès !');
    }

    public function modifierBien(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $bien = Bien::where('id_bien', $id)
                    ->where('id_agence', session('admin')->id_agence)
                    ->firstOrFail();

        $request->validate([
            'titre_bien'       => 'required|string',
            'description_bien' => 'required|string',
            'superficie'       => 'required|numeric',
            'localisation'     => 'required|string',
            'id_typebien'      => 'required|exists:type_biens,id_typebien',
            'type_contrat'     => 'required|in:vente,location',
            'id_departement'   => 'required|exists:departements,id_departement',
            'id_ville'         => 'required|exists:villes,id_ville',
            'id_quartier'      => 'nullable|exists:quartiers,id_quartier',
            'photos'           => 'nullable|array|max:5',
            'photos.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
            'prix'             => 'required_if:type_contrat,vente|nullable|numeric|min:0',
            'loyer'            => 'required_if:type_contrat,location|nullable|numeric|min:0',
            'nb_mois_avance'   => 'nullable|integer|min:0|max:24',
            'caution_eau'      => 'nullable|numeric|min:0',
            'caution_electricite' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['_token', '_method', 'photos', 'loyer']);
        // Normaliser le prix selon le type de contrat
        $data['prix'] = $request->type_contrat === 'location' ? $request->loyer : $request->prix;
        if ($request->type_contrat !== 'location') {
            $data['nb_mois_avance']    = null;
            $data['caution_eau']       = null;
            $data['caution_electricite'] = null;
        }

        // Nouvelles photos si envoyées
        if ($request->hasFile('photos')) {
            $photosPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('biens', 'public');
                $photosPaths[] = $path;
            }
            $data['photos'] = $photosPaths;
        }

        $bien->update($data);

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien modifié avec succès !');
    }

    public function supprimerBien($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        Bien::where('id_bien', $id)
            ->where('id_agence', session('admin')->id_agence)
            ->firstOrFail()->delete();

        return redirect()->route('admin.biens')
                        ->with('success', 'Bien supprimé avec succès !');
    }

    public function modifierStatutBien(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $bien = Bien::where('id_bien', $id)
                    ->where('id_agence', session('admin')->id_agence)
                    ->firstOrFail();

        $bien->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut modifié avec succès !');
    }

    // ═══ CONFIG PAIEMENT CINETPAY (admin principal uniquement) ═══
    public function configPaiement()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        if (!session('admin')->est_principal) {
            return redirect()->route('admin.dashboard')
                             ->with('error', 'Accès réservé à l\'administrateur principal.');
        }

        $agence = \App\Models\Agence::find(session('admin')->id_agence);
        return view('admin.paiement-config', compact('agence'));
    }

    public function sauvegarderConfigPaiement(Request $request)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        if (!session('admin')->est_principal) {
            return redirect()->route('admin.dashboard')
                             ->with('error', 'Accès réservé à l\'administrateur principal.');
        }

        $request->validate([
            'cinetpay_site_id' => 'required|string|max:100',
            'cinetpay_api_key' => 'required|string|max:255',
            'cinetpay_env'     => 'required|in:TEST,PROD',
        ]);

        \App\Models\Agence::where('id_agence', session('admin')->id_agence)
                          ->update([
                              'cinetpay_site_id' => $request->cinetpay_site_id,
                              'cinetpay_api_key' => $request->cinetpay_api_key,
                              'cinetpay_env'     => $request->cinetpay_env,
                          ]);

        return redirect()->route('admin.paiement.config')
                         ->with('success', '✅ Configuration CinetPay sauvegardée avec succès !');
    }

    // ═══ GESTION RESERVATIONS ═══
    public function reservations()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $query = \App\Models\Contrat::with(['bien', 'client', 'paiements', 'location', 'vente'])
                           ->whereHas('bien', function($q) {
                               $q->where('id_agence', session('admin')->id_agence);
                           });

        if (request('statut')) {
            $query->where('statut_contrat', request('statut'));
        }

        $contrats = $query->latest()->paginate(10);

        return view('admin.reservations', compact('contrats'));
    }

    public function confirmerReservation($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $contrat = \App\Models\Contrat::whereHas('bien', function($q) {
                        $q->where('id_agence', session('admin')->id_agence);
                    })->findOrFail($id);

        $contrat->update(['statut_contrat' => 'confirme']);

        // Mettre à jour le statut du bien
        $statut = $contrat->type_contrat === 'location' ? 'loue' : 'vendu';
        $contrat->bien->update(['statut' => $statut]);

        return back()->with('success', 'Réservation confirmée avec succès !');
    }

    public function annulerReservation($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $contrat = \App\Models\Contrat::whereHas('bien', function($q) {
                        $q->where('id_agence', session('admin')->id_agence);
                    })->findOrFail($id);

        $contrat->update(['statut_contrat' => 'annule']);
        $contrat->bien->update(['statut' => 'disponible']);

        return back()->with('success', 'Réservation annulée. Le bien est de nouveau disponible.');
    }

    // ═══ GESTION ADMINISTRATEURS ═══
    public function administrateurs()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $admins = Administrateur::where('id_agence', session('admin')->id_agence)->get();

        return view('admin.administrateurs', compact('admins'));
    }

    public function creerAdministrateur(Request $request)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        // Seul l'admin principal peut créer
        if (!session('admin')->est_principal) {
            return redirect()->route('admin.administrateurs')
                            ->with('error', 'Seul l\'admin principal peut créer des administrateurs');
        }

        $request->validate([
            'nom_admin'    => 'required|string',
            'prenom_admin' => 'required|string',
            'email'        => 'required|email|unique:administrateurs,email',
            'mot_de_passe' => 'required|min:6',
        ]);

        Administrateur::create([
            'id_agence'     => session('admin')->id_agence,
            'nom_admin'     => $request->nom_admin,
            'prenom_admin'  => $request->prenom_admin,
            'email'         => $request->email,
            'mot_de_passe'  => Hash::make($request->mot_de_passe),
            'est_principal' => false,
        ]);

        return redirect()->route('admin.administrateurs')
                        ->with('success', 'Administrateur créé avec succès !');
    }

    public function supprimerAdministrateur($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        // Seul l'admin principal peut supprimer
        if (!session('admin')->est_principal) {
            return redirect()->route('admin.administrateurs')
                            ->with('error', 'Seul l\'admin principal peut supprimer des administrateurs');
        }

        $admin = Administrateur::where('id_admin', $id)
                            ->where('id_agence', session('admin')->id_agence)
                            ->firstOrFail();

        // Ne pas supprimer l'admin principal
        if ($admin->est_principal) {
            return back()->with('error', 'Impossible de supprimer l\'admin principal');
        }

        $admin->delete();

        return back()->with('success', 'Administrateur supprimé');
    }
}