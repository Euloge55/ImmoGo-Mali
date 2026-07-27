@extends('layouts.admin')
@section('title', 'Gestion des Biens')

@section('styles')
<style>
.section-location { display:none; background:#f0fffe; border-radius:12px; padding:16px; border:1px solid #b2ece8; }
.section-vente    { display:none; background:#fff8f0; border-radius:12px; padding:16px; border:1px solid #ffe0b2; }
.total-box { background:#e8fffe; border-radius:10px; padding:12px 16px; border-left:4px solid #4ECDC4; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Mes Biens</h4>
    <button class="btn fw-semibold" style="background:#4ECDC4;color:white;border-radius:10px"
            data-bs-toggle="modal" data-bs-target="#modalCreerBien">
        <i class="fas fa-plus me-2"></i>Ajouter un bien
    </button>
</div>

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Titre</th><th>Type bien</th><th>Contrat</th>
                        <th>Ville</th><th>Prix</th><th>Statut</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($biens as $bien)
                    <tr>
                        <td class="fw-semibold">{{ $bien->titre_bien }}</td>
                        <td>{{ $bien->typeBien->libelle ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $bien->type_contrat==='location' ? 'bg-info' : 'bg-warning text-dark' }}">
                                {{ $bien->type_contrat==='location' ? 'À louer' : 'À vendre' }}
                            </span>
                        </td>
                        <td>{{ $bien->ville->nom_ville ?? 'N/A' }}</td>
                        <td style="color:#4ECDC4" class="fw-bold">
                            {{ number_format($bien->prix, 0, ',', ' ') }} F
                            @if($bien->type_contrat==='location')<small class="text-muted">/mois</small>@endif
                        </td>
                        <td>
                            <form action="{{ route('admin.biens.statut',$bien->id_bien) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="disponible" {{ $bien->statut=='disponible'?'selected':'' }}>Disponible</option>
                                    <option value="reserve"   {{ $bien->statut=='reserve'?'selected':'' }}>Réservé</option>
                                    <option value="loue"      {{ $bien->statut=='loue'?'selected':'' }}>Loué</option>
                                    <option value="vendu"     {{ $bien->statut=='vendu'?'selected':'' }}>Vendu</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('biens.show',$bien->id_bien) }}" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalModifier{{ $bien->id_bien }}"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.biens.supprimer',$bien->id_bien) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce bien ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun bien pour le moment</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $biens->links() }}
    </div>
</div>

{{-- ═══ MODAL CRÉER BIEN ═══ --}}
<div class="modal fade" id="modalCreerBien" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 rounded-4">
      <div class="modal-header border-0" style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);border-radius:16px 16px 0 0">
        <h5 class="modal-title fw-bold text-white"><i class="fas fa-plus-circle me-2"></i>Ajouter un bien</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form action="{{ route('admin.biens.creer') }}" method="POST" enctype="multipart/form-data" id="formCreer">
          @csrf
          <div class="row g-3">

            {{-- Titre + Type bien --}}
            <div class="col-md-8">
              <label class="form-label fw-semibold">Titre du bien <span class="text-danger">*</span></label>
              <input type="text" name="titre_bien" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Type de bien <span class="text-danger">*</span></label>
              <select name="id_typebien" class="form-select" required>
                <option value="">Choisir</option>
                @foreach($typesBiens as $type)
                  <option value="{{ $type->id_typebien }}">{{ $type->libelle }}</option>
                @endforeach
              </select>
            </div>

            {{-- Description --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
              <textarea name="description_bien" class="form-control" rows="3" required></textarea>
            </div>

            {{-- Type de contrat — CHAMP CLÉ --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Type de contrat <span class="text-danger">*</span></label>
              <select name="type_contrat" class="form-select" id="typeContratCreer" onchange="toggleContratCreer(this.value)" required>
                <option value="">-- Choisir --</option>
                <option value="vente">🏷️ À Vendre</option>
                <option value="location">🏠 À Louer</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Superficie (m²) <span class="text-danger">*</span></label>
              <input type="number" name="superficie" class="form-control" step="0.1" required>
            </div>

            {{-- Section VENTE --}}
            <div class="col-12 section-vente" id="sectionVenteCreer">
              <p class="fw-semibold mb-2" style="color:#e67e22"><i class="fas fa-tag me-2"></i>Prix de vente</p>
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Prix de vente (CFA) <span class="text-danger">*</span></label>
                  <input type="number" name="prix" id="prixVenteCreer" class="form-control" min="0">
                </div>
              </div>
            </div>

            {{-- Section LOCATION --}}
            <div class="col-12 section-location" id="sectionLocationCreer">
              <p class="fw-semibold mb-3" style="color:#4ECDC4"><i class="fas fa-key me-2"></i>Conditions de location</p>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Loyer mensuel (CFA) <span class="text-danger">*</span></label>
                  <input type="number" name="loyer" id="loyerCreer" class="form-control" min="0" oninput="calculerTotalCreer()">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Nombre de mois d'avance <small class="text-muted">(optionnel)</small></label>
                  <input type="number" name="nb_mois_avance" id="nbMoisCreer" class="form-control" min="0" max="24" placeholder="Ex: 3" oninput="calculerTotalCreer()">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Caution eau (CFA) <small class="text-muted">(optionnel)</small></label>
                  <input type="number" name="caution_eau" id="cautionEauCreer" class="form-control" min="0" placeholder="0" oninput="calculerTotalCreer()">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Caution électricité (CFA) <small class="text-muted">(optionnel)</small></label>
                  <input type="number" name="caution_electricite" id="cautionElecCreer" class="form-control" min="0" placeholder="0" oninput="calculerTotalCreer()">
                </div>
                <div class="col-12">
                  <div class="total-box" id="totalBoxCreer" style="display:none">
                    <p class="mb-1 fw-semibold small text-muted">Récapitulatif — Montant total à payer à l'entrée :</p>
                    <div class="row g-2 small" id="detailsTotalCreer"></div>
                    <hr class="my-2">
                    <p class="mb-0 fw-bold">Total : <span id="totalCreer" style="color:#4ECDC4; font-size:1.1rem">0</span> CFA</p>
                  </div>
                </div>
              </div>
            </div>

            {{-- Localisation --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Région <span class="text-danger">*</span></label>
              <select name="id_departement" class="form-select" required onchange="loadVillesModal(this.value)">
                <option value="">Choisir</option>
                @foreach($departements as $dep)
                  <option value="{{ $dep->id_departement }}">{{ $dep->nom_departement }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Ville <span class="text-danger">*</span></label>
              <select name="id_ville" class="form-select" id="villeSelectModal" required onchange="loadQuartiersModal(this.value)">
                <option value="">Choisir d'abord une région</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Quartier</label>
              <select name="id_quartier" class="form-select" id="quartierSelectModal">
                <option value="">Choisir d'abord une ville</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Adresse précise <span class="text-danger">*</span></label>
              <input type="text" name="localisation" class="form-control" required>
            </div>

            {{-- Photos --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Photos <small class="text-muted fw-normal">(5 max, JPG/PNG)</small></label>
              <div class="border rounded-3 p-3" style="border-style:dashed !important;background:#f8f9fa">
                <input type="file" name="photos[]" id="photosInput" class="form-control mb-2"
                       accept="image/jpeg,image/png,image/jpg" multiple onchange="previewPhotos(this)">
                <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
              </div>
            </div>

          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn fw-semibold" style="background:#4ECDC4;color:white">
              <i class="fas fa-save me-2"></i>Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ═══ MODALS MODIFIER BIEN ═══ --}}
@foreach($biens as $bien)
<div class="modal fade" id="modalModifier{{ $bien->id_bien }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 rounded-4">
      <div class="modal-header border-0" style="background:linear-gradient(135deg,#f39c12,#e67e22);border-radius:16px 16px 0 0">
        <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2"></i>Modifier — {{ $bien->titre_bien }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form action="{{ route('admin.biens.modifier',$bien->id_bien) }}" method="POST" enctype="multipart/form-data">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Titre</label>
              <input type="text" name="titre_bien" class="form-control" value="{{ $bien->titre_bien }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Type de bien</label>
              <select name="id_typebien" class="form-select" required>
                @foreach($typesBiens as $type)
                  <option value="{{ $type->id_typebien }}" {{ $bien->id_typebien==$type->id_typebien?'selected':'' }}>{{ $type->libelle }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description_bien" class="form-control" rows="3" required>{{ $bien->description_bien }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Type de contrat</label>
              <select name="type_contrat" class="form-select" id="typeContratMod{{ $bien->id_bien }}"
                      onchange="toggleContratModifier(this.value, {{ $bien->id_bien }})" required>
                <option value="vente"    {{ $bien->type_contrat==='vente'?'selected':'' }}>🏷️ À Vendre</option>
                <option value="location" {{ $bien->type_contrat==='location'?'selected':'' }}>🏠 À Louer</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Superficie (m²)</label>
              <input type="number" name="superficie" class="form-control" value="{{ $bien->superficie }}" step="0.1" required>
            </div>

            {{-- Section Vente modifier --}}
            <div class="col-12 section-vente" id="sectionVenteMod{{ $bien->id_bien }}"
                 style="{{ $bien->type_contrat==='vente' ? 'display:block' : 'display:none' }}; background:#fff8f0; border-radius:12px; padding:16px; border:1px solid #ffe0b2;">
              <p class="fw-semibold mb-2" style="color:#e67e22"><i class="fas fa-tag me-2"></i>Prix de vente</p>
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Prix (CFA)</label>
                <input type="number" name="prix" class="form-control" value="{{ $bien->type_contrat==='vente' ? $bien->prix : '' }}"
                       id="prixVenteMod{{ $bien->id_bien }}" min="0">
              </div>
            </div>

            {{-- Section Location modifier --}}
            <div class="col-12 section-location" id="sectionLocationMod{{ $bien->id_bien }}"
                 style="{{ $bien->type_contrat==='location' ? 'display:block' : 'display:none' }}; background:#f0fffe; border-radius:12px; padding:16px; border:1px solid #b2ece8;">
              <p class="fw-semibold mb-3" style="color:#4ECDC4"><i class="fas fa-key me-2"></i>Conditions de location</p>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Loyer mensuel (CFA)</label>
                  <input type="number" name="loyer" class="form-control" min="0"
                         value="{{ $bien->type_contrat==='location' ? $bien->prix : '' }}"
                         id="loyerMod{{ $bien->id_bien }}" oninput="calculerTotalModifier({{ $bien->id_bien }})">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Mois d'avance</label>
                  <input type="number" name="nb_mois_avance" class="form-control" min="0" max="24"
                         value="{{ $bien->nb_mois_avance }}" id="nbMoisMod{{ $bien->id_bien }}"
                         oninput="calculerTotalModifier({{ $bien->id_bien }})">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Caution eau (CFA)</label>
                  <input type="number" name="caution_eau" class="form-control" min="0"
                         value="{{ $bien->caution_eau }}" id="cautionEauMod{{ $bien->id_bien }}"
                         oninput="calculerTotalModifier({{ $bien->id_bien }})">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Caution électricité (CFA)</label>
                  <input type="number" name="caution_electricite" class="form-control" min="0"
                         value="{{ $bien->caution_electricite }}" id="cautionElecMod{{ $bien->id_bien }}"
                         oninput="calculerTotalModifier({{ $bien->id_bien }})">
                </div>
                <div class="col-12">
                  <div class="total-box" id="totalBoxMod{{ $bien->id_bien }}">
                    <p class="mb-1 fw-semibold small text-muted">Total à l'entrée :</p>
                    <div id="detailsTotalMod{{ $bien->id_bien }}" class="row g-1 small"></div>
                    <hr class="my-2">
                    <p class="mb-0 fw-bold">Total : <span id="totalMod{{ $bien->id_bien }}" style="color:#4ECDC4;font-size:1.1rem">0</span> CFA</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Région</label>
              <select name="id_departement" class="form-select" required onchange="loadVillesModifier(this.value, {{ $bien->id_bien }})">
                <option value="">Choisir</option>
                @foreach($departements as $dep)
                  <option value="{{ $dep->id_departement }}" {{ $bien->id_departement==$dep->id_departement?'selected':'' }}>{{ $dep->nom_departement }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Ville</label>
              <select name="id_ville" class="form-select" required id="villeModifier{{ $bien->id_bien }}" onchange="loadQuartiersModifier(this.value,{{ $bien->id_bien }})">
                @if($bien->ville)
                  <option value="{{ $bien->id_ville }}" selected>{{ $bien->ville->nom_ville }}</option>
                @else
                  <option value="">Choisir une région d'abord</option>
                @endif
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Quartier</label>
              <select name="id_quartier" class="form-select" id="quartierModifier{{ $bien->id_bien }}">
                <option value="">Aucun</option>
                @if($bien->quartier)
                  <option value="{{ $bien->id_quartier }}" selected>{{ $bien->quartier->nom_quartier }}</option>
                @endif
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Adresse précise</label>
              <input type="text" name="localisation" class="form-control" value="{{ $bien->localisation }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Nouvelles photos <small class="text-muted fw-normal">(optionnel)</small></label>
              <input type="file" name="photos[]" class="form-control mb-2" accept="image/*" multiple>
              @if($bien->photos && count($bien->photos)>0)
                <div class="d-flex gap-2 mt-2 flex-wrap">
                  @foreach($bien->photos as $photo)
                    <img src="{{ asset('storage/'.$photo) }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:2px solid #4ECDC4">
                  @endforeach
                </div>
                <small class="text-muted">{{ count($bien->photos) }} photo(s) actuelle(s)</small>
              @endif
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn fw-semibold" style="background:#f39c12;color:white;border-radius:10px">
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
// ─── Toggle formulaire Créer ───────────────────────────
function toggleContratCreer(val) {
    const sv = document.getElementById('sectionVenteCreer');
    const sl = document.getElementById('sectionLocationCreer');
    sv.style.display = val === 'vente' ? 'block' : 'none';
    sl.style.display = val === 'location' ? 'block' : 'none';
    // required dynamiques
    document.getElementById('prixVenteCreer').required = val === 'vente';
    document.getElementById('loyerCreer').required     = val === 'location';
    if (val === 'location') calculerTotalCreer();
}

function calculerTotalCreer() {
    const loyer   = parseFloat(document.getElementById('loyerCreer').value) || 0;
    const mois    = parseInt(document.getElementById('nbMoisCreer').value) || 0;
    const eau     = parseFloat(document.getElementById('cautionEauCreer').value) || 0;
    const elec    = parseFloat(document.getElementById('cautionElecCreer').value) || 0;
    const avance  = loyer * mois;
    const total   = avance + eau + elec;
    const box     = document.getElementById('totalBoxCreer');
    box.style.display = (loyer > 0) ? 'block' : 'none';
    let details = '';
    if (loyer > 0) details += `<div class="col-6">Loyer x1 mois</div><div class="col-6 text-end fw-semibold">${fmt(loyer)} CFA</div>`;
    if (mois > 0)  details += `<div class="col-6">Avance (${mois} mois)</div><div class="col-6 text-end fw-semibold">${fmt(avance)} CFA</div>`;
    if (eau > 0)   details += `<div class="col-6">Caution eau</div><div class="col-6 text-end fw-semibold">${fmt(eau)} CFA</div>`;
    if (elec > 0)  details += `<div class="col-6">Caution électricité</div><div class="col-6 text-end fw-semibold">${fmt(elec)} CFA</div>`;
    document.getElementById('detailsTotalCreer').innerHTML = details;
    document.getElementById('totalCreer').textContent = fmt(total + loyer);
}

// ─── Toggle formulaire Modifier ──────────────────────
function toggleContratModifier(val, id) {
    document.getElementById('sectionVenteMod'+id).style.display    = val==='vente'?'block':'none';
    document.getElementById('sectionLocationMod'+id).style.display = val==='location'?'block':'none';
    if (val==='location') calculerTotalModifier(id);
}

function calculerTotalModifier(id) {
    const loyer  = parseFloat(document.getElementById('loyerMod'+id)?.value) || 0;
    const mois   = parseInt(document.getElementById('nbMoisMod'+id)?.value) || 0;
    const eau    = parseFloat(document.getElementById('cautionEauMod'+id)?.value) || 0;
    const elec   = parseFloat(document.getElementById('cautionElecMod'+id)?.value) || 0;
    const avance = loyer * mois;
    let details = '';
    if (loyer > 0) details += `<div class="col-6">Loyer x1 mois</div><div class="col-6 text-end fw-semibold">${fmt(loyer)} CFA</div>`;
    if (mois > 0)  details += `<div class="col-6">Avance (${mois} mois)</div><div class="col-6 text-end fw-semibold">${fmt(avance)} CFA</div>`;
    if (eau > 0)   details += `<div class="col-6">Caution eau</div><div class="col-6 text-end fw-semibold">${fmt(eau)} CFA</div>`;
    if (elec > 0)  details += `<div class="col-6">Caution électricité</div><div class="col-6 text-end fw-semibold">${fmt(elec)} CFA</div>`;
    document.getElementById('detailsTotalMod'+id).innerHTML = details;
    document.getElementById('totalMod'+id).textContent = fmt(loyer + avance + eau + elec);
}

function fmt(n) { return Math.round(n).toLocaleString('fr-FR'); }

// ─── Localisation cascade ─────────────────────────────
function loadVillesModal(idDep) {
    const s = document.getElementById('villeSelectModal');
    s.innerHTML = '<option value="">Chargement...</option>';
    if (!idDep) return;
    fetch(`/api/departements/${idDep}/villes`).then(r=>r.json()).then(v=>{
        s.innerHTML = '<option value="">Choisir</option>';
        v.forEach(x => s.innerHTML += `<option value="${x.id_ville}">${x.nom_ville}</option>`);
        document.getElementById('quartierSelectModal').innerHTML = '<option value="">Choisir d\'abord une ville</option>';
    });
}
function loadQuartiersModal(idVille) {
    const s = document.getElementById('quartierSelectModal');
    s.innerHTML = '<option value="">Chargement...</option>';
    if (!idVille) return;
    fetch(`/api/villes/${idVille}/quartiers`).then(r=>r.json()).then(q=>{
        s.innerHTML = '<option value="">(optionnel)</option>';
        q.forEach(x => s.innerHTML += `<option value="${x.id_quartier}">${x.nom_quartier}</option>`);
    });
}
function loadVillesModifier(idDep, id) {
    const s = document.getElementById('villeModifier'+id);
    s.innerHTML = '<option value="">Chargement...</option>';
    if (!idDep) return;
    fetch(`/api/departements/${idDep}/villes`).then(r=>r.json()).then(v=>{
        s.innerHTML = '<option value="">Choisir</option>';
        v.forEach(x => s.innerHTML += `<option value="${x.id_ville}">${x.nom_ville}</option>`);
        document.getElementById('quartierModifier'+id).innerHTML = '<option value="">Aucun</option>';
    });
}
function loadQuartiersModifier(idVille, id) {
    const s = document.getElementById('quartierModifier'+id);
    s.innerHTML = '<option value="">Chargement...</option>';
    if (!idVille) return;
    fetch(`/api/villes/${idVille}/quartiers`).then(r=>r.json()).then(q=>{
        s.innerHTML = '<option value="">Aucun</option>';
        q.forEach(x => s.innerHTML += `<option value="${x.id_quartier}">${x.nom_quartier}</option>`);
    });
}
function previewPhotos(input) {
    const preview = document.getElementById('photosPreview');
    preview.innerHTML = '';
    if (input.files.length > 5) { alert('Maximum 5 photos !'); input.value=''; return; }
    Array.from(input.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => preview.innerHTML += `<div style="position:relative"><img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #4ECDC4"><span style="position:absolute;top:-5px;right:-5px;background:#4ECDC4;color:white;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:11px">${i+1}</span></div>`;
        reader.readAsDataURL(file);
    });
}

// Initialiser les sections modifier au chargement
document.addEventListener('DOMContentLoaded', () => {
    @foreach($biens as $bien)
    calculerTotalModifier({{ $bien->id_bien }});
    @endforeach
});
</script>
@endsection
