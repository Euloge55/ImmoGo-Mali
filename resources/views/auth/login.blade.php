<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMMO-Mali — Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #2C3E50 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 45px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4ECDC4, #2C3E50);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1.5px solid #dee2e6;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 3px rgba(78,205,196,0.15);
        }
        .btn-login {
            background: #4ECDC4;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover { background: #3dbdb4; }
        .btn-login:disabled { background: #a0e0dc; cursor: not-allowed; }
        .divider { border-color: #dee2e6; margin: 25px 0; }
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">

    <!-- LOGO -->
    <div class="logo-circle">
        <i class="fas fa-building fa-2x text-white"></i>
    </div>
    <h4 class="text-center fw-bold mb-1">IMMO-Mali</h4>
    <p class="text-center text-muted small mb-4">Connectez-vous à votre espace</p>

    {{-- Messages serveur --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $errors->first('email') ?: $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- FORMULAIRE --}}
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold small">Adresse email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   placeholder="votre@email.com"
                   autocomplete="username"
                   required
                   autofocus>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Mot de passe</label>
            <div class="input-group">
                <input type="password"
                       name="mot_de_passe"
                       id="pwdInput"
                       class="form-control"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
                <button type="button"
                        class="btn btn-outline-secondary"
                        style="border-radius:0 10px 10px 0"
                        onclick="togglePwd()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login" id="submitBtn">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
        </button>
    </form>

    <hr class="divider">

    {{-- Lien inscription --}}
    <div class="text-center">
        <p class="text-muted small mb-2">Pas encore de compte ?</p>
        <a href="{{ route('register') }}"
           class="btn btn-outline-secondary w-100"
           style="border-radius:10px; font-weight:600">
            <i class="fas fa-user-plus me-2"></i>Créer un compte client
        </a>
    </div>

    {{-- Rôles info --}}
    <div class="text-center mt-4">
        <p class="text-muted" style="font-size:11px">
            Ce formulaire est utilisé par tous les rôles :<br>
            <span class="role-badge me-1" style="background:#e8fffe; color:#4ECDC4">
                <i class="fas fa-crown me-1"></i>Super Admin
            </span>
            <span class="role-badge me-1" style="background:#e8f4fd; color:#3498db">
                <i class="fas fa-building me-1"></i>Admin Agence
            </span>
            <span class="role-badge" style="background:#f0f0f0; color:#555">
                <i class="fas fa-user me-1"></i>Client
            </span>
        </p>
    </div>

</div>

<script>
// Afficher/masquer mot de passe
function togglePwd() {
    const input = document.getElementById('pwdInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Désactiver le bouton pendant la soumission pour éviter les doubles clics
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';
});
</script>

</body>
</html>
