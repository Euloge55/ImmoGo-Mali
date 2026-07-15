@extends('layouts.app')
@section('title', 'Accueil')

@section('styles')
<style>
    /* ═══ NAVBAR ═══ */
    .navbar {
        background-color: rgba(44, 62, 80, 0.85) !important;
        backdrop-filter: blur(10px);
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 1000;
    }

    /* ═══ HERO SLIDER ═══ */
    .hero-section {
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        padding-top: 70px;
    }

    .hero-slide {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-size: cover;
        background-position: center;
        will-change: transform;
        transform: translateX(100%);
        transition: transform 1.2s ease-in-out;
    }

    .hero-slide.active {
        transform: translateX(0) !important;
    }

    .hero-slide::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(44,62,80,0.85) 0%,
            rgba(52,152,219,0.55) 100%
        );
    }

    .hero-slide-1 {
        background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1600');
    }
    .hero-slide-2 {
        background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1600');
    }
    .hero-slide-3 {
        background-image: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=1600');
    }

    /* DOTS */
    .slider-dots {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        display: flex;
        gap: 10px;
    }
    .dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background 0.3s;
        border: none;
    }
    .dot.active { background: #4ECDC4; }

    /* ═══ HERO CONTENT ═══ */
    .hero-content {
        position: relative;
        z-index: 10;
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        padding: 60px 0;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* ═══ SEARCH BOX ═══ */
    .search-box {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    /* ═══ STATS ═══ */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #4ECDC4;
    }

    /* ═══ BIEN CARDS ═══ */
    .bien-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .bien-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    .bien-image {
        height: 220px;
        background: linear-gradient(135deg, #4ECDC4, #2C3E50);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .badge-disponible { background-color: #2ecc71; }
    .badge-reserve    { background-color: #e67e22; }
    .badge-loue       { background-color: #3498db; }
    .badge-vendu      { background-color: #e74c3c; }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        position: relative;
        padding-bottom: 15px;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 60px; height: 4px;
        background: #4ECDC4;
        border-radius: 2px;
    }

    /* Empêcher le body de scroller sous la navbar fixe */
    body { padding-top: 0 !important; }
</style>
@endsection
@section('content')

<!-- ═══ HERO SECTION AVEC SLIDER ═══ -->
<section class="hero-section">

    <!-- SLIDES -->
    <div class="hero-slide hero-slide-1 active"></div>
    <div class="hero-slide hero-slide-2"></div>
    <div class="hero-slide hero-slide-3"></div>

    <!-- DOTS -->
    <div class="slider-dots">
        <div class="dot active" onclick="goToSlide(0)"></div>
        <div class="dot" onclick="goToSlide(1)"></div>
        <div class="dot" onclick="goToSlide(2)"></div>
    </div>

    <!-- CONTENU HERO -->
    <div class="container hero-content">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="hero-title text-white mb-4">
                    Trouvez le bien
                    <span style="color:#4ECDC4">immobilier</span>
                    de vos rêves
                </h1>
                <p class="fs-5 mb-4 text-white opacity-75">
                    Découvrez des maisons, appartements et terrains
                    disponibles partout au Mali.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('biens.index') }}"
                       class="btn btn-lg px-4 py-3 fw-semibold"
                       style="background:#4ECDC4; color:white; border-radius:12px">
                        <i class="fas fa-search me-2"></i>Voir les biens
                    </a>
                    @if(!session('client'))
                    <a href="{{ route('register') }}"
                       class="btn btn-lg btn-outline-light px-4 py-3 fw-semibold"
                       style="border-radius:12px">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                    </a>
                    @endif
                </div>
            </div>

            <!-- BOITE DE RECHERCHE -->
            <div class="col-lg-6">
                <div class="search-box">
                    <h5 class="fw-bold mb-4 text-dark">
                        <i class="fas fa-search me-2" style="color:#4ECDC4"></i>
                        Recherche rapide
                    </h5>
                    <form action="{{ route('biens.index') }}" method="GET"
                          id="searchForm">

                        <!-- RÉGION -->
                        <div class="mb-3">
                            <select name="id_departement"
                                    class="form-select"
                                    id="depSearch"
                                    onchange="loadVillesSearch(this.value)">
                                <option value="">Toutes les régions</option>
                                @foreach($departements as $dep)
                                    <option value="{{ $dep->id_departement }}">
                                        {{ $dep->nom_departement }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- VILLE -->
                        <div class="mb-3">
                            <select name="id_ville"
                                    class="form-select"
                                    id="villeSearch"
                                    onchange="loadQuartiersSearch(this.value)">
                                <option value="">Toutes les villes</option>
                            </select>
                        </div>

                        <!-- QUARTIER -->
                        <div class="mb-3">
                            <select name="id_quartier"
                                    class="form-select"
                                    id="quartierSearch">
                                <option value="">Tous les quartiers</option>
                            </select>
                        </div>

                        <!-- TYPE -->
                        <div class="mb-3">
                            <select name="id_typebien" class="form-select">
                                <option value="">Tous les types</option>
                                @foreach($typesBiens as $type)
                                    <option value="{{ $type->id_typebien }}">
                                        {{ $type->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BUDGET -->
                        <div class="mb-3">
                            <input type="number"
                                   name="prix_max"
                                   class="form-control"
                                   placeholder="Budget maximum (CFA)">
                        </div>

                        <button type="submit"
                                class="btn w-100 py-2 fw-semibold"
                                style="background:#4ECDC4; color:white;
                                       border-radius:10px">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ STATISTIQUES ═══ -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalBiens }}</div>
                    <p class="text-muted mb-0 fw-semibold">Biens total</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalDisponibles }}</div>
                    <p class="text-muted mb-0 fw-semibold">Disponibles</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalAgences }}</div>
                    <p class="text-muted mb-0 fw-semibold">Agences</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalClients }}</div>
                    <p class="text-muted mb-0 fw-semibold">Clients</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ BIENS DISPONIBLES ═══ -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="section-title">Biens disponibles</h2>
                <p class="text-muted mt-3">Découvrez nos dernières offres</p>
            </div>
            <a href="{{ route('biens.index') }}"
               class="btn fw-semibold"
               style="background:#4ECDC4; color:white; border-radius:10px">
                Voir tout <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        @if($biensDisponibles->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-home fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun bien disponible pour le moment</h4>
            </div>
        @else
        <div class="row g-4">
            @foreach($biensDisponibles as $bien)
            <div class="col-md-4">
                <div class="card bien-card h-100">

                    <!-- IMAGE AVEC BADGES -->
                    <div class="bien-image" style="position:relative">
                        @if($bien->photos && count($bien->photos) > 0)
                            <img src="{{ asset('storage/' . $bien->photos[0]) }}"
                                class="w-100 h-100"
                                style="object-fit:cover"
                                alt="{{ $bien->titre_bien }}"
                                onerror="this.parentElement.innerHTML=
                                '<i class=\'fas fa-building fa-4x text-white opacity-50\'></i>'">
                        @else
                            <i class="fas fa-building fa-4x text-white opacity-50"></i>
                        @endif

                        <!-- BADGE A LOUER / A VENDRE -->
                        <div style="position:absolute; top:12px; left:12px">
                            @if(isset($bien->type_contrat) && $bien->type_contrat == 'location')
                                <span class="badge fw-semibold px-3 py-2"
                                    style="background:rgba(78,205,196,0.9);
                                            border-radius:20px; font-size:11px">
                                    A LOUER
                                </span>
                            @else
                                <span class="badge fw-semibold px-3 py-2"
                                    style="background:rgba(231,76,60,0.9);
                                            border-radius:20px; font-size:11px">
                                    A VENDRE
                                </span>
                            @endif
                        </div>

                        <!-- BOUTON FAVORIS -->
                        <div style="position:absolute; top:12px; right:12px">
                            @if(session('client'))
                                <form action="{{ route('client.favoris.ajouter') }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="id_bien"
                                        value="{{ $bien->id_bien }}">
                                    <button type="submit"
                                            style="width:36px; height:36px;
                                                border-radius:50%; background:white;
                                                border:none; cursor:pointer;
                                                display:flex; align-items:center;
                                                justify-content:center;
                                                box-shadow:0 2px 8px rgba(0,0,0,0.2)">
                                        <i class="fas fa-heart"
                                        style="color:#e74c3c; font-size:14px"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button"
                                        style="width:36px; height:36px;
                                            border-radius:50%; background:white;
                                            border:none; cursor:pointer;
                                            display:flex; align-items:center;
                                            justify-content:center;
                                            box-shadow:0 2px 8px rgba(0,0,0,0.2)"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalInscriptionRapide">
                                    <i class="fas fa-heart"
                                    style="color:#ccc; font-size:14px"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- INFOS -->
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-2">{{ $bien->titre_bien }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"
                            style="color:#4ECDC4"></i>
                            {{ $bien->ville->nom_ville ?? 'N/A' }}
                            @if($bien->departement)
                                , {{ $bien->departement->nom_departement }}
                            @endif
                        </p>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-tag me-1" style="color:#4ECDC4"></i>
                            {{ $bien->typeBien->libelle ?? 'Non défini' }}
                            &nbsp;•&nbsp;
                            <i class="fas fa-ruler-combined me-1"></i>
                            {{ $bien->superficie }} m²
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold fs-5" style="color:#4ECDC4">
                                    {{ number_format($bien->prix, 0, ',', ' ') }}
                                </span>
                                <small class="text-muted">
                                    FCFA
                                    @if($bien->type_contrat == 'location')
                                        /mois
                                    @endif
                                </small>
                            </div>
                            <a href="{{ route('biens.show', $bien->id_bien) }}"
                            class="btn btn-sm fw-semibold"
                            style="background:#4ECDC4; color:white;
                                    border-radius:8px">
                                Voir détail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- ═══ POURQUOI NOUS ═══ -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title text-center mb-2">Pourquoi choisir ImmoGo ?</h2>
        <p class="text-center text-muted mb-5">
            La plateforme immobilière de référence au Mali
        </p>
        <div class="row g-4">
            <div class="col-md-4 text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center
                            justify-content-center mb-4"
                     style="width:80px; height:80px; background:#e8fffe">
                    <i class="fas fa-shield-alt fa-2x" style="color:#4ECDC4"></i>
                </div>
                <h5 class="fw-bold">Fiable et sécurisé</h5>
                <p class="text-muted">Toutes les agences sont vérifiées.</p>
            </div>
            <div class="col-md-4 text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center
                            justify-content-center mb-4"
                     style="width:80px; height:80px; background:#e8fffe">
                    <i class="fas fa-map-marked-alt fa-2x" style="color:#4ECDC4"></i>
                </div>
                <h5 class="fw-bold">Partout au Mali</h5>
                <p class="text-muted">Biens disponibles dans toutes les régions du Mali.</p>
            </div>
            <div class="col-md-4 text-center p-4">
                <div class="rounded-circle d-inline-flex align-items-center
                            justify-content-center mb-4"
                     style="width:80px; height:80px; background:#e8fffe">
                    <i class="fas fa-mobile-alt fa-2x" style="color:#4ECDC4"></i>
                </div>
                <h5 class="fw-bold">Application mobile</h5>
                <p class="text-muted">Disponible sur Android et iOS.</p>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
// ═══ SLIDER — droite vers gauche + 10 secondes ═══
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.dot');
const total  = slides.length;

// Position initiale
slides.forEach((slide, i) => {
    slide.style.transform = i === 0 ? 'translateX(0)' : 'translateX(100%)';
    slide.style.opacity   = '1';
    slide.style.transition = 'transform 1.2s ease-in-out';
});

function goToSlide(n) {
    const prev = currentSlide;
    currentSlide = n;

    // Slide sortant vers la gauche
    slides[prev].style.transform = 'translateX(-100%)';

    // Slide entrant depuis la droite
    slides[currentSlide].style.transform = 'translateX(100%)';

    // Force reflow
    slides[currentSlide].offsetHeight;

    // Slide entrant arrive au centre
    slides[currentSlide].style.transform = 'translateX(0)';

    // Dots
    dots[prev].classList.remove('active');
    dots[currentSlide].classList.add('active');

    // Remettre le slide sortant en position pour la prochaine fois
    setTimeout(() => {
        slides[prev].style.transition = 'none';
        slides[prev].style.transform  = 'translateX(100%)';
        setTimeout(() => {
            slides[prev].style.transition = 'transform 1.2s ease-in-out';
        }, 50);
    }, 1200);
}

function nextSlide() {
    goToSlide((currentSlide + 1) % total);
}

// 10 secondes entre chaque slide
setInterval(nextSlide, 10000);

// ═══ LOCALISATION EN CASCADE ═══
function loadVillesSearch(idDep) {
    const villeSelect    = document.getElementById('villeSearch');
    const quartierSelect = document.getElementById('quartierSearch');

    villeSelect.innerHTML    = '<option value="">Toutes les villes</option>';
    quartierSelect.innerHTML = '<option value="">Tous les quartiers</option>';

    if (!idDep) return;

    fetch(`/api/departements/${idDep}/villes`)
        .then(r => r.json())
        .then(villes => {
            villes.forEach(v => {
                villeSelect.innerHTML +=
                    `<option value="${v.id_ville}">${v.nom_ville}</option>`;
            });
        })
        .catch(err => console.error('Erreur villes:', err));
}

function loadQuartiersSearch(idVille) {
    const quartierSelect = document.getElementById('quartierSearch');
    quartierSelect.innerHTML = '<option value="">Tous les quartiers</option>';

    if (!idVille) return;

    fetch(`/api/villes/${idVille}/quartiers`)
        .then(r => r.json())
        .then(quartiers => {
            quartiers.forEach(q => {
                quartierSelect.innerHTML +=
                    `<option value="${q.id_quartier}">${q.nom_quartier}</option>`;
            });
        })
        .catch(err => console.error('Erreur quartiers:', err));
}
</script>
@endsection
