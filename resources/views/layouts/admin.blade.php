<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        :root { --primary: #4ECDC4; --dark: #2C3E50; }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--dark);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-avatar {
            width: 50px; height: 50px;
            border-radius: 50%;
            background: rgba(78,205,196,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(78,205,196,0.15);
            color: var(--primary);
            border-left: 3px solid var(--primary);
        }
        .sidebar-menu a i { width: 25px; margin-right: 10px; font-size: 15px; }
        .sidebar-section-title {
            padding: 8px 20px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            font-weight: 600;
        }
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .content { padding: 30px; }
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 25px;
            color: white;
        }
        .stat-number { font-size: 2rem; font-weight: 800; }
        .badge-principal {
            background: rgba(78,205,196,0.2);
            color: #4ECDC4;
            border: 1px solid #4ECDC4;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .logout-btn {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #e74c3c !important;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
            background: none;
            border-top: none;
            border-right: none;
            border-bottom: none;
            width: 100%;
        }
        .logout-btn:hover {
            background: rgba(231,76,60,0.1);
            border-left: 3px solid #e74c3c;
        }
        .logout-btn i { width: 25px; margin-right: 10px; }
    </style>
    @yield('styles')
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<div class="sidebar">

    <!-- BRAND -->
    <div class="sidebar-brand">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-building fa-lg me-2" style="color:#4ECDC4"></i>
            <h5 class="text-white fw-bold mb-0">IMMO-Mali</h5>
        </div>
        <div class="d-flex align-items-center mt-3">
            <div class="sidebar-avatar me-3">
                <i class="fas fa-user" style="color:#4ECDC4"></i>
            </div>
            <div>
                @if(session('superadmin'))
                    <p class="text-white fw-semibold mb-0 small">
                        {{ session('superadmin')->nom_superadmin }}
                    </p>
                    <span class="badge-principal">
                        <i class="fas fa-crown me-1" style="font-size:8px"></i>
                        Super Admin
                    </span>
                @elseif(session('admin'))
                    <p class="text-white fw-semibold mb-0 small">
                        {{ session('admin')->prenom_admin }}
                        {{ session('admin')->nom_admin }}
                    </p>
                    @if(session('admin')->est_principal)
                        <span class="badge-principal">
                            <i class="fas fa-crown me-1" style="font-size:8px"></i>
                            Principal
                        </span>
                    @else
                        <span style="color:rgba(255,255,255,0.4); font-size:11px">
                            Assistant
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        @if(session('admin'))
            {{-- ═══ MENU ADMIN AGENCE ═══ --}}
            <div class="sidebar-section-title">Navigation</div>

            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <a href="{{ route('admin.biens') }}"
               class="{{ request()->routeIs('admin.biens') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Mes Biens
            </a>

            <a href="{{ route('admin.reservations') }}"
               class="{{ request()->routeIs('admin.reservations') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Réservations
            </a>

            @if(session('admin')->est_principal)
                <a href="{{ route('admin.administrateurs') }}"
                   class="{{ request()->routeIs('admin.administrateurs') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Administrateurs
                    <span class="ms-auto badge"
                          style="background:rgba(78,205,196,0.2);
                                 color:#4ECDC4; font-size:10px">
                        Gérer
                    </span>
                </a>
                <a href="{{ route('admin.paiement.config') }}"
                   class="{{ request()->routeIs('admin.paiement.config') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i> Config Paiement
                    <span class="ms-auto badge"
                          style="background:rgba(243,156,18,0.2);
                                 color:#f39c12; font-size:10px">
                        FedaPay
                    </span>
                </a>
            @endif

        @elseif(session('superadmin'))
            {{-- ═══ MENU SUPER ADMIN ═══ --}}
            <div class="sidebar-section-title">Navigation</div>

            <a href="{{ route('superadmin.dashboard') }}"
               class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <a href="{{ route('superadmin.agences') }}"
               class="{{ request()->routeIs('superadmin.agences') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Agences
            </a>

        @endif

        <hr style="border-color:rgba(255,255,255,0.1); margin:15px 20px">
        <div class="sidebar-section-title">Compte</div>

        <a href="{{ route('admin.profil') }}"
           class="{{ request()->routeIs('admin.profil') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Mon Profil
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </form>

    </div>
</div>

<!-- ═══ CONTENU PRINCIPAL ═══ -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="d-flex align-items-center">
            <h6 class="mb-0 fw-bold">@yield('title')</h6>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if(session('superadmin'))
                <span class="text-muted small">
                    <i class="fas fa-shield-alt me-1" style="color:#4ECDC4"></i>
                    {{ session('superadmin')->nom_superadmin }}
                    <span class="badge ms-1"
                          style="background:#4ECDC4; color:white; font-size:10px">
                        Super Admin
                    </span>
                </span>
            @elseif(session('admin'))
                <span class="text-muted small">
                    <i class="fas fa-building me-1" style="color:#4ECDC4"></i>
                    Agence #{{ session('admin')->id_agence }}
                </span>
                <span class="text-muted small">
                    <i class="fas fa-user-circle me-1" style="color:#4ECDC4"></i>
                    {{ session('admin')->prenom_admin }}
                    {{ session('admin')->nom_admin }}
                    @if(session('admin')->est_principal)
                        <span class="badge ms-1"
                              style="background:#4ECDC4; color:white; font-size:10px">
                            Principal
                        </span>
                    @endif
                </span>
            @endif
        </div>
    </div>

    <!-- MESSAGES -->
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show
                        border-0 rounded-3"
                 style="border-left: 4px solid #2ecc71 !important">
                <i class="fas fa-check-circle me-2" style="color:#2ecc71"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show
                        border-0 rounded-3"
                 style="border-left: 4px solid #e74c3c !important">
                <i class="fas fa-exclamation-circle me-2" style="color:#e74c3c"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning alert-dismissible fade show
                        border-0 rounded-3"
                 style="border-left: 4px solid #f39c12 !important">
                <i class="fas fa-exclamation-triangle me-2" style="color:#f39c12"></i>
                <strong>Erreurs :</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>

