@extends('layouts.superadmin')
@section('title', 'Gestion des Agences')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Toutes les Agences</h4>
    <button class="btn fw-semibold"
            style="background:#4ECDC4; color:white; border-radius:10px"
            data-bs-toggle="modal" data-bs-target="#modalCreerAgence">
        <i class="fas fa-plus me-2"></i>Nouvelle agence
    </button>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="fas fa-exclamation-circle me-2"></i>Erreurs :</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Logo</th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Admins</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agences as $agence)
                    <tr>
                        <td>
                            @if($agence->logo)
                                <img src="{{ asset('storage/' . $agence->logo) }}"
                                     style="width:40px; height:40px;
                                            object-fit:cover; border-radius:8px">
                            @else
                                <div style="width:40px; height:40px;
                                            background:#e8fffe; border-radius:8px;
                                            display:flex; align-items:center;
                                            justify-content:center">
                                    <i class="fas fa-building"
                                       style="color:#4ECDC4"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $agence->nom_agence }}</td>
                        <td>{{ $agence->adresse_agence }}</td>
                        <td>{{ $agence->tel_agence }}</td>
                        <td>{{ $agence->email }}</td>
                        <td>
                            <span class="badge" style="background:#4ECDC4">
                                {{ $agence->administrateurs->count() }} admin(s)
                            </span>
                        </td>
                        <td>
                            <!-- MODIFIER -->
                            <button class="btn btn-sm btn-outline-warning me-1"
                                    title="Modifier"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalModifier{{ $agence->id_agence }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- SUPPRIMER -->
                            <form action="{{ route('superadmin.agences.supprimer',
                                          $agence->id_agence) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer cette agence ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Aucune agence pour le moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $agences->links() }}
    </div>
</div>

