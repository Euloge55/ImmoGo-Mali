@extends('layouts.admin')
@section('title', 'Configuration Paiement FedaPay')

@section('styles')
<style>
    .env-active { border-color:#4ECDC4 !important; background:#e8fffe; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- EN-TÊTE -->
        <div class="card border-0 rounded-4 shadow-sm mb-4"
             style="background:linear-gradient(135deg,#2C3E50,#3498db)">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-4"
                     style="width:60px;height:60px;background:rgba(78,205,196,0.3)">
                    <i class="fas fa-credit-card fa-2x" style="color:#4ECDC4"></i>
                </div>
                <div>
                    <h4 class="text-white fw-bold mb-1">Configuration FedaPay</h4>
                    <p class="text-white opacity-75 mb-0">
                        Clés de paiement pour l'agence <strong>{{ $agence->nom_agence }}</strong>
                    </p>
                    <span class="badge mt-1" style="background:#4ECDC4">
                        <i class="fas fa-lock me-1"></i>Admin principal uniquement
                    </span>
                </div>
            </div>
        </div>

        <!-- GUIDE -->
        <div class="alert border-0 rounded-3 mb-4"
             style="background:#e8fffe; border-left:4px solid #4ECDC4 !important">
            <i class="fas fa-info-circle me-2" style="color:#4ECDC4"></i>
            <strong>Comment obtenir votre clé FedaPay ?</strong>
            <ol class="mb-0 mt-2 ps-3 small">
                <li>Créez un compte sur <a href="https://fedapay.com" target="_blank" style="color:#4ECDC4">fedapay.com</a></li>
                <li>Allez dans votre tableau de bord → <strong>API Keys</strong></li>
                <li>Copiez votre <strong>Clé secrète</strong> (commence par <code>sk_sandbox_</code> pour les tests ou <code>sk_live_</code> en production)</li>
            </ol>
        </div>

        <!-- FORMULAIRE -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-key me-2" style="color:#4ECDC4"></i>
                    Identifiants FedaPay
                </h5>

                <form action="{{ route('admin.paiement.config.save') }}" method="POST">
                    @csrf

                    {{-- Clé secrète --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Clé secrète API <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#e8fffe;border-color:#4ECDC4">
                                <i class="fas fa-key" style="color:#4ECDC4"></i>
                            </span>
                            <input type="password"
                                   name="fedapay_secret_key"
                                   id="apiKeyInput"
                                   class="form-control @error('fedapay_secret_key') is-invalid @enderror"
                                   value="{{ old('fedapay_secret_key', $agence->fedapay_secret_key) }}"
                                   placeholder="sk_sandbox_... ou sk_live_..."
                                   required>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleKey()">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                            @error('fedapay_secret_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">
                            Clé secrète depuis votre dashboard FedaPay → <strong>Développeurs → Clés API</strong>
                        </small>
                    </div>

                    {{-- Environnement --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Environnement <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center env-card
                                     {{ old('fedapay_env', $agence->fedapay_env ?? 'sandbox') === 'sandbox' ? 'env-active' : '' }}"
                                     id="envSandbox" onclick="selectEnv('sandbox')" style="cursor:pointer">
                                    <input type="radio" name="fedapay_env" value="sandbox" id="radioSandbox" class="d-none"
                                           {{ old('fedapay_env', $agence->fedapay_env ?? 'sandbox') === 'sandbox' ? 'checked' : '' }}>
                                    <i class="fas fa-flask fa-2x mb-2" style="color:#f39c12"></i>
                                    <p class="fw-bold mb-1">Sandbox</p>
                                    <small class="text-muted">Tests — utilisez <code>sk_sandbox_...</code></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center env-card
                                     {{ old('fedapay_env', $agence->fedapay_env) === 'live' ? 'env-active' : '' }}"
                                     id="envLive" onclick="selectEnv('live')" style="cursor:pointer">
                                    <input type="radio" name="fedapay_env" value="live" id="radioLive" class="d-none"
                                           {{ old('fedapay_env', $agence->fedapay_env) === 'live' ? 'checked' : '' }}>
                                    <i class="fas fa-check-circle fa-2x mb-2" style="color:#2ecc71"></i>
                                    <p class="fw-bold mb-1">Live</p>
                                    <small class="text-muted">Production — utilisez <code>sk_live_...</code></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statut actuel --}}
                    @if($agence->fedapay_secret_key)
                    <div class="alert border-0 rounded-3 mb-4"
                         style="background:#d4edda;border-left:4px solid #2ecc71 !important">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        <strong>FedaPay configuré</strong> — Environnement :
                        <span class="badge {{ $agence->fedapay_env === 'live' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $agence->fedapay_env ?? 'sandbox' }}
                        </span>
                    </div>
                    @else
                    <div class="alert border-0 rounded-3 mb-4"
                         style="background:#fff3cd;border-left:4px solid #f39c12 !important">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        <strong>Non configuré</strong> — Les clients ne pourront pas payer en ligne.
                    </div>
                    @endif

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn fw-semibold px-4 py-2"
                                style="background:#4ECDC4;color:white;border-radius:10px">
                            <i class="fas fa-save me-2"></i>Sauvegarder
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2"
                           style="border-radius:10px">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- MOYENS DE PAIEMENT -->
        <div class="card border-0 rounded-4 shadow-sm mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-mobile-alt me-2" style="color:#4ECDC4"></i>
                    Moyens de paiement disponibles via FedaPay
                </h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color:#FF6900"></i>
                            <p class="fw-semibold mb-0 small">Orange Money</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color:#00A651"></i>
                            <p class="fw-semibold mb-0 small">Moov Money</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:#f8f9fa">
                            <i class="fas fa-mobile-alt fa-2x mb-2" style="color:#FFD700"></i>
                            <p class="fw-semibold mb-0 small">Wave</p>
                        </div>
                    </div>
                    <div class="col-md-3">
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

@section('scripts')
<script>
function toggleKey() {
    const input = document.getElementById('apiKeyInput');
    const icon  = document.getElementById('eyeIcon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
function selectEnv(env) {
    document.getElementById('radioSandbox').checked = env === 'sandbox';
    document.getElementById('radioLive').checked    = env === 'live';
    document.getElementById('envSandbox').classList.toggle('env-active', env === 'sandbox');
    document.getElementById('envLive').classList.toggle('env-active', env === 'live');
}
</script>
@endsection
