@extends('layouts.app')
@section('title', 'Mes Réservations')

@section('content')
<div class="container py-5">

    <h3 class="fw-bold mb-4">
        <i class="fas fa-calendar-check me-2" style="color:#4ECDC4"></i>
        Mes Réservations
    </h3>

    @if($contrats->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Aucune réservation</h4>
            <a href="{{ route('biens.index') }}"
               class="btn fw-semibold mt-3"
               style="background:#4ECDC4; color:white; border-radius:10px">
                Voir les biens
            </a>
        </div>
    @else
        @foreach($contrats as $contrat)
        @php
            $montantTotal = $contrat->type_contrat == 'location'
                ? ($contrat->location->montant_total_location ?? 0)
                : ($contrat->vente->montant_total_vente ?? 0);
            $totalPaye = $contrat->paiements->sum('montant');
            $solde     = max(0, $montantTotal - $totalPaye);
        @endphp
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body p-4">

                <!-- EN-TÊTE CONTRAT -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="fas fa-home me-2" style="color:#4ECDC4"></i>
                            {{ $contrat->bien->titre_bien ?? 'Bien supprimé' }}
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1" style="color:#4ECDC4"></i>
                            {{ $contrat->bien->ville->nom_ville ?? 'N/A' }}
                            @if($contrat->bien->agence)
                                &nbsp;•&nbsp;
                                <i class="fas fa-building me-1"></i>
                                {{ $contrat->bien->agence->nom_agence }}
                            @endif
                        </small>
                    </div>
                    <div class="text-end">
                        @if($contrat->statut_contrat == 'confirme')
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Confirmé
                            </span>
                        @elseif($contrat->statut_contrat == 'en_attente')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i>En attente
                            </span>
                        @else
                            <span class="badge bg-danger">Annulé</span>
                        @endif
                        <br>
                        <small class="text-muted">
                            {{ $contrat->type_contrat === 'location' ? 'Location' : 'Vente' }}
                        </small>
                    </div>
                </div>

                <!-- MONTANTS -->
                <div class="row g-3 mb-3">
                    <div class="col-4 text-center">
                        <div class="p-2 rounded-3" style="background:#f8f9fa">
                            <small class="text-muted d-block">Montant total</small>
                            <strong class="small">
                                {{ number_format($montantTotal, 0, ',', ' ') }} CFA
                            </strong>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="p-2 rounded-3" style="background:#d4edda">
                            <small class="text-muted d-block">Payé</small>
                            <strong class="small" style="color:#2ecc71">
                                {{ number_format($totalPaye, 0, ',', ' ') }} CFA
                            </strong>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="p-2 rounded-3"
                             style="background:{{ $solde > 0 ? '#f8d7da' : '#d4edda' }}">
                            <small class="text-muted d-block">Solde restant</small>
                            <strong class="small" style="color:{{ $solde > 0 ? '#e74c3c' : '#2ecc71' }}">
                                {{ number_format($solde, 0, ',', ' ') }} CFA
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- HISTORIQUE PAIEMENTS -->
                @if($contrat->paiements->isNotEmpty())
                <div class="border-top pt-3 mb-3">
                    <p class="fw-semibold small mb-2">
                        <i class="fas fa-history me-1" style="color:#4ECDC4"></i>
                        Historique des paiements
                    </p>
                    @foreach($contrat->paiements as $paiement)
                    <div class="d-flex justify-content-between align-items-center
                                py-1 px-2 rounded mb-1" style="background:#f8f9fa">
                        <div>
                            <small class="fw-semibold">
                                {{ ucfirst($paiement->type_paiement) }}
                            </small>
                            <small class="text-muted ms-2">
                                {{ $paiement->date_paiement->format('d/m/Y') }}
                            </small>
                        </div>
                        <div class="text-end">
                            <small class="fw-bold" style="color:#2ecc71">
                                +{{ number_format($paiement->montant, 0, ',', ' ') }} CFA
                            </small>
                            @if($paiement->reference)
                            <br><small class="text-muted" style="font-size:10px">
                                {{ $paiement->reference }}
                            </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- BOUTON PAYER SOLDE (CinetPay) -->
                @if($contrat->statut_contrat == 'en_attente' && $solde > 0)
                <div class="border-top pt-3">
                    <form action="{{ route('cinetpay.solde') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_contrat"
                               value="{{ $contrat->id_contrat }}">
                        <button type="submit"
                                class="btn fw-semibold w-100"
                                style="background:#4ECDC4; color:white; border-radius:10px">
                            <i class="fas fa-credit-card me-2"></i>
                            Payer le solde restant
                            ({{ number_format($solde, 0, ',', ' ') }} CFA)
                            via CinetPay
                        </button>
                    </form>
                </div>
                @endif

            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
