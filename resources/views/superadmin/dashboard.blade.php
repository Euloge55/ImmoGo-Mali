@extends('layouts.superadmin')
@section('title', 'Dashboard Super Admin')

@section('content')

<!-- INFOS SUPER ADMIN -->
<div class="card border-0 rounded-4 shadow-sm mb-4"
     style="background:linear-gradient(135deg,#2C3E50,#3498db)">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center
                        justify-content-center me-4"
                 style="width:70px; height:70px;
                        background:rgba(78,205,196,0.3)">
                <i class="fas fa-shield-alt fa-2x" style="color:#4ECDC4"></i>
            </div>
            <div>
                <h4 class="text-white fw-bold mb-1">
                    Bienvenue, {{ session('superadmin')->nom_superadmin }}
                </h4>
                <p class="text-white opacity-75 mb-1">
                    <i class="fas fa-envelope me-2"></i>
                    {{ session('superadmin')->email }}
                </p>
                <span class="badge" style="background:#4ECDC4">
                    <i class="fas fa-crown me-1"></i>Super Administrateur
                </span>
            </div>
        </div>
    </div>
</div>

<!-- STATISTIQUES -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#4ECDC4,#3dbdb4)">
            <div class="stat-number">{{ $totalAgences }}</div>
            <p class="mb-0 opacity-75">
                <i class="fas fa-building me-2"></i>Agences
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#2ecc71,#27ae60)">
            <div class="stat-number">{{ $totalClients }}</div>
            <p class="mb-0 opacity-75">
                <i class="fas fa-users me-2"></i>Clients
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#3498db,#2980b9)">
            <div class="stat-number">{{ $totalBiens }}</div>
            <p class="mb-0 opacity-75">
                <i class="fas fa-home me-2"></i>Biens
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#9b59b6,#8e44ad)">
            <div class="stat-number">{{ $totalContrats }}</div>
            <p class="mb-0 opacity-75">
                <i class="fas fa-file-contract me-2"></i>Contrats
            </p>
        </div>
    </div>
</div>

<!-- AGENCES SEULEMENT -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-building me-2" style="color:#4ECDC4"></i>
                Dernières agences
            </h5>
            <div class="d-flex gap-2">
                <button class="btn fw-semibold"
                        style="background:#4ECDC4; color:white; border-radius:10px"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCreerAgenceDash">
                    <i class="fas fa-plus me-2"></i>Nouvelle agence
                </button>
                <a href="{{ route('superadmin.agences') }}"
                   class="btn btn-outline-secondary fw-semibold"
                   style="border-radius:10px">
                    <i class="fas fa-list me-2"></i>Voir toutes
                </a>
            </div>
        </div>

        @forelse($dernieresAgences as $agence)
        <div class="d-flex justify-content-between align-items-center
                    p-3 mb-2 rounded-3" style="background:#f8f9fa">
            <div class="d-flex align-items-center">
                @if($agence->logo)
                    <img src="{{ asset('storage/' . $agence->logo) }}"
                         style="width:45px; height:45px; object-fit:cover;
                                border-radius:8px; margin-right:15px">
                @else
                    <div style="width:45px; height:45px; background:#e8fffe;
                                border-radius:8px; margin-right:15px;
                                display:flex; align-items:center;
                                justify-content:center">
                        <i class="fas fa-building" style="color:#4ECDC4"></i>
                    </div>
                @endif
                <div>
                    <p class="mb-0 fw-semibold">{{ $agence->nom_agence }}</p>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $agence->adresse_agence }}
                    </small>
                </div>
            </div>
            <div class="text-end">
                <span class="badge" style="background:#4ECDC4">
                    {{ $agence->administrateurs->count() }} admin(s)
                </span>
                <br>
                <small class="text-muted">
                    {{ $agence->created_at->format('d/m/Y') }}
                </small>
            </div>
        </div>
        @empty
        <div class="text-center py-4">
            <i class="fas fa-building fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucune agence créée pour le moment</p>
            <button class="btn fw-semibold"
                    style="background:#4ECDC4; color:white; border-radius:10px"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCreerAgenceDash">
                <i class="fas fa-plus me-2"></i>Créer la première agence
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- MODAL CRÉER AGENCE -->
<div class="modal fade" id="modalCreerAgenceDash" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-building me-2"></i>
                    Créer une nouvelle agence
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <form action="{{ route('superadmin.agences.creer') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="p-4 border-bottom">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <span class="rounded-circle d-inline-flex
                                         align-items-center justify-content-center me-2"
                                  style="width:28px; height:28px;
                                         background:#4ECDC4; color:white;
                                         font-size:13px">1</span>
                            Informations de l'agence
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    Nom de l'agence
                                </label>
                                <input type="text" name="nom_agence"
                                       class="form-control" required
                                       placeholder="Ex: Immo Excellence">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Logo</label>
                                <div class="border rounded-3 p-2 text-center"
                                     style="border-style:dashed !important;
                                            cursor:pointer"
                                     onclick="document.getElementById(
                                     'logoDash').click()">
                                    <div id="logoPreviewDash">
                                        <i class="fas fa-camera fa-2x text-muted"></i>
                                        <p class="small text-muted mb-0 mt-1">
                                            Ajouter logo
                                        </p>
                                    </div>
                                    <input type="file" name="logo"
                                           id="logoDash" accept="image/*"
                                           style="display:none"
                                           onchange="previewLogoDash(this)">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" name="adresse_agence"
                                       class="form-control" required
                                       placeholder="Ex: Bamako, Mali">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" name="tel_agence"
                                       class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <span class="rounded-circle d-inline-flex
                                         align-items-center justify-content-center me-2"
                                  style="width:28px; height:28px;
                                         background:#2C3E50; color:white;
                                         font-size:13px">2</span>
                            Administrateur principal
                        </h6>
                        <div class="alert border-0 rounded-3"
                             style="background:#e8fffe">
                            <i class="fas fa-info-circle me-2"
                               style="color:#4ECDC4"></i>
                            Cet administrateur gérera cette agence.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom_admin"
                                       class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom_admin"
                                       class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Email admin
                                </label>
                                <input type="email" name="email_admin"
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
                                <label class="form-label fw-semibold">
                                    Confirmer
                                </label>
                                <input type="password"
                                       name="mot_de_passe_confirmation"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 pb-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn fw-semibold"
                                style="background:#4ECDC4; color:white;
                                       border-radius:10px; padding:10px 25px">
                            <i class="fas fa-check me-2"></i>
                            Créer l'agence + l'admin
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
function previewLogoDash(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreviewDash').innerHTML = `
                <img src="${e.target.result}"
                     style="width:70px; height:70px; object-fit:cover;
                            border-radius:8px; border:2px solid #4ECDC4">
                <p class="small text-muted mb-0 mt-1">Logo sélectionné</p>`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection