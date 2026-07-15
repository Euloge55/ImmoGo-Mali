@extends('layouts.admin')
@section('title', 'Gestion des Biens')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Mes Biens</h4>
    <button class="btn fw-semibold"
            style="background:#4ECDC4; color:white; border-radius:10px"
            data-bs-toggle="modal" data-bs-target="#modalCreerBien">
        <i class="fas fa-plus me-2"></i>Ajouter un bien
    </button>
</div>

<!-- TABLE -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Ville</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($biens as $bien)
                    <tr>
                        <td class="fw-semibold">{{ $bien->titre_bien }}</td>
                        <td>{{ $bien->typeBien->libelle ?? 'N/A' }}</td>
                        <td>{{ $bien->ville->nom_ville ?? 'N/A' }}</td>
                        <td style="color:#4ECDC4" class="fw-bold">
                            {{ number_format($bien->prix, 0, ',', ' ') }} F
                        </td>
                        <td>
                            <form action="{{ route('admin.biens.statut',
                                          $bien->id_bien) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <select name="statut"
                                        class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                    <option value="disponible"
                                        {{ $bien->statut=='disponible'
                                           ? 'selected' : '' }}>
                                        Disponible
                                    </option>
                                    <option value="reserve"
                                        {{ $bien->statut=='reserve'
                                           ? 'selected' : '' }}>
                                        Réservé
                                    </option>
                                    <option value="loue"
                                        {{ $bien->statut=='loue'
                                           ? 'selected' : '' }}>
                                        Loué
                                    </option>
                                    <option value="vendu"
                                        {{ $bien->statut=='vendu'
                                           ? 'selected' : '' }}>
                                        Vendu
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <!-- VOIR -->
                            <a href="{{ route('biens.show', $bien->id_bien) }}"
                               class="btn btn-sm btn-outline-info me-1"
                               title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- MODIFIER -->
                            <button class="btn btn-sm btn-outline-warning me-1"
                                    title="Modifier"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalModifier{{ $bien->id_bien }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- SUPPRIMER -->
                            <form action="{{ route('admin.biens.supprimer',
                                          $bien->id_bien) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce bien ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Aucun bien pour le moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $biens->links() }}
    </div>
</div>

