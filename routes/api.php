<?php
/**
 * api.php — Routes API ImmoGo (version corrigée mobile)
 *
 * Corrections appliquées :
 * - Pagination sur GET /biens et GET /recherche
 * - Route GET /me pour le profil du client connecté
 * - Suppression de la redondance /biens/localisation (fusionné dans /recherche)
 * - Route DELETE /favoris/bien/{id_bien} pour supprimer par id_bien
 * - Sécurisation : id_client retiré du body (utilise auth()->id() côté contrôleur)
 * - Filtre nombre_pieces ajouté sur /biens et /recherche
 * - Filtre type_transaction (location/vente) pour les onglets mobiles
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SuperAdminController;
use App\Http\Controllers\API\AgenceController;
use App\Http\Controllers\API\BienController;
use App\Http\Controllers\API\TypeBienController;
use App\Http\Controllers\API\ContratController;
use App\Http\Controllers\API\PaiementController;
use App\Http\Controllers\API\FavorisController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\AdministrateurController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\VenteController;
use App\Http\Controllers\API\ModePaiementController;
use App\Http\Controllers\API\LocalisationController;


// ROUTES PUBLIQUES (sans token)


// ── LOCALISATION (cascades dépendantes) ──
Route::get('/departements',                 [LocalisationController::class, 'departements']);
Route::get('/departements/{id}/villes',     [LocalisationController::class, 'villes']);
Route::get('/villes/{id}/quartiers',        [LocalisationController::class, 'quartiers']);

// ── RECHERCHE UNIFIÉE (remplace /biens/localisation) ──
// Paramètres : q, id_departement, id_ville, id_quartier, id_typebien,
//              prix_min, prix_max, nombre_pieces, type_transaction,
//              statut, tri_prix (asc|desc), page, per_page
Route::get('/recherche',                    [LocalisationController::class, 'recherche']);

// ── AUTH ──
Route::post('/register/client',             [AuthController::class, 'registerClient']);
Route::post('/login/client',                [AuthController::class, 'loginClient']);
Route::post('/login/admin',                 [AuthController::class, 'loginAdmin']);
Route::post('/login/superadmin',            [AuthController::class, 'loginSuperAdmin']);

// ── BIENS PUBLICS ──
// Paramètres : statut, id_typebien, prix_max, nombre_pieces, type_transaction, page, per_page
Route::get('/biens',                        [BienController::class, 'index']);
Route::get('/biens/{id}',                   [BienController::class, 'show']);

// ── TYPES DE BIENS ──
Route::get('/type-biens',                   [TypeBienController::class, 'index']);

// ── MODES DE PAIEMENT (public pour affichage) ──
Route::get('/mode-paiements',               [ModePaiementController::class, 'index']);


// ROUTES PROTÉGÉES (token Sanctum requis)

Route::middleware('auth:sanctum')->group(function () {

    // ── AUTH ──
    Route::post('/logout',                  [AuthController::class, 'logout']);

    // ── PROFIL CLIENT CONNECTÉ ──
    // Retourne le client authentifié sans exposer l'id dans l'URL
    Route::get('/me',                       [ClientController::class, 'me']);
    Route::put('/me',                       [ClientController::class, 'updateMe']);
    Route::patch('/me/mot-de-passe',        [ClientController::class, 'modifierMotDePasseMe']);

    // ── FAVORIS (sécurisé : id_client depuis auth()) ──
    Route::post('/favoris',                 [FavorisController::class, 'store']);
    Route::get('/favoris',                  [FavorisController::class, 'index']);
    Route::delete('/favoris/{id}',          [FavorisController::class, 'destroy']);
    // Supprimer par id_bien (plus pratique côté mobile)
    Route::delete('/favoris/bien/{id_bien}',[FavorisController::class, 'destroyByBien']);

    // ── CONTRATS (id_client depuis auth()) ──
    Route::post('/contrats',                        [ContratController::class, 'store']);
    Route::get('/contrats',                         [ContratController::class, 'contratClient']);
    Route::get('/contrats/{id}/solde',              [ContratController::class, 'calculerSolde']);

    // ── PAIEMENTS ──
    Route::post('/paiements',                       [PaiementController::class, 'store']);
    Route::get('/paiements/contrat/{id_contrat}',   [PaiementController::class, 'historique']);

    // ── LOCATIONS DU CLIENT CONNECTÉ ──
    Route::get('/locations',                        [LocationController::class, 'locationClient']);
    Route::get('/locations/{id}',                   [LocationController::class, 'show']);

    // ── VENTES DU CLIENT CONNECTÉ ──
    Route::get('/ventes',                           [VenteController::class, 'venteClient']);
    Route::get('/ventes/{id}',                      [VenteController::class, 'show']);

    // ── SUPER ADMIN ──
    Route::prefix('superadmin')->group(function () {
        Route::post('/agences',             [SuperAdminController::class, 'creerAgence']);
        Route::get('/agences',              [SuperAdminController::class, 'listeAgences']);
        Route::put('/agences/{id}',         [SuperAdminController::class, 'modifierAgence']);
        Route::delete('/agences/{id}',      [SuperAdminController::class, 'supprimerAgence']);
        Route::get('/clients',              [ClientController::class, 'index']);
    });

    // ── AGENCE / ADMIN ──
    Route::prefix('agence')->group(function () {
        Route::post('/administrateurs',                 [AgenceController::class, 'creerAdministrateur']);
        Route::get('/administrateurs/{id_agence}',      [AgenceController::class, 'listeAdministrateurs']);
        Route::put('/administrateurs/{id}',             [AgenceController::class, 'modifierAdministrateur']);
        Route::delete('/administrateurs/{id}',          [AgenceController::class, 'supprimerAdministrateur']);
    });

    // ── BIENS (admin) ──
    Route::post('/biens',                   [BienController::class, 'store']);
    Route::put('/biens/{id}',               [BienController::class, 'update']);
    Route::patch('/biens/{id}/statut',      [BienController::class, 'modifierStatut']);
    Route::delete('/biens/{id}',            [BienController::class, 'destroy']);

    // ── TYPE BIENS (admin) ──
    Route::post('/type-biens',              [TypeBienController::class, 'store']);
    Route::delete('/type-biens/{id}',       [TypeBienController::class, 'destroy']);

    // ── CONTRATS AGENCE (admin) ──
    Route::get('/contrats/agence/{id_agence}',      [ContratController::class, 'contratAgence']);

    // ── LOCATIONS AGENCE (admin) ──
    Route::get('/locations/agence/{id_agence}',     [LocationController::class, 'locationAgence']);
    Route::put('/locations/{id}',                   [LocationController::class, 'update']);

    // ── VENTES AGENCE (admin) ──
    Route::get('/ventes/agence/{id_agence}',        [VenteController::class, 'venteAgence']);
    Route::put('/ventes/{id}',                      [VenteController::class, 'update']);

    // ── ADMINISTRATEUR ──
    Route::get('/administrateurs/{id}',             [AdministrateurController::class, 'show']);
    Route::put('/administrateurs/{id}',             [AdministrateurController::class, 'update']);
    Route::patch('/administrateurs/{id}/mot-de-passe', [AdministrateurController::class, 'modifierMotDePasse']);
    Route::get('/administrateurs/{id}/biens',       [AdministrateurController::class, 'biens']);

    // ── MODE PAIEMENT (admin) ──
    Route::post('/mode-paiements',          [ModePaiementController::class, 'store']);
    Route::put('/mode-paiements/{id}',      [ModePaiementController::class, 'update']);
    Route::delete('/mode-paiements/{id}',   [ModePaiementController::class, 'destroy']);

    // ── CLIENT (admin) ──
    Route::get('/clients/{id}',             [ClientController::class, 'show']);
    Route::put('/clients/{id}',             [ClientController::class, 'update']);
    Route::delete('/clients/{id}',          [ClientController::class, 'destroy']);
    Route::patch('/clients/{id}/mot-de-passe', [ClientController::class, 'modifierMotDePasse']);
});
