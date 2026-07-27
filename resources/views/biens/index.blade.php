@extends('layouts.app')
@section('title', 'Liste des biens')

@section('styles')
<style>
    .filter-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        position: sticky;
        top: 20px;
    }
    .bien-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    .bien-card:hover { transform: translateY(-5px); }
    .bien-image {
        height: 200px;
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
</style>
@endsection

@section('content')
<div class="container py-5">

    <!-- TITRE -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">
                <i class="fas fa-building me-2" style="color:#4ECDC4"></i>
                Liste des biens
            </h2>
            <p class="text-muted">{{ $biens->total() }} bien(s) trouvé(s)</p>
        </div>
    </div>

    <div class="row g-4">

        <!-- ═══ FILTRES (gauche) ═══ -->
        <div class="col-lg-3">
            <div class="card filter-card p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-filter me-2" style="color:#4ECDC4"></i>Filtres
                </h5>

                <form action="{{ route('biens.index') }}" method="GET" id="filterForm">

                    <!-- Recherche texte -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Recherche</label>
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Titre, description..."
                               value="{{ request('q') }}">
                    </div>

                    <!-- Région -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Région</label>
                        <select name="id_departement"
                                class="form-select"
                                onchange="loadVilles(this.value)">
                            <option value="">Toutes</option>
                            @foreach($departements as $dep)
                                <option value="{{ $dep->id_departement }}"
                                    {{ request('id_departement') == $dep->id_departement ? 'selected' : '' }}>
                                    {{ $dep->nom_departement }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ville -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ville</label>
                        <select name="id_ville" class="form-select" id="villeSelect">
                            <option value="">Toutes</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id_ville }}"
                                    {{ request('id_ville') == $ville->id_ville ? 'selected' : '' }}>
                                    {{ $ville->nom_ville }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type de bien -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Type de bien</label>
                        <select name="id_typebien" class="form-select">
                            <option value="">Tous</option>
                            @foreach($typesBiens as $type)
                                <option value="{{ $type->id_typebien }}"
                                    {{ request('id_typebien') == $type->id_typebien ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prix -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Prix minimum (CFA)</label>
                        <input type="number"
                               name="prix_min"
                               class="form-control"
                               placeholder="0"
                               value="{{ request('prix_min') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Prix maximum (CFA)</label>
                        <input type="number"
                               name="prix_max"
                               class="form-control"
                               placeholder="Sans limite"
                               value="{{ request('prix_max') }}">
                    </div>

                    <!-- Statut -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous</option>
                            <option value="disponible" {{ request('statut') == 'disponible' ? 'selected' : '' }}>
                                Disponible
                            </option>
                            <option value="reserve" {{ request('statut') == 'reserve' ? 'selected' : '' }}>
                                Réservé
                            </option>
                            <option value="loue" {{ request('statut') == 'loue' ? 'selected' : '' }}>
                                Loué
                            </option>
                            <option value="vendu" {{ request('statut') == 'vendu' ? 'selected' : '' }}>
                                Vendu
                            </option>
                        </select>
                    </div>

                    <!-- Tri prix -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Trier par prix</label>
                        <select name="tri_prix" class="form-select">
                            <option value="">Par défaut</option>
                            <option value="asc" {{ request('tri_prix') == 'asc' ? 'selected' : '' }}>
                                Prix croissant
                            </option>
                            <option value="desc" {{ request('tri_prix') == 'desc' ? 'selected' : '' }}>
                                Prix décroissant
                            </option>
                        </select>
                    </div>

                    <button type="submit"
                            class="btn w-100 fw-semibold mb-2"
                            style="background:#4ECDC4; color:white; border-radius:10px">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('biens.index') }}"
                       class="btn btn-outline-secondary w-100"
                       style="border-radius:10px">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </form>
            </div>
        </div>

        <!-- ═══ LISTE DES BIENS (droite) ═══ -->
        <div class="col-lg-9">
            @if($biens->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun bien trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos filtres</p>
                    <a href="{{ route('biens.index') }}"
                       class="btn fw-semibold"
                       style="background:#4ECDC4; color:white">
                        Voir tous les biens
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($biens as $bien)
                    <div class="col-md-4">
                        <div class="card bien-card h-100">

                            <!-- IMAGE AVEC BADGES -->
                            <div class="bien-image" style="position:relative">
                                @if($bien->photos && count($bien->photos) > 0)
                                    <img src="{{ asset('storage/' . $bien->photos[0]) }}"
                                        class="w-100 h-100"
                                        style="object-fit:cover"
                                        alt="{{ $bien->titre_bien }}">
                                @else
                                    <i class="fas fa-building fa-3x text-white opacity-50"></i>
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
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1">{{ $bien->titre_bien }}</h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"
                                    style="color:#4ECDC4"></i>
                                    {{ $bien->ville->nom_ville ?? 'N/A' }}
                                </p>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-tag me-1" style="color:#4ECDC4"></i>
                                    {{ $bien->typeBien->libelle ?? 'N/A' }}
                                    &nbsp;•&nbsp; {{ $bien->superficie }} m²
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="color:#4ECDC4">
                                        {{ number_format($bien->prix, 0, ',', ' ') }}
                                        <small class="text-muted">
                                            CFA{{ $bien->type_contrat == 'location' ? '/mois' : '' }}
                                        </small>
                                    </span>
                                    <a href="{{ route('biens.show', $bien->id_bien) }}"
                                    class="btn btn-sm fw-semibold"
                                    style="background:#4ECDC4; color:white;
                                            border-radius:8px">
                                        Détail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- PAGINATION -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $biens->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Charger les villes selon la région
function loadVilles(idDepartement) {
    const villeSelect = document.getElementById('villeSelect');
    villeSelect.innerHTML = '<option value="">Toutes</option>';

    if (!idDepartement) return;

    fetch(`/api/departements/${idDepartement}/villes`)
        .then(res => res.json())
        .then(villes => {
            villes.forEach(ville => {
                villeSelect.innerHTML +=
                    `<option value="${ville.id_ville}">${ville.nom_ville}</option>`;
            });
        });
}
</script>
@endsection