<!-- ═══ MODAL CRÉER BIEN ═══ -->
<div class="modal fade" id="modalCreerBien" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter un bien
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.biens.creer') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Titre du bien</label>
                            <input type="text" name="titre_bien"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type de bien</label>
                            <select name="id_typebien" class="form-select" required>
                                <option value="">Choisir</option>
                                @foreach($typesBiens as $type)
                                    <option value="{{ $type->id_typebien }}">
                                        {{ $type->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description_bien"
                                      class="form-control"
                                      rows="3" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix (FCFA)</label>
                            <input type="number" name="prix"
                                   class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Type de contrat</label>
                            <select name="type_contrat" class="form-select" required>
                                <option value="location">
                                    🏠 À Louer
                                </option>
                                <option value="vente">
                                    🏷️ À Vendre
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Superficie (m²)
                            </label>
                            <input type="number" name="superficie"
                                   class="form-control" step="0.1" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Région</label>
                            <select name="id_departement" class="form-select"
                                    required
                                    onchange="loadVillesModal(this.value)">
                                <option value="">Choisir</option>
                                @foreach($departements as $dep)
                                    <option value="{{ $dep->id_departement }}">
                                        {{ $dep->nom_departement }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ville</label>
                            <select name="id_ville" class="form-select"
                                    id="villeSelectModal" required
                                    onchange="loadQuartiersModal(this.value)">
                                <option value="">Choisir d'abord une région</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quartier</label>
                            <select name="id_quartier" class="form-select"
                                    id="quartierSelectModal">
                                <option value="">Choisir d'abord une ville</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Adresse précise
                            </label>
                            <input type="text" name="localisation"
                                   class="form-control" required>
                        </div>

                        <!-- PHOTOS -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Photos du bien
                                <small class="text-muted fw-normal">
                                    (5 maximum, JPG/PNG)
                                </small>
                            </label>
                            <div class="border rounded-3 p-3"
                                 style="border-style:dashed !important;
                                        background:#f8f9fa">
                                <input type="file"
                                       name="photos[]"
                                       id="photosInput"
                                       class="form-control mb-2"
                                       accept="image/jpeg,image/png,image/jpg"
                                       multiple
                                       onchange="previewPhotos(this)">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Sélectionnez jusqu'à 5 photos
                                </small>
                                <div id="photosPreview"
                                     class="d-flex flex-wrap gap-2 mt-3"></div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn fw-semibold"
                                style="background:#4ECDC4; color:white">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODALS MODIFIER BIEN ═══ -->
@foreach($biens as $bien)
<div class="modal fade" id="modalModifier{{ $bien->id_bien }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#f39c12,#e67e22);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-edit me-2"></i>
                    Modifier — {{ $bien->titre_bien }}
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.biens.modifier', $bien->id_bien) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Titre</label>
                            <input type="text" name="titre_bien"
                                   class="form-control"
                                   value="{{ $bien->titre_bien }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="id_typebien" class="form-select" required>
                                @foreach($typesBiens as $type)
                                    <option value="{{ $type->id_typebien }}"
                                        {{ $bien->id_typebien == $type->id_typebien
                                           ? 'selected' : '' }}>
                                        {{ $type->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description_bien"
                                      class="form-control"
                                      rows="3" required>{{ $bien->description_bien }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix (FCFA)</label>
                            <input type="number" name="prix"
                                   class="form-control"
                                   value="{{ $bien->prix }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Superficie (m²)
                            </label>
                            <input type="number" name="superficie"
                                   class="form-control"
                                   value="{{ $bien->superficie }}"
                                   step="0.1" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Région</label>
                            <select name="id_departement" class="form-select" required
                                    onchange="loadVillesModifier(
                                    this.value, {{ $bien->id_bien }})">
                                <option value="">Choisir</option>
                                @foreach($departements as $dep)
                                    <option value="{{ $dep->id_departement }}"
                                        {{ $bien->id_departement ==
                                           $dep->id_departement
                                           ? 'selected' : '' }}>
                                        {{ $dep->nom_departement }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ville</label>
                            <select name="id_ville" class="form-select" required
                                    id="villeModifier{{ $bien->id_bien }}"
                                    onchange="loadQuartiersModifier(
                                    this.value, {{ $bien->id_bien }})">
                                @if($bien->ville)
                                    <option value="{{ $bien->id_ville }}" selected>
                                        {{ $bien->ville->nom_ville }}
                                    </option>
                                @else
                                    <option value="">Choisir une région d'abord</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quartier</label>
                            <select name="id_quartier" class="form-select"
                                    id="quartierModifier{{ $bien->id_bien }}">
                                <option value="">Aucun</option>
                                @if($bien->quartier)
                                    <option value="{{ $bien->id_quartier }}" selected>
                                        {{ $bien->quartier->nom_quartier }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Adresse précise
                            </label>
                            <input type="text" name="localisation"
                                   class="form-control"
                                   value="{{ $bien->localisation }}" required>
                        </div>

                        <!-- PHOTOS -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Nouvelles photos
                                <small class="text-muted fw-normal">
                                    (optionnel — remplace les anciennes)
                                </small>
                            </label>
                            <input type="file"
                                   name="photos[]"
                                   class="form-control mb-2"
                                   accept="image/*"
                                   multiple>

                            @if($bien->photos && count($bien->photos) > 0)
                                <div class="d-flex gap-2 mt-2 flex-wrap">
                                    @foreach($bien->photos as $photo)
                                        <img src="{{ asset('storage/' . $photo) }}"
                                             style="width:60px; height:60px;
                                                    object-fit:cover;
                                                    border-radius:8px;
                                                    border:2px solid #4ECDC4">
                                    @endforeach
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-images me-1"></i>
                                    {{ count($bien->photos) }} photo(s) actuelle(s)
                                </small>
                            @endif
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
// ═══ CRÉER BIEN ═══
function loadVillesModal(idDep) {
    const select = document.getElementById('villeSelectModal');
    select.innerHTML = '<option value="">Chargement...</option>';
    if (!idDep) return;
    fetch(`/api/departements/${idDep}/villes`)
        .then(r => r.json())
        .then(villes => {
            select.innerHTML = '<option value="">Choisir</option>';
            villes.forEach(v => {
                select.innerHTML +=
                    `<option value="${v.id_ville}">${v.nom_ville}</option>`;
            });
            // Vider quartiers
            document.getElementById('quartierSelectModal').innerHTML =
                '<option value="">Choisir d\'abord une ville</option>';
        });
}

function loadQuartiersModal(idVille) {
    const select = document.getElementById('quartierSelectModal');
    select.innerHTML = '<option value="">Chargement...</option>';
    if (!idVille) return;
    fetch(`/api/villes/${idVille}/quartiers`)
        .then(r => r.json())
        .then(quartiers => {
            select.innerHTML = '<option value="">Choisir (optionnel)</option>';
            quartiers.forEach(q => {
                select.innerHTML +=
                    `<option value="${q.id_quartier}">${q.nom_quartier}</option>`;
            });
        });
}

// ═══ MODIFIER BIEN ═══
function loadVillesModifier(idDep, idBien) {
    const select = document.getElementById('villeModifier' + idBien);
    select.innerHTML = '<option value="">Chargement...</option>';
    if (!idDep) return;
    fetch(`/api/departements/${idDep}/villes`)
        .then(r => r.json())
        .then(villes => {
            select.innerHTML = '<option value="">Choisir</option>';
            villes.forEach(v => {
                select.innerHTML +=
                    `<option value="${v.id_ville}">${v.nom_ville}</option>`;
            });
            // Vider quartiers
            document.getElementById('quartierModifier' + idBien).innerHTML =
                '<option value="">Aucun</option>';
        });
}

function loadQuartiersModifier(idVille, idBien) {
    const select = document.getElementById('quartierModifier' + idBien);
    select.innerHTML = '<option value="">Chargement...</option>';
    if (!idVille) return;
    fetch(`/api/villes/${idVille}/quartiers`)
        .then(r => r.json())
        .then(quartiers => {
            select.innerHTML = '<option value="">Aucun (optionnel)</option>';
            quartiers.forEach(q => {
                select.innerHTML +=
                    `<option value="${q.id_quartier}">${q.nom_quartier}</option>`;
            });
        });
}

// ═══ PREVIEW PHOTOS ═══
function previewPhotos(input) {
    const preview = document.getElementById('photosPreview');
    preview.innerHTML = '';

    if (input.files.length > 5) {
        alert('Maximum 5 photos autorisées !');
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML += `
                <div style="position:relative">
                    <img src="${e.target.result}"
                         style="width:80px; height:80px;
                                object-fit:cover; border-radius:8px;
                                border:2px solid #4ECDC4">
                    <span style="position:absolute; top:-5px; right:-5px;
                                 background:#4ECDC4; color:white;
                                 border-radius:50%; width:20px; height:20px;
                                 display:flex; align-items:center;
                                 justify-content:center; font-size:11px">
                        ${index + 1}
                    </span>
                </div>`;
        };
        reader.readAsDataURL(file);
    });
}

function loadVillesModifier(idDep, idBien) {
    const select = document.getElementById('villeModifier' + idBien);
    select.innerHTML = '<option value="">Chargement...</option>';
    if (!idDep) return;
    fetch(`/api/departements/${idDep}/villes`)
        .then(r => r.json())
        .then(villes => {
            select.innerHTML = '<option value="">Choisir</option>';
            villes.forEach(v => {
                select.innerHTML +=
                    `<option value="${v.id_ville}">${v.nom_ville}</option>`;
            });
        });
}

function previewLogoModifier(input, id) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreviewModifier' + id).outerHTML =
                `<img src="${e.target.result}"
                      id="logoPreviewModifier${id}"
                      style="width:60px; height:60px;
                             object-fit:cover; border-radius:8px">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
