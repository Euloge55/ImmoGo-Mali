<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMMO-Mali - @yield('title', 'Gestion Immobilière')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        :root {
            --primary: #4ECDC4;
            --secondary: #2C3E50;
        }
        .navbar {
            background-color: rgba(44, 62, 80, 0.95) !important;
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
        }
        body { padding-top: 70px; }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
    </style>

    @yield('styles')
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">
                <i class="fas fa-building me-2" style="color:#4ECDC4"></i>IMMO-Mali
            </a>
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">

                <!-- LIENS GAUCHE -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('biens.index') }}">
                            <i class="fas fa-building me-1"></i>Biens
                        </a>
                    </li>
                </ul>

                <!-- LIENS DROITE -->
                <ul class="navbar-nav">
                    @if(session('client'))
                        {{-- CLIENT CONNECTÉ --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#" data-bs-toggle="dropdown">
                                <div class="rounded-circle d-flex align-items-center
                                            justify-content-center me-2"
                                     style="width:32px; height:32px;
                                            background:#4ECDC4; color:white;
                                            font-size:13px; font-weight:600">
                                    {{ strtoupper(substr(session('client')->prenom_client, 0, 1)) }}
                                </div>
                                {{ session('client')->prenom_client }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('client.profil') }}">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('client.reservations') }}">
                                        <i class="fas fa-calendar me-2"></i>
                                        Mes Réservations
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('client.favoris') }}">
                                        <i class="fas fa-heart me-2"></i>
                                        Mes Favoris
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        {{-- VISITEUR --}}
                        <li class="nav-item me-2">
                            <a href="{{ route('login') }}"
                               class="btn btn-outline-light fw-semibold px-4"
                               style="border-radius:25px">
                                Se connecter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}"
                               class="btn fw-semibold px-4"
                               style="background:#4ECDC4; color:white;
                                      border-radius:25px">
                                S'inscrire →
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- MESSAGES -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- CONTENU -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="mt-5 py-4" style="background-color:#2C3E50; color:white;">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-building me-2" style="color:#4ECDC4"></i>
                <strong>IMMO-Mali</strong> — Application de Gestion Immobilière
            </p>
            <small class="text-muted">© 2026 Tous droits réservés</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    <!-- MODAL INSCRIPTION RAPIDE -->
    <div class="modal fade" id="modalInscriptionRapide" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-0"
                    style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                            border-radius:16px 16px 0 0">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-heart me-2"></i>
                        Créez votre compte pour continuer
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom_client"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom_client"
                                    class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email"
                                    class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" name="tel_client"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Mot de passe
                                </label>
                                <input type="password" name="mot_de_passe"
                                    class="form-control" required
                                    placeholder="Minimum 6 caractères">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer</label>
                                <input type="password"
                                    name="mot_de_passe_confirmation"
                                    class="form-control" required>
                            </div>
                        </div>

                        <button type="submit"
                                class="btn w-100 py-3 fw-semibold mt-4"
                                style="background:#4ECDC4; color:white;
                                    border-radius:12px">
                            <i class="fas fa-user-plus me-2"></i>
                            Créer mon compte
                        </button>

                        <hr class="my-3">

                        <p class="text-center text-muted mb-0">
                            Déjà un compte ?
                            <a href="{{ route('login') }}"
                            class="fw-semibold text-decoration-none"
                            style="color:#4ECDC4">
                                Se connecter
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
