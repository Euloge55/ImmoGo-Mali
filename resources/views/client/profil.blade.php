@extends('layouts.app')
@section('title', 'Mon Profil')

@section('styles')
<style>
    .profile-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .avatar-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4ECDC4, #2C3E50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        margin: 0 auto;
    }
    .nav-pills .nav-link {
        border-radius: 10px;
        color: #2C3E50;
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background: #4ECDC4;
        color: white;
    }
    .tab-content .tab-pane { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row g-4">

        <!-- ═══ SIDEBAR PROFIL ═══ -->
        <div class="col-lg-3">
            <div class="card profile-card p-4 text-center mb-4">
                <div class="avatar-circle mb-3">
                    {{ strtoupper(substr($client->prenom_client, 0, 1)) }}{{ strtoupper(substr($client->nom_client, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">
                    {{ $client->prenom_client }} {{ $client->nom_client }}
                </h5>
                <p class="text-muted small mb-2">{{ $client->email }}</p>
                <span class="badge" style="background:#e8fffe; color:#4ECDC4;
                      border:1px solid #4ECDC4">
                    <i class="fas fa-user-check me-1"></i>Client vérifié
                </span>
                <hr class="my-3">
                <div class="text-start">
                    <p class="text-muted small mb-1">
                        <i class="fas fa-phone me-2" style="color:#4ECDC4"></i>
                        {{ $client->tel_client ?: 'Non renseigné' }}
                    </p>
                    <p class="text-muted small mb-1">
                        <i class="fas fa-calendar me-2" style="color:#4ECDC4"></i>
                        Membre depuis {{ $client->created_at->format('M Y') }}
                    </p>
                </div>
            </div>

            <!-- NAVIGATION -->
            <div class="card profile-card p-3">
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link active mb-1" href="#" data-tab="infos" onclick="showTab('infos', this)">
                        <i class="fas fa-user me-2"></i>Mes informations
                    </a>
                    <a class="nav-link mb-1" href="#" data-tab="password" onclick="showTab('password', this)">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </a>
                    <hr>
                    <a class="nav-link text-muted" href="{{ route('client.reservations') }}">
                        <i class="fas fa-calendar-check me-2"></i>Mes réservations
                    </a>
                    <a class="nav-link text-muted" href="{{ route('client.favoris') }}">
                        <i class="fas fa-heart me-2"></i>Mes favoris
                    </a>
                </nav>
            </div>
        </div>

        <!-- ═══ CONTENU ═══ -->
        <div class="col-lg-9">

            <!-- TAB INFOS -->
            <div class="card profile-card p-4 mb-4" id="tab-infos">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px; height:42px; background:#e8fffe">
                        <i class="fas fa-user" style="color:#4ECDC4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Mes informations personnelles</h5>
                        <p class="text-muted small mb-0">Modifiez vos données de profil</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-3 mb-4">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('client.profil.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nom_client"
                                   class="form-control @error('nom_client') is-invalid @enderror"
                                   value="{{ old('nom_client', $client->nom_client) }}"
                                   required>
                            @error('nom_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="prenom_client"
                                   class="form-control @error('prenom_client') is-invalid @enderror"
                                   value="{{ old('prenom_client', $client->prenom_client) }}"
                                   required>
                            @error('prenom_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $client->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <img src="https://flagcdn.com/w20/ml.png" alt="ML"
                                         style="width:20px; margin-right:5px">
                                    +223
                                </span>
                                <input type="text"
                                       name="tel_client"
                                       class="form-control @error('tel_client') is-invalid @enderror"
                                       value="{{ old('tel_client', $client->tel_client) }}"
                                       placeholder="XX XX XX XX">
                            </div>
                            @error('tel_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit"
                                class="btn fw-semibold px-4 py-2"
                                style="background:#4ECDC4; color:white; border-radius:10px">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB MOT DE PASSE -->
            <div class="card profile-card p-4" id="tab-password" style="display:none">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px; height:42px; background:#fff3cd">
                        <i class="fas fa-lock" style="color:#f39c12"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Changer le mot de passe</h5>
                        <p class="text-muted small mb-0">Utilisez un mot de passe fort de minimum 6 caractères</p>
                    </div>
                </div>

                <form action="{{ route('client.password.update') }}" method="POST">
                    @csrf @method('PATCH')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ancien mot de passe</label>
                            <div class="input-group">
                                <input type="password"
                                       name="ancien_mot_de_passe"
                                       id="oldPass"
                                       class="form-control"
                                       placeholder="Votre mot de passe actuel"
                                       required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass('oldPass', 'eyeOld')">
                                    <i class="fas fa-eye" id="eyeOld"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password"
                                       name="nouveau_mot_de_passe"
                                       id="newPass"
                                       class="form-control @error('nouveau_mot_de_passe') is-invalid @enderror"
                                       placeholder="Minimum 6 caractères"
                                       required
                                       oninput="checkStrength(this.value)">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass('newPass', 'eyeNew')">
                                    <i class="fas fa-eye" id="eyeNew"></i>
                                </button>
                                @error('nouveau_mot_de_passe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Indicateur de force -->
                            <div class="mt-2">
                                <div class="progress" style="height:4px; border-radius:4px">
                                    <div class="progress-bar" id="strengthBar"
                                         style="width:0%; background:#e74c3c; transition:all 0.3s"></div>
                                </div>
                                <small class="text-muted" id="strengthText"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <input type="password"
                                   name="nouveau_mot_de_passe_confirmation"
                                   class="form-control"
                                   placeholder="Répétez le nouveau mot de passe"
                                   required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                                class="btn fw-semibold px-4 py-2"
                                style="background:#f39c12; color:white; border-radius:10px">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Navigation tabs
function showTab(tab, link) {
    event.preventDefault();
    document.getElementById('tab-infos').style.display = tab === 'infos' ? 'block' : 'none';
    document.getElementById('tab-password').style.display = tab === 'password' ? 'block' : 'none';

    document.querySelectorAll('.nav-pills .nav-link[data-tab]').forEach(el => el.classList.remove('active'));
    link.classList.add('active');
}

// Toggle password visibility
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Indicateur de force du mot de passe
function checkStrength(password) {
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score = 0;

    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
        { pct: 0,   color: '#e74c3c', label: '' },
        { pct: 20,  color: '#e74c3c', label: 'Très faible' },
        { pct: 40,  color: '#f39c12', label: 'Faible' },
        { pct: 60,  color: '#f39c12', label: 'Moyen' },
        { pct: 80,  color: '#2ecc71', label: 'Fort' },
        { pct: 100, color: '#27ae60', label: 'Très fort' },
    ];

    bar.style.width  = levels[score].pct + '%';
    bar.style.background = levels[score].color;
    text.textContent = levels[score].label;
    text.style.color = levels[score].color;
}

// Ouvrir le bon tab si erreur de mot de passe
@if($errors->has('nouveau_mot_de_passe') || $errors->has('ancien_mot_de_passe'))
    document.addEventListener('DOMContentLoaded', function() {
        const link = document.querySelector('.nav-link[data-tab="password"]');
        showTab('password', link);
    });
@endif
</script>
@endsection
