@extends('layouts.app')
@section('title', 'Inscription')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">

                    <!-- TITRE -->
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x mb-3" style="color: #4ECDC4"></i>
                        <h2 class="fw-bold">Créer un compte</h2>
                        <p class="text-muted">Rejoignez IMMO-Mali dès maintenant</p>
                    </div>

                    <!-- FORMULAIRE -->
                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nom</label>
                                <input
                                    type="text"
                                    name="nom_client"
                                    class="form-control @error('nom_client') is-invalid @enderror"
                                    value="{{ old('nom_client') }}"
                                    placeholder="Votre nom"
                                >
                                @error('nom_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input
                                    type="text"
                                    name="prenom_client"
                                    class="form-control @error('prenom_client') is-invalid @enderror"
                                    value="{{ old('prenom_client') }}"
                                    placeholder="Votre prénom"
                                >
                                @error('prenom_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="votre@email.com"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input
                                type="text"
                                name="tel_client"
                                class="form-control @error('tel_client') is-invalid @enderror"
                                value="{{ old('tel_client') }}"
                                placeholder="+223 XX XX XX XX"
                            >
                            @error('tel_client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <input
                                type="password"
                                name="mot_de_passe"
                                class="form-control @error('mot_de_passe') is-invalid @enderror"
                                placeholder="Minimum 6 caractères"
                            >
                            @error('mot_de_passe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <input
                                type="password"
                                name="mot_de_passe_confirmation"
                                class="form-control"
                                placeholder="Répétez le mot de passe"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="fas fa-user-plus me-2"></i>Créer mon compte
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Déjà un compte ?
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: #4ECDC4">
                                Se connecter
                            </a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection