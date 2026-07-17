@extends('layouts.app')
@section('title', $bien->titre_bien)

@section('styles')
<style>
    .bien-hero {
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(135deg, #4ECDC4, #2C3E50);
    }
    .info-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .badge-disponible { background-color: #2ecc71; font-size: 1rem; padding: 8px 16px; }
    .badge-reserve    { background-color: #e67e22; font-size: 1rem; padding: 8px 16px; }
    .badge-loue       { background-color: #3498db; font-size: 1rem; padding: 8px 16px; }
    .badge-vendu      { background-color: #e74c3c; font-size: 1rem; padding: 8px 16px; }
    .similar-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    .similar-card:hover { transform: translateY(-5px); }
    .thumbnail-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.2s;
        background: #f0f0f0;
    }
    .thumbnail-img:hover,
    .thumbnail-img.active { border-color: #4ECDC4; }
</style>
@endsection

@section('content')
<div class="container py-5">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" style="color:#4ECDC4">Accueil</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('biens.index') }}" style="color:#4ECDC4">Biens</a>
            </li>
            <li class="breadcrumb-item active">{{ $bien->titre_bien }}</li>
        </ol>
    </nav>

    <div class="row g-4">

        <!-- ═══ COLONNE GAUCHE ═══ -->
        <div class="col-lg-8">

            <!-- IMAGE PRINCIPALE -->
            <div class="bien-hero mb-3" id="mainImageContainer">
                @if($bien->photos && count($bien->photos) > 0)
                    <img src="{{ asset('storage/' . $bien->photos[0]) }}"
                    class="w-100"
                    style="width:100%;
                            height:auto;
                            max-height:1000px;
                            object-fit:cover;
                            display:block;
                            border-radius:16px;"
                    id="mainImage"
                    alt="{{ $bien->titre_bien }}">
                @else
                    <i class="fas fa-building fa-5x text-white opacity-50"></i>
                @endif
            </div>

            <!-- MINIATURES PHOTOS -->
            @if($bien->photos && count($bien->photos) > 1)
            <div class="d-flex gap-2 mb-4 flex-wrap">
                @foreach($bien->photos as $index => $photo)
                <img src="{{ asset('storage/' . $photo) }}"
                     class="thumbnail-img {{ $index === 0 ? 'active' : '' }}"
                     alt="Photo {{ $index + 1 }}"
                     onclick="changeMainImage(this, '{{ asset('storage/' . $photo) }}')">
                @endforeach
            </div>
            @endif

            <!-- TITRE ET STATUT -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="fw-bold">{{ $bien->titre_bien }}</h2>
                <span class="badge badge-{{ $bien->statut }}">
                    {{ ucfirst($bien->statut) }}
                </span>
            </div>

            <!-- INFOS RAPIDES -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                        <i class="fas fa-tag fa-2x mb-2" style="color:#4ECDC4"></i>
                        <p class="mb-0 fw-semibold small">
                            {{ $bien->typeBien->libelle ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                        <i class="fas fa-ruler-combined fa-2x mb-2"
                           style="color:#4ECDC4"></i>
                        <p class="mb-0 fw-semibold small">{{ $bien->superficie }} m²</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                        <i class="fas fa-map-marker-alt fa-2x mb-2"
                           style="color:#4ECDC4"></i>
                        <p class="mb-0 fw-semibold small">
                            {{ $bien->ville->nom_ville ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                        <i class="fas fa-building fa-2x mb-2" style="color:#4ECDC4"></i>
                        <p class="mb-0 fw-semibold small">
                            {{ $bien->agence->nom_agence ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="card info-card p-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
                    Description
                </h5>
                <p class="text-muted lh-lg">{{ $bien->description_bien }}</p>
            </div>

            <!-- LOCALISATION -->
            <div class="card info-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-map-marker-alt me-2" style="color:#4ECDC4"></i>
                    Localisation
                </h5>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Région</p>
                        <p class="fw-semibold">
                            {{ $bien->departement->nom_departement ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Ville</p>
                        <p class="fw-semibold">
                            {{ $bien->ville->nom_ville ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Quartier</p>
                        <p class="fw-semibold">
                            {{ $bien->quartier->nom_quartier ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <p class="text-muted mt-2">
                    <i class="fas fa-map-pin me-2"></i>{{ $bien->localisation }}
                </p>
            </div>
        </div>

        <!-- ═══ COLONNE DROITE ═══ -->
        <div class="col-lg-4">

            <!-- PRIX ET BOUTONS -->
            <div class="card info-card p-4 mb-4">
                <div class="text-center mb-4">
                    <p class="text-muted mb-1">Prix</p>
                    <h2 class="fw-bold" style="color:#4ECDC4">
                        {{ number_format($bien->prix, 0, ',', ' ') }}
                    </h2>
                    <p class="text-muted">FCFA</p>
                </div>

                @if($bien->statut === 'disponible')
                    @if(session('client'))
                    <!-- BOUTON RÉSERVER avec CinetPay -->
                    <form action="{{ route('cinetpay.acompte') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_bien" value="{{ $bien->id_bien }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type de contrat</label>
                            <select name="type_contrat" class="form-select">
                                <option value="location">Location</option>
                                <option value="vente">Vente</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="btn w-100 py-3 fw-semibold mb-2"
                                style="background:#4ECDC4; color:white; border-radius:12px">
                            <i class="fas fa-calendar-check me-2"></i>
                            Réserver — Payer acompte ({{ number_format($bien->prix * 0.10, 0, ',', ' ') }} CFA)
                        </button>
                    </form>

                    <!-- BOUTON PAYER TOTAL avec CinetPay -->
                    <form action="{{ route('cinetpay.total') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_bien" value="{{ $bien->id_bien }}">
                        <input type="hidden" name="type_contrat" value="vente">
                        <button type="submit"
                                class="btn w-100 py-3 fw-semibold btn-outline-success"
                                style="border-radius:12px">
                            <i class="fas fa-credit-card me-2"></i>
                            Payer la totalité ({{ number_format($bien->prix, 0, ',', ' ') }} CFA)
                        </button>
                    </form>
                @endif

                @else
                    <button class="btn w-100 py-3 fw-semibold btn-secondary"
                            disabled>
                        <i class="fas fa-times-circle me-2"></i>
                        {{ ucfirst($bien->statut) }}
                    </button>
                @endif
            </div>

            <!-- AGENCE -->
            <div class="card info-card p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-building me-2" style="color:#4ECDC4"></i>
                    Agence
                </h5>
                @if($bien->agence)
                    @if($bien->agence->logo)
                        <img src="{{ asset('storage/' . $bien->agence->logo) }}"
                             alt="{{ $bien->agence->nom_agence }}"
                             style="width:60px; height:60px; object-fit:cover;
                                    border-radius:8px; margin-bottom:10px">
                    @endif
                    <p class="fw-semibold mb-1">{{ $bien->agence->nom_agence }}</p>
                    <p class="text-muted small mb-1">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $bien->agence->adresse_agence }}
                    </p>
                    <p class="text-muted small mb-1">
                        <i class="fas fa-phone me-1"></i>
                        {{ $bien->agence->tel_agence }}
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-envelope me-1"></i>
                        {{ $bien->agence->email }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- ═══ BIENS SIMILAIRES ═══ -->
    @if($biensSimilaires->isNotEmpty())
    <div class="mt-5">
        <h4 class="fw-bold mb-4">
            <i class="fas fa-th me-2" style="color:#4ECDC4"></i>
            Biens similaires
        </h4>
        <div class="row g-4">
            @foreach($biensSimilaires as $similaire)
            <div class="col-md-4">
                <div class="card similar-card">
                    <div style="height:160px;
                                background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                                display:flex; align-items:center;
                                justify-content:center">
                        @if($similaire->photos && count($similaire->photos) > 0)
                            <img src="{{ asset('storage/' . $similaire->photos[0]) }}"
                                 class="w-100 h-100"
                                 style="object-fit:cover"
                                 alt="{{ $similaire->titre_bien }}">
                        @else
                            <i class="fas fa-building fa-3x text-white opacity-50"></i>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1">{{ $similaire->titre_bien }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"
                               style="color:#4ECDC4"></i>
                            {{ $similaire->ville->nom_ville ?? 'N/A' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small" style="color:#4ECDC4">
                                {{ number_format($similaire->prix, 0, ',', ' ') }} FCFA
                            </span>
                            <a href="{{ route('biens.show', $similaire->id_bien) }}"
                               class="btn btn-sm"
                               style="background:#4ECDC4; color:white;
                                      border-radius:8px">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ═══ MODALS ═══ --}}
@if(session('client') && $bien->statut === 'disponible')

<!-- MODAL RÉSERVATION (acompte 10%) -->
<div class="modal fade" id="modalReserver" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-calendar-check me-2"></i>
                    Réserver ce bien
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="alert border-0 rounded-3 mb-4"
                     style="background:#e8fffe">
                    <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
                    Acompte requis :
                    <strong style="color:#4ECDC4">
                        {{ number_format($bien->prix * 0.10, 0, ',', ' ') }} FCFA
                    </strong>
                    (10% du prix total)
                </div>

                <form action="{{ route('client.reserver') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_bien"
                           value="{{ $bien->id_bien }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type de contrat</label>
                        <select name="type_contrat" class="form-select" required>
                            <option value="location">
                                <i class="fas fa-key"></i> Location
                            </option>
                            <option value="vente">
                                <i class="fas fa-home"></i> Vente
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Mode de paiement de l'acompte
                        </label>
                        <select name="id_mode_paiement" class="form-select" required>
                            @foreach(\App\Models\ModePaiement::all() as $mode)
                                <option value="{{ $mode->id_mode_paiement }}">
                                    {{ $mode->nom_mode_paiement }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="btn w-100 py-3 fw-semibold"
                            style="background:#4ECDC4; color:white;
                                   border-radius:12px">
                        <i class="fas fa-check me-2"></i>
                        Confirmer la réservation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PAIEMENT TOTAL -->
<div class="modal fade" id="modalPayerTotal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#2ecc71,#27ae60);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-credit-card me-2"></i>
                    Paiement total
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    Montant total à payer :
                    <strong>
                        {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                    </strong>
                </div>

                <form action="{{ route('client.payer.total') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_bien"
                           value="{{ $bien->id_bien }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type de contrat</label>
                        <select name="type_contrat" class="form-select" required>
                            <option value="location">Location</option>
                            <option value="vente">Vente</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mode de paiement</label>
                        <select name="id_mode_paiement" class="form-select" required>
                            @foreach(\App\Models\ModePaiement::all() as $mode)
                                <option value="{{ $mode->id_mode_paiement }}">
                                    {{ $mode->nom_mode_paiement }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="btn w-100 py-3 fw-semibold btn-success"
                            style="border-radius:12px">
                        <i class="fas fa-credit-card me-2"></i>
                        Payer {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

@section('scripts')
<script>
// Changer l'image principale
function changeMainImage(thumbnail, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.classList.remove('active');
    });
    thumbnail.classList.add('active');
}
</script>
@endsection
