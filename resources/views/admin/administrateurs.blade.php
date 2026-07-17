@extends('layouts.admin')
@section('title', 'Gestion des Administrateurs')

@section('content')

@if(!session('admin')->est_principal)
    <div class="alert border-0 rounded-3"
         style="background:#fff3cd; border-left:4px solid #f39c12 !important">
        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
        Seul l'administrateur principal peut gérer les autres administrateurs.
    </div>
@else

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Administrateurs de l'agence</h4>
        <p class="text-muted mb-0">Gérez l'équipe qui vous aide à administrer vos biens</p>
    </div>
    <button class="btn fw-semibold"
            style="background:#4ECDC4; color:white; border-radius:10px"
            data-bs-toggle="modal" data-bs-target="#modalCreerAdmin">
        <i class="fas fa-user-plus me-2"></i>Ajouter un administrateur
    </button>
</div>

<!-- LISTE DES ADMINS -->
<div class="row g-4 mb-4">
    @forelse($admins as $admin)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:50px; height:50px;
                                    background:{{ $admin->est_principal ? '#4ECDC4' : '#e8f4fd' }};
                                    color:{{ $admin->est_principal ? 'white' : '#2C3E50' }}">
                            <i class="fas fa-user" style="font-size:18px"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0">
                                {{ $admin->prenom_admin }} {{ $admin->nom_admin }}
                            </p>
                            @if($admin->est_principal)
                                <span class="badge"
                                      style="background:#4ECDC4; font-size:10px">
                                    <i class="fas fa-crown me-1"></i>Principal
                                </span>
                            @else
                                <span class="badge bg-secondary" style="font-size:10px">
                                    Assistant
                                </span>
                            @endif
                        </div>
                    </div>
                    @if(!$admin->est_principal)
                        <form action="{{ route('admin.administrateurs.supprimer', $admin->id_admin) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer {{ $admin->prenom_admin }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    style="border-radius:8px">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="border-top pt-3">
                    <p class="text-muted small mb-1">
                        <i class="fas fa-envelope me-2" style="color:#4ECDC4"></i>
                        {{ $admin->email }}
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-calendar me-2" style="color:#4ECDC4"></i>
                        Depuis le {{ $admin->created_at->format('d/m/Y') }}
                    </p>
                </div>

                @if($admin->id_admin === session('admin')->id_admin)
                    <div class="mt-3">
                        <span class="badge" style="background:#e8fffe; color:#4ECDC4;
                              border:1px solid #4ECDC4; font-size:10px">
                            <i class="fas fa-user-circle me-1"></i>Vous
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-users fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Aucun administrateur</h4>
    </div>
    @endforelse
</div>

<!-- EXPLICATION RÔLES -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
            Rôles et permissions
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background:#e8fffe">
                    <p class="fw-bold mb-1">
                        <i class="fas fa-crown me-2" style="color:#4ECDC4"></i>
                        Administrateur Principal
                    </p>
                    <ul class="text-muted small mb-0">
                        <li>Gérer les biens (créer, modifier, supprimer)</li>
                        <li>Voir et gérer les réservations</li>
                        <li>Créer et supprimer des administrateurs assistants</li>
                        <li>Configurer les clés de paiement CinetPay</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background:#f8f9fa">
                    <p class="fw-bold mb-1">
                        <i class="fas fa-user me-2 text-muted"></i>
                        Administrateur Assistant
                    </p>
                    <ul class="text-muted small mb-0">
                        <li>Gérer les biens (créer, modifier, supprimer)</li>
                        <li>Voir et gérer les réservations</li>
                        <li>Pas d'accès à la configuration paiement</li>
                        <li>Ne peut pas créer d'autres administrateurs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CRÉER ADMIN -->
<div class="modal fade" id="modalCreerAdmin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0"
                 style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);
                        border-radius:16px 16px 0 0">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-user-plus me-2"></i>
                    Ajouter un administrateur assistant
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert border-0 rounded-3 mb-4"
                     style="background:#e8fffe">
                    <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
                    Cet administrateur pourra gérer les biens et les réservations,
                    mais n'aura pas accès à la configuration du paiement.
                </div>

                <form action="{{ route('admin.administrateurs.creer') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="nom_admin"
                                   class="form-control @error('nom_admin') is-invalid @enderror"
                                   value="{{ old('nom_admin') }}"
                                   placeholder="Nom de famille"
                                   required>
                            @error('nom_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom_admin"
                                   class="form-control @error('prenom_admin') is-invalid @enderror"
                                   value="{{ old('prenom_admin') }}"
                                   placeholder="Prénom"
                                   required>
                            @error('prenom_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="email@agence.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <input type="password" name="mot_de_passe"
                                   class="form-control @error('mot_de_passe') is-invalid @enderror"
                                   placeholder="Minimum 6 caractères"
                                   required>
                            @error('mot_de_passe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn fw-semibold"
                                style="background:#4ECDC4; color:white; border-radius:10px">
                            <i class="fas fa-user-plus me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalCreerAdmin')).show();
    });
</script>
@endif

@endif
@endsection
