<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Administrateur;
use App\Models\Client;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminWebController extends Controller
{
    private function checkAuth()
    {
        if (!session('superadmin')) {
            return redirect()->route('login')
                           ->with('error', 'Accès réservé au super administrateur');
        }
        return null;
    }

    // ═══ DASHBOARD ═══
    public function dashboard()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $totalAgences  = Agence::count();
        $totalClients  = Client::count();
        $totalBiens    = Bien::count();
        $totalContrats = Contrat::count();

        $dernieresAgences = Agence::with('administrateurs')
                                ->latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'totalAgences',
            'totalClients',
            'totalBiens',
            'totalContrats',
            'dernieresAgences'
        ));
    }
    // ═══ GESTION AGENCES ═══
    public function agences()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $agences = Agence::with('administrateurs')->latest()->paginate(10);

        return view('superadmin.agences', compact('agences'));
    }

    public function creerAgence(Request $request)
    {
    if ($redirect = $this->checkAuth()) return $redirect;

    $request->validate([
        'nom_agence'     => 'required|string',
        'adresse_agence' => 'required|string',
        'tel_agence'     => 'required|string',
        'email'          => 'required|email|unique:agences,email',
        'logo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        // Admin principal
        'nom_admin'      => 'required|string',
        'prenom_admin'   => 'required|string',
        'email_admin'    => 'required|email|unique:administrateurs,email',
        'mot_de_passe'   => 'required|min:6|confirmed',
    ]);

    // Upload logo
    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
    }

    // Créer l'agence
    $agence = Agence::create([
        'id_superadmin'  => session('superadmin')->id_superadmin,
        'nom_agence'     => $request->nom_agence,
        'adresse_agence' => $request->adresse_agence,
        'tel_agence'     => $request->tel_agence,
        'email'          => $request->email,
        'logo'           => $logoPath,
    ]);

    // Créer automatiquement l'admin principal
    Administrateur::create([
        'id_agence'     => $agence->id_agence,
        'nom_admin'     => $request->nom_admin,
        'prenom_admin'  => $request->prenom_admin,
        'email'         => $request->email_admin,
        'mot_de_passe'  => Hash::make($request->mot_de_passe),
        'est_principal' => true,
    ]);

    return redirect()->route('superadmin.agences')
                    ->with('success',
                           'Agence et administrateur principal créés avec succès !');
    }
    public function modifierAgence(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $agence = Agence::findOrFail($id);

        $request->validate([
            'nom_agence'     => 'required|string',
            'adresse_agence' => 'required|string',
            'tel_agence'     => 'required|string',
            'email'          => 'required|email|unique:agences,email,' .
                                $id . ',id_agence',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'logo']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $agence->update($data);

        return redirect()->route('superadmin.agences')
                        ->with('success', 'Agence modifiée avec succès !');
    }

    public function supprimerAgence($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        Agence::findOrFail($id)->delete();

        return back()->with('success', 'Agence supprimée avec succès !');
    }

    // ═══ GESTION CLIENTS ═══
    public function clients()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $search  = request('q');
        $clients = \App\Models\Client::when($search, function($q) use ($search) {
                        $q->where('nom_client', 'like', "%$search%")
                          ->orWhere('prenom_client', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                    })->withCount('contrats')->latest()->paginate(15);

        return view('superadmin.clients', compact('clients'));
    }

    public function supprimerClient($id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        \App\Models\Client::findOrFail($id)->delete();

        return back()->with('success', 'Client supprimé avec succès !');
    }

    // ═══ VUE GLOBALE CONTRATS ═══
    public function contrats()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $contrats = \App\Models\Contrat::with([
                        'bien.agence', 'bien.ville', 'client',
                        'paiements', 'location', 'vente'
                    ])
                    ->when(request('statut'), fn($q) => $q->where('statut_contrat', request('statut')))
                    ->when(request('id_agence'), fn($q) => $q->whereHas('bien', fn($b) => $b->where('id_agence', request('id_agence'))))
                    ->latest()->paginate(20);

        $agences = \App\Models\Agence::orderBy('nom_agence')->get();

        return view('superadmin.contrats', compact('contrats', 'agences'));
    }
}