<!-- ═══ MODAL CRÉER AGENCE ═══ -->
<div class="modal fade" id="modalCreerAgence" tabindex="-1">
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

                    <!-- SECTION 1 : INFOS AGENCE -->
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
                                       class="form-control
                                       @error('nom_agence') is-invalid @enderror"
                                       value="{{ old('nom_agence') }}"
                                       required
                                       placeholder="Ex: Immo Excellence">
                                @error('nom_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Logo de l'agence
                                </label>
                                <div class="border rounded-3 p-2 text-center"
                                     style="border-style:dashed !important;
                                            cursor:pointer"
                                     onclick="document.getElementById('logoInput')
                                              .click()">
                                    <div id="logoPreview">
                                        <i class="fas fa-camera fa-2x text-muted"></i>
                                        <p class="small text-muted mb-0 mt-1">
                                            Cliquer pour ajouter
                                        </p>
                                    </div>
                                    <input type="file" name="logo" id="logoInput"
                                           accept="image/*" style="display:none"
                                           onchange="previewLogo(this)">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" name="adresse_agence"
                                       class="form-control
                                       @error('adresse_agence') is-invalid @enderror"
                                       value="{{ old('adresse_agence') }}"
                                       required
                                       placeholder="Ex: Bamako, Mali">
                                @error('adresse_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" name="tel_agence"
                                       class="form-control
                                       @error('tel_agence') is-invalid @enderror"
                                       value="{{ old('tel_agence') }}"
                                       required
                                       placeholder="+223 XX XX XX XX">
                                @error('tel_agence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email de l'agence
                                </label>
                                <input type="email" name="email"
                                       class="form-control
                                       @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="contact@agence.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 : ADMIN PRINCIPAL -->
                    <div class="p-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <span class="rounded-circle d-inline-flex
                                         align-items-center justify-content-center me-2"
                                  style="width:28px; height:28px;
                                         background:#2C3E50; color:white;
                                         font-size:13px">2</span>
                            Administrateur principal de l'agence
                        </h6>
                        <div class="alert border-0 rounded-3"
                             style="background:#e8fffe">
                            <i class="fas fa-info-circle me-2"
                               style="color:#4ECDC4"></i>
                            Cet administrateur aura accès complet à la gestion.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom_admin"
                                       class="form-control
                                       @error('nom_admin') is-invalid @enderror"
                                       value="{{ old('nom_admin') }}"
                                       required>
                                @error('nom_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom_admin"
                                       class="form-control
                                       @error('prenom_admin') is-invalid @enderror"
                                       value="{{ old('prenom_admin') }}"
                                       required>
                                @error('prenom_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Email administrateur
                                </label>
                                <input type="email" name="email_admin"
                                       class="form-control
                                       @error('email_admin') is-invalid @enderror"
                                       value="{{ old('email_admin') }}"
                                       required>
                                @error('email_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Mot de passe
                                </label>
                                <input type="password" name="mot_de_passe"
                                       class="form-control
                                       @error('mot_de_passe') is-invalid @enderror"
                                       required
                                       placeholder="Minimum 6 caractères">
                                @error('mot_de_passe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Confirmer mot de passe
                                </label>
                                <input type="password"
                                       name="mot_de_passe_confirmation"
                                       class="form-control"
                                       required>
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

<!-- ═══ MODALS MODIFIER AGENCE ═══ -->
@foreach($agences as $agence)
<div class="modal fade" id="modalModifier{{ $agence->id_agence }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#f39c12,#e67e22);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-edit me-2"></i>
                    Modifier — {{ $agence->nom_agence }}
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('superadmin.agences.modifier',
                              $agence->id_agence) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Nom de l'agence
                            </label>
                            <input type="text" name="nom_agence"
                                   class="form-control"
                                   value="{{ $agence->nom_agence }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Logo</label>
                            <div class="border rounded-3 p-2 text-center"
                                 style="border-style:dashed !important; cursor:pointer"
                                 onclick="document.getElementById(
                                 'logoModifier{{ $agence->id_agence }}').click()">
                                @if($agence->logo)
                                    <img id="logoPreviewModifier{{ $agence->id_agence }}"
                                         src="{{ asset('storage/' . $agence->logo) }}"
                                         style="width:60px; height:60px;
                                                object-fit:cover; border-radius:8px;
                                                border:2px solid #4ECDC4">
                                @else
                                    <div id="logoPreviewModifier{{ $agence->id_agence }}">
                                        <i class="fas fa-camera fa-2x text-muted"></i>
                                        <p class="small text-muted mb-0 mt-1">
                                            Changer logo
                                        </p>
                                    </div>
                                @endif
                                <input type="file"
                                       name="logo"
                                       id="logoModifier{{ $agence->id_agence }}"
                                       accept="image/*"
                                       style="display:none"
                                       onchange="previewLogoModifier(
                                       this, {{ $agence->id_agence }})">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="adresse_agence"
                                   class="form-control"
                                   value="{{ $agence->adresse_agence }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="tel_agence"
                                   class="form-control"
                                   value="{{ $agence->tel_agence }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control"
                                   value="{{ $agence->email }}" required>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn fw-semibold"
                                style="background:#f39c12; color:white;
                                       border-radius:10px">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// Logo créer
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').innerHTML = `
                <img src="${e.target.result}"
                     style="width:80px; height:80px; object-fit:cover;
                            border-radius:8px; border:2px solid #4ECDC4">
                <p class="small text-muted mb-0 mt-1">
                    <i class="fas fa-check-circle" style="color:#4ECDC4"></i>
                    Logo sélectionné
                </p>`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Logo modifier
function previewLogoModifier(input, id) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const el = document.getElementById('logoPreviewModifier' + id);
            if (el.tagName === 'IMG') {
                el.src = e.target.result;
            } else {
                el.outerHTML = `
                    <img id="logoPreviewModifier${id}"
                         src="${e.target.result}"
                         style="width:60px; height:60px; object-fit:cover;
                                border-radius:8px; border:2px solid #4ECDC4">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Rouvrir modal créer si erreurs
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(
            document.getElementById('modalCreerAgence')
        );
        modal.show();
    });
@endif
</script>
@endsection
