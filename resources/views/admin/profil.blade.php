@extends('layouts.admin')
@section('title', 'Mon Profil')

@section('styles')
<style>
    .avatar-circle {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg,#4ECDC4,#2C3E50);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 700; color: white;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- En-tête profil --}}
        <div class="card border-0 rounded-4 shadow-sm mb-4"
             style="background:linear-gradient(135deg,#2C3E50,#3498db)">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="avatar-circle me-4 flex-shrink-0">
                    {{ strtoupper(substr($admin->prenom_admin,0,1)) }}{{ strtoupper(substr($admin->nom_admin,0,1)) }}
                </div>
                <div>
                    <h4 class="text-white fw-bold mb-1">{{ $admin->prenom_admin }} {{ $admin->nom_admin }}</h4>
                    <p class="text-white opacity-75 mb-1">{{ $admin->email }}</p>
                    @if($admin->est_principal)
                        <span class="badge" style="background:#4ECDC4">
                            <i class="fas fa-crown me-1"></i>Admin Principal
                        </span>
                    @else
                        <span class="badge bg-secondary">Admin Assistant</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modifier informations --}}
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-user me-2" style="color:#4ECDC4"></i>
                    Informations personnelles
                </h5>

                <form action="{{ route('admin.profil.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="nom_admin" class="form-control"
                                   value="{{ old('nom_admin', $admin->nom_admin) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom_admin" class="form-control"
                                   value="{{ old('prenom_admin', $admin->prenom_admin) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $admin->email) }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn fw-semibold mt-3"
                            style="background:#4ECDC4;color:white;border-radius:10px">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>

        {{-- Modifier mot de passe --}}
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-lock me-2" style="color:#f39c12"></i>
                    Changer le mot de passe
                </h5>

                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ancien mot de passe</label>
                            <input type="password" name="ancien_mot_de_passe" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" name="nouveau_mot_de_passe" class="form-control"
                                   placeholder="Minimum 6 caractères" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmer</label>
                            <input type="password" name="nouveau_mot_de_passe_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn fw-semibold mt-3"
                            style="background:#f39c12;color:white;border-radius:10px">
                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
