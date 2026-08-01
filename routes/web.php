<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\BienWebController;
use App\Http\Controllers\Web\ClientWebController;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\SuperAdminWebController;
use App\Http\Controllers\Web\CinetPayController;

// ═══════════════════════════════════
// AUTHENTIFICATION
// ═══════════════════════════════════
Route::get('/inscription', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/inscription', [AuthWebController::class, 'register']);

Route::get('/connexion', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/connexion', [AuthWebController::class, 'login']);

Route::get('/admin/connexion', [AuthWebController::class, 'showLoginAdmin'])->name('login.admin');
Route::post('/admin/connexion', [AuthWebController::class, 'loginAdmin']);

Route::get('/superadmin/connexion', [AuthWebController::class, 'showLoginSuperAdmin'])->name('login.superadmin');
Route::post('/superadmin/connexion', [AuthWebController::class, 'loginSuperAdmin']);

Route::post('/deconnexion', [AuthWebController::class, 'logout'])->name('logout');

// ═══════════════════════════════════
// PAGES PUBLIQUES
// ═══════════════════════════════════
Route::get('/', [BienWebController::class, 'home'])->name('home');
Route::get('/biens', [BienWebController::class, 'index'])->name('biens.index');
Route::get('/biens/{id}', [BienWebController::class, 'show'])->name('biens.show');

// ═══════════════════════════════════
// CLIENT
// ═══════════════════════════════════
Route::get('/profil', [ClientWebController::class, 'profil'])->name('client.profil');
Route::put('/profil', [ClientWebController::class, 'updateProfil'])->name('client.profil.update');
Route::patch('/profil/mot-de-passe', [ClientWebController::class, 'updateMotDePasse'])->name('client.password.update');

Route::get('/mes-reservations', [ClientWebController::class, 'reservations'])->name('client.reservations');
Route::post('/reserver', [ClientWebController::class, 'reserver'])->name('client.reserver');
Route::post('/payer', [ClientWebController::class, 'payer'])->name('client.payer');
Route::post('/payer-total', [ClientWebController::class, 'payerTotal'])->name('client.payer.total');

Route::get('/mes-favoris', [ClientWebController::class, 'favoris'])->name('client.favoris');
Route::post('/favoris', [ClientWebController::class, 'ajouterFavori'])->name('client.favoris.ajouter');
Route::delete('/favoris/{id}', [ClientWebController::class, 'supprimerFavori'])->name('client.favoris.supprimer');

// ═══════════════════════════════════
// ADMIN AGENCE
// ═══════════════════════════════════
Route::get('/admin/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');

Route::get('/admin/biens', [AdminWebController::class, 'biens'])->name('admin.biens');
Route::post('/admin/biens', [AdminWebController::class, 'creerBien'])->name('admin.biens.creer');
Route::put('/admin/biens/{id}', [AdminWebController::class, 'modifierBien'])->name('admin.biens.modifier');
Route::delete('/admin/biens/{id}', [AdminWebController::class, 'supprimerBien'])->name('admin.biens.supprimer');
Route::patch('/admin/biens/{id}/statut', [AdminWebController::class, 'modifierStatutBien'])->name('admin.biens.statut');

Route::get('/admin/reservations', [AdminWebController::class, 'reservations'])->name('admin.reservations');
Route::patch('/admin/reservations/{id}/confirmer', [AdminWebController::class, 'confirmerReservation'])->name('admin.reservations.confirmer');
Route::patch('/admin/reservations/{id}/annuler', [AdminWebController::class, 'annulerReservation'])->name('admin.reservations.annuler');

Route::get('/admin/administrateurs', [AdminWebController::class, 'administrateurs'])->name('admin.administrateurs');
Route::post('/admin/administrateurs', [AdminWebController::class, 'creerAdministrateur'])->name('admin.administrateurs.creer');
Route::delete('/admin/administrateurs/{id}', [AdminWebController::class, 'supprimerAdministrateur'])->name('admin.administrateurs.supprimer');

// Config paiement CinetPay — admin principal uniquement
Route::get('/admin/paiement/config', [AdminWebController::class, 'configPaiement'])->name('admin.paiement.config');
Route::post('/admin/paiement/config', [AdminWebController::class, 'sauvegarderConfigPaiement'])->name('admin.paiement.config.save');

// Profil admin
Route::get('/admin/profil', [AdminWebController::class, 'profil'])->name('admin.profil');
Route::put('/admin/profil', [AdminWebController::class, 'updateProfil'])->name('admin.profil.update');
Route::patch('/admin/profil/mot-de-passe', [AdminWebController::class, 'updateMotDePasse'])->name('admin.password.update');

// ═══════════════════════════════════
// SUPER ADMIN
// ═══════════════════════════════════
Route::get('/superadmin/dashboard', [SuperAdminWebController::class, 'dashboard'])->name('superadmin.dashboard');

Route::get('/superadmin/agences', [SuperAdminWebController::class, 'agences'])->name('superadmin.agences');
Route::post('/superadmin/agences', [SuperAdminWebController::class, 'creerAgence'])->name('superadmin.agences.creer');
Route::put('/superadmin/agences/{id}', [SuperAdminWebController::class, 'modifierAgence'])->name('superadmin.agences.modifier');
Route::delete('/superadmin/agences/{id}', [SuperAdminWebController::class, 'supprimerAgence'])->name('superadmin.agences.supprimer');

Route::get('/superadmin/clients', [SuperAdminWebController::class, 'clients'])->name('superadmin.clients');
Route::delete('/superadmin/clients/{id}', [SuperAdminWebController::class, 'supprimerClient'])->name('superadmin.clients.supprimer');
Route::get('/superadmin/contrats', [SuperAdminWebController::class, 'contrats'])->name('superadmin.contrats');

// ═══════════════════════════════════
// CINETPAY — Paiement mobile money Mali
// ═══════════════════════════════════
Route::post('/paiement/acompte', [CinetPayController::class, 'payerAcompte'])->name('cinetpay.acompte');
Route::post('/paiement/total',   [CinetPayController::class, 'payerTotal'])->name('cinetpay.total');
Route::post('/paiement/solde',   [CinetPayController::class, 'payerSolde'])->name('cinetpay.solde');
Route::get('/paiement/callback', [CinetPayController::class, 'callback'])->name('cinetpay.callback');
Route::post('/paiement/notify',  [CinetPayController::class, 'notify'])->name('cinetpay.notify');
// Endpoint AJAX qui prépare la transaction et retourne site_id + transaction_id au SDK JS
Route::post('/paiement/init',    [CinetPayController::class, 'initAjax'])->name('cinetpay.init');
Route::post('/paiement/init-solde', [CinetPayController::class, 'initAjaxSolde'])->name('cinetpay.init.solde');
