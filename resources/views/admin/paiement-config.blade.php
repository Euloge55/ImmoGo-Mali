@extends('layouts.admin')
@section('title', 'Configuration Paiement CinetPay')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- EN-TÊTE -->
        <div class="card border-0 rounded-4 shadow-sm mb-4"
             style="background:linear-gradient(135deg,#2C3E50,#3498db)">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-4"
                         style="width:60px; height:60px; background:rgba(78,205,196,0.3)">
                        <i class="fas fa-credit-card fa-2x" style="color:#4ECDC4"></i>
                    </div>
                    <div>
                        <h4 class="text-white fw-bold mb-1">Configuration CinetPay</h4>
                        <p class="text-white opacity-75 mb-0">
                            Configurez les clés de paiement de votre agence
                            <strong>{{ $agence->nom_agence }}</strong>
                        </p>
                        <span class="badge mt-1" style="background:#4ECDC4">
                            <i class="fas fa-lock me-1"></i>Admin principal uniquement
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERTE INFO -->
        <div class="alert border-0 rounded-3 mb-4"
             style="background:#e8fffe; border-left:4px solid #4ECDC4 !important">
            <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
            <strong>Comment obtenir vos clés CinetPay ?</strong>
            <ol class="mb-0 mt-2 ps-3">
                <li>Créez un compte sur <a href="https://www.cinetpay.com" target="_blank" style="color:#4ECDC4">cinetpay.com</a></li>
                <li>Accédez à votre tableau de bord → <strong>Mes Services</strong></li>
                <li>Créez un service pour votre agence et récupérez le <strong>Site ID</strong> et la <strong>Clé API</strong></li>
                <li>Utilisez l'environnement <strong>TEST</strong> pour vos tests, puis <strong>PROD</strong> en production</li>
            </ol>
        </div>

        <!-- FORMULAIRE CONFIG -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-key me-2" style="color:#4ECDC4"></i>
                    Clés d'intégration
                </h5>

                <form action="{{ route('admin.paiement.config.save') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Site ID <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#e8fffe; border-color:#4ECDC4">
                                <i class="fas fa-id-badge" style="color:#4ECDC4"></i>
                            </span>
                            <input type="text"
                                   name="cinetpay_site_id"
                                   class="form-control @error('cinetpay_site_id') is-invalid @enderror"
                                   value="{{ old('cinetpay_site_id', $agence->cinetpay_site_id) }}"
                                   placeholder="Ex: 1234567"
                                   required>
                            @error('cinetpay_site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Le Site ID fourni par CinetPay pour votre service</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Clé API <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#e8fffe; border-color:#4ECDC4">
                                <i class="fas fa-key" style="color:#4ECDC4"></i>
                            </span>
                            <input type="password"
                                   name="cinetpay_api_key"
                                   id="apiKeyInput"
                                   class="form-control @error('cinetpay_api_key') is-invalid @enderror"
                                   value="{{ old('cinetpay_api_key', $agence->cinetpay_api_key) }}"
                                   placeholder="Votre clé API secrète CinetPay"
                                   required>
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="toggleApiKey()">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                            @error('cinetpay_api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Gardez cette clé confidentielle — ne la partagez jamais</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Environnement <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center cursor-pointer env-card
                                            {{ old('cinetpay_env', $agence->cinetpay_env ?? 'TEST') === 'TEST' ? 'env-active' : '' }}"
                                     id="envTest" onclick="selectEnv('TEST')"
                                     style="cursor:pointer; transition:all 0.2s">
                                    <input type="radio" name="cinetpay_env" value="TEST"
                                           id="radioTest" class="d-none"
                                           {{ old('cinetpay_env', $agence->cinetpay_env ?? 'TEST') === 'TEST' ? 'checked' : '' }}>
                                    <i class="fas fa-flask fa-2x mb-2" style="color:#f39c12"></i>
                                    <p class="fw-bold mb-1">TEST</p>
                                    <small class="text-muted">Pour les tests — aucun vrai paiement</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center cursor-pointer env-card
                                            {{ old('cinetpay_env', $agence->cinetpay_env) === 'PROD' ? 'env-active' : '' }}"
                                     id="envProd" onclick="selectEnv('PROD')"
                                     style="cursor:pointer; transition:all 0.2s">
                                    <input type="radio" name="cinetpay_env" value="PROD"
                                           id="radioProd" class="d-none"
                                           {{ old('cinetpay_env', $agence->cinetpay_env) === 'PROD' ? 'checked' : '' }}>
                                    <i class="fas fa-check-circle fa-2x mb-2" style="color:#2ecc71"></i>
                                    <p class="fw-bold mb-1">PRODUCTION</p>
                                    <small class="text-muted">Paiements réels — activez en production</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STATUT ACTUEL -->
                    @if($agence->cinetpay_site_id && $agence->cinetpay_api_key)
                        <div class="alert border-0 rounded-3 mb-4"
                             style="background:#d4edda; border-left:4px solid #2ecc71 !important">
                            <i class="fas fa-check-circle me-2 text-success"></i>
                            <strong>CinetPay est configuré</strong> pour cette agence.
                            Environnement actuel :
                            <span class="badge {{ $agence->cinetpay_env === 'PROD' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $agence->cinetpay_env ?? 'TEST' }}
                            </span>
                        </div>
                    @else
                        <div class="alert border-0 rounded-3 mb-4"
                             style="background:#fff3cd; border-left:4px solid #f39c12 !important">
                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                            <strong>Paiement non configuré.</strong>
                            Les clients ne pourront pas payer en ligne tant que vous n'avez pas sauvegardé les clés.
                        </div>
                    @endif

                    <div class="d-flex gap-3">
                        <button type="submit"
                                class="btn fw-semibold px-4 py-2"
                                style="background:#4ECDC4; color:white; border-radius:10px">
                            <i class="fas fa-save me-2"></i>Sauvegarder la configuration
                        </button>
                        <a href="{{ route('admin.dashboard') }}"
                           class="btn btn-outline-secondary px-4 py-2"
                           style="border-radius:10px">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- INFOS CINETPAY -->
        <div class="card border-0 rounded-4 shadow-sm mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-mobile-alt me-2" style="color:#4ECDC4"></i>
                    Moyens de paiement acceptés via CinetPay au Mali
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color:#FF6900"></i>
                            <p class="fw-semibold mb-0 small">Orange Money</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color:#00A651"></i>
                            <p class="fw-semibold mb-0 small">Moov Money</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-credit-card fa-2x mb-2" style="color:#4ECDC4"></i>
                            <p class="fw-semibold mb-0 small">Visa / Mastercard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('styles')
<style>
    .env-active {
        border-color: #4ECDC4 !important;
        background: #e8fffe;
    }
</style>
@endsection

@section('scripts')
<script>
function toggleApiKey() {
    const input = document.getElementById('apiKeyInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function selectEnv(env) {
    document.getElementById('radioTest').checked = env === 'TEST';
    document.getElementById('radioProd').checked = env === 'PROD';
    document.getElementById('envTest').classList.toggle('env-active', env === 'TEST');
    document.getElementById('envProd').classList.toggle('env-active', env === 'PROD');
}
</script>
@endsection
