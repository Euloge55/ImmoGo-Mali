<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Administrateur;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthWebController extends Controller
{
    // ═══ INSCRIPTION CLIENT ═══
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom_client'    => 'required|string|max:100',
            'prenom_client' => 'required|string|max:100',
            'email'         => 'required|email|unique:clients,email',
            'tel_client'    => 'required|string|max:20',
            'mot_de_passe'  => 'required|min:6|confirmed',
        ]);

        $client = Client::create([
            'nom_client'    => $request->nom_client,
            'prenom_client' => $request->prenom_client,
            'email'         => strtolower($request->email),
            'tel_client'    => $request->tel_client,
            'mot_de_passe'  => Hash::make($request->mot_de_passe),
        ]);

        Session::put('client', $client);
        Session::put('role', 'client');

        return redirect()->route('home')
                         ->with('success', 'Compte créé avec succès !');
    }

    // ═══ CONNEXION UNIQUE ═══
    // Un seul formulaire pour tous les rôles.
    // Ordre de recherche : SuperAdmin → Administrateur → Client
    public function showLogin()
    {
        // Si déjà connecté, rediriger vers le bon dashboard
        if (Session::get('role') === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }
        if (Session::get('role') === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (Session::get('role') === 'client') {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'mot_de_passe' => 'required',
        ]);

        $email    = strtolower(trim($request->email));
        $password = $request->mot_de_passe;

        // ── 1. Super Admin ──
        $superAdmin = SuperAdmin::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($superAdmin && Hash::check($password, $superAdmin->mot_de_passe)) {
            Session::flush();
            Session::regenerate();
            Session::put('superadmin', $superAdmin);
            Session::put('role', 'superadmin');
            return redirect()->route('superadmin.dashboard')
                             ->with('success', 'Bienvenue ' . $superAdmin->nom_superadmin . ' !');
        }

        // ── 2. Administrateur d'agence ──
        $admin = Administrateur::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($admin && Hash::check($password, $admin->mot_de_passe)) {
            Session::flush();
            Session::regenerate();
            Session::put('admin', $admin);
            Session::put('role', 'admin');
            return redirect()->route('admin.dashboard')
                             ->with('success', 'Bienvenue ' . $admin->prenom_admin . ' !');
        }

        // ── 3. Client ──
        $client = Client::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($client && Hash::check($password, $client->mot_de_passe)) {
            Session::flush();
            Session::regenerate();
            Session::put('client', $client);
            Session::put('role', 'client');
            return redirect()->route('home')
                             ->with('success', 'Bienvenue ' . $client->prenom_client . ' !');
        }

        // ── Aucun match ──
        return back()
            ->withErrors(['email' => 'Email ou mot de passe incorrect.'])
            ->withInput(['email' => $request->email]);
    }

    // ═══ DECONNEXION ═══
    public function logout()
    {
        Session::flush();
        Session::regenerate();
        return redirect()->route('login')
                         ->with('success', 'Vous êtes déconnecté.');
    }

    // ─────────────────────────────────────────────
    // Anciennes routes conservées pour compatibilité
    // (redirigent toutes vers /connexion)
    // ─────────────────────────────────────────────
    public function showLoginAdmin()
    {
        return redirect()->route('login');
    }

    public function loginAdmin(Request $request)
    {
        return $this->login($request);
    }

    public function showLoginSuperAdmin()
    {
        return redirect()->route('login');
    }

    public function loginSuperAdmin(Request $request)
    {
        return $this->login($request);
    }
}
