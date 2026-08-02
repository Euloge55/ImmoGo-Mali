@extends('layouts.app')
@section('title', 'Connexion Super Admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <i class="fas fa-crown fa-3x mb-3" style="color:#4ECDC4"></i>
                        <h2 class="fw-bold">Super Administrateur</h2>
                        <p class="text-muted">Accès plateforme globale</p>
                    </div>

                    {{-- Erreurs serveur --}}
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Message d'erreur JS --}}
                    <div id="jsError" class="alert alert-danger border-0 rounded-3 d-none"></div>

                    <form id="loginForm" action="{{ route('login.superadmin') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="superadmin@IMMO-Mali.com"
                                   autocomplete="email"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <input type="password"
                                       name="mot_de_passe"
                                       id="password"
                                       class="form-control"
                                       placeholder="Votre mot de passe"
                                       autocomplete="current-password"
                                       required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd()">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                id="submitBtn"
                                class="btn w-100 py-2 fw-semibold"
                                style="background:#4ECDC4; color:white; border-radius:10px">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login.admin') }}" class="text-decoration-none text-muted small me-3">
                            <i class="fas fa-user-shield me-1"></i>Admin Agence
                        </a>
                        <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                            <i class="fas fa-user me-1"></i>Client
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Soumission avec token CSRF toujours frais
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn     = document.getElementById('submitBtn');
    const errDiv  = document.getElementById('jsError');
    const email   = document.getElementById('email').value.trim();
    const pwd     = document.getElementById('password').value;

    if (!email || !pwd) {
        showError('Veuillez remplir tous les champs.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';
    errDiv.classList.add('d-none');

    try {
        // 1. Récupérer un token CSRF frais
        const pageResp = await fetch('/superadmin/connexion', { credentials: 'include' });
        const html     = await pageResp.text();
        const match    = html.match(/name="_token"\s+value="([^"]+)"/);

        if (!match) {
            showError('Erreur CSRF. Rechargez la page.');
            reset(); return;
        }

        // 2. Soumettre avec le token frais
        const form = new FormData();
        form.append('_token', match[1]);
        form.append('email', email);
        form.append('mot_de_passe', pwd);

        const resp = await fetch('/superadmin/connexion', {
            method: 'POST',
            body: form,
            credentials: 'include',
            redirect: 'follow'
        });

        const finalUrl = resp.url;

        if (finalUrl.includes('/superadmin/dashboard')) {
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Connecté !';
            btn.style.background = '#2ecc71';
            setTimeout(() => window.location.href = '/superadmin/dashboard', 300);
        } else {
            // Lire le contenu pour extraire l'erreur
            const body = await resp.text();
            const errMatch = body.match(/alert-danger[^>]*>[\s\S]*?<\/div>/);
            showError('Email ou mot de passe incorrect.');
            reset();
        }
    } catch(err) {
        showError('Erreur réseau. Vérifiez que le serveur tourne.');
        reset();
    }
});

function showError(msg) {
    const d = document.getElementById('jsError');
    d.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + msg;
    d.classList.remove('d-none');
}

function reset() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Se connecter';
}

function togglePwd() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endsection
