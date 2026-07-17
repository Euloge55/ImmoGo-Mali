@extends('layouts.admin')
@section('title', 'Réservations')

@section('styles')
<style>
    .contrat-card { border:none; border-radius:14px; box-shadow:0 3px 15px rgba(0,0,0,0.07); transition:transform .2s; }
    .contrat-card:hover { transform:translateY(-2px); }
    .progress-pay { height:6px; border-radius:3px; background:#e9ecef; }
    .progress-pay-bar { height:100%; border-radius:3px; background:#4ECDC4; }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Gestion des Réservations</h4>
        <p class="text-muted mb-0">{{ $contrats->total() }} réservation(s) au total</p>
    </div>
    {{-- Filtres rapides --}}
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reservations') }}"
           class="btn btn-sm {{ !request('statut') ? 'btn-dark' : 'btn-outline-secondary' }}"
           style="border-radius:20px">Toutes</a>
        <a href="{{ route('admin.reservations') }}?statut=en_attente"
           class="btn btn-sm {{ request('statut')=='en_attente' ? 'btn-warning' : 'btn-outline-warning' }}"
           style="border-radius:20px">En attente</a>
        <a href="{{ route('admin.reservations') }}?statut=confirme"
           class="btn btn-sm {{ request('statut')=='confirme' ? 'btn-success' : 'btn-outline-success' }}"
           style="border-radius:20px">Confirmées</a>
    </div>
</div>

@if($contrats->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Aucune réservation</h5>
    </div>
@else
<div class="row g-3">
    @foreach($contrats as $contrat)
    @php
        $montantTotal = $contrat->type_contrat === 'location'
            ? ($contrat->location->montant_total_location ?? 0)
            : ($contrat->vente->montant_total_vente ?? 0);
        $totalPaye = $contrat->paiements->sum('montant');
        $solde     = max(0, $montantTotal - $totalPaye);
        $pct       = $montantTotal > 0 ? min(100, round($totalPaye / $montantTotal * 100)) : 0;
    @endphp
    <div class="col-12">
        <div class="card contrat-card p-4">
            <div class="row align-items-center g-3">

                {{-- INFOS BIEN + CLIENT --}}
                <div class="col-lg-4">
                    <div class="d-flex align-items-start">
                        <div class="rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:#e8fffe">
                            <i class="fas fa-home" style="color:#4ECDC4"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0 small">{{ $contrat->bien->titre_bien ?? 'N/A' }}</p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1" style="color:#4ECDC4"></i>
                                {{ $contrat->bien->ville->nom_ville ?? 'N/A' }}
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                {{ $contrat->client->prenom_client ?? '' }}
                                {{ $contrat->client->nom_client ?? 'N/A' }}
                            </small>
                            @if($contrat->client->tel_client ?? false)
                            <br><small class="text-muted">
                                <i class="fas fa-phone me-1"></i>{{ $contrat->client->tel_client }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- MONTANTS + BARRE --}}
                <div class="col-lg-3">
                    <div class="mb-1 d-flex justify-content-between">
                        <small class="text-muted">Progression paiement</small>
                        <small class="fw-bold">{{ $pct }}%</small>
                    </div>
                    <div class="progress-pay mb-2">
                        <div class="progress-pay-bar" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <small class="text-muted d-block">Total</small>
                            <span class="fw-bold small">{{ number_format($montantTotal, 0, ',', ' ') }} F</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Payé</small>
                            <span class="fw-bold small" style="color:#2ecc71">{{ number_format($totalPaye, 0, ',', ' ') }} F</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Reste</small>
                            <span class="fw-bold small" style="color:{{ $solde>0?'#e74c3c':'#2ecc71' }}">{{ number_format($solde, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                </div>

                {{-- TYPE + DATE --}}
                <div class="col-lg-2 text-center">
                    <span class="badge mb-2 d-block" style="background:#e8f4fd;color:#3498db">
                        {{ $contrat->type_contrat === 'location' ? 'Location' : 'Vente' }}
                    </span>
                    <small class="text-muted">{{ $contrat->created_at->format('d/m/Y') }}</small>
                </div>

                {{-- STATUT + ACTIONS --}}
                <div class="col-lg-3 text-end">
                    @if($contrat->statut_contrat === 'confirme')
                        <span class="badge bg-success mb-2 d-inline-block">
                            <i class="fas fa-check me-1"></i>Confirmé
                        </span>
                    @elseif($contrat->statut_contrat === 'en_attente')
                        <span class="badge bg-warning text-dark mb-2 d-inline-block">
                            <i class="fas fa-clock me-1"></i>En attente
                        </span>
                    @else
                        <span class="badge bg-danger mb-2 d-inline-block">Annulé</span>
                    @endif
                    <br>

                    {{-- Actions --}}
                    <div class="d-flex gap-1 justify-content-end mt-1 flex-wrap">
                        {{-- Confirmer manuellement --}}
                        @if($contrat->statut_contrat === 'en_attente')
                        <form action="{{ route('admin.reservations.confirmer', $contrat->id_contrat) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm btn-success"
                                    style="border-radius:8px"
                                    onclick="return confirm('Confirmer cette réservation ?')">
                                <i class="fas fa-check"></i> Confirmer
                            </button>
                        </form>
                        <form action="{{ route('admin.reservations.annuler', $contrat->id_contrat) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    style="border-radius:8px"
                                    onclick="return confirm('Annuler cette réservation ?')">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        </form>
                        @endif

                        {{-- Voir détail --}}
                        <button class="btn btn-sm btn-outline-secondary"
                                style="border-radius:8px"
                                data-bs-toggle="modal"
                                data-bs-target="#modalContrat{{ $contrat->id_contrat }}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL DÉTAIL --}}
    <div class="modal fade" id="modalContrat{{ $contrat->id_contrat }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-0"
                     style="background:linear-gradient(135deg,#4ECDC4,#2C3E50);border-radius:16px 16px 0 0">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-file-contract me-2"></i>
                        Contrat #{{ $contrat->id_contrat }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="fas fa-user me-2" style="color:#4ECDC4"></i>Client</h6>
                            <p class="mb-1"><strong>{{ $contrat->client->prenom_client ?? '' }} {{ $contrat->client->nom_client ?? 'N/A' }}</strong></p>
                            <p class="text-muted small mb-1"><i class="fas fa-envelope me-1"></i>{{ $contrat->client->email ?? 'N/A' }}</p>
                            <p class="text-muted small mb-0"><i class="fas fa-phone me-1"></i>{{ $contrat->client->tel_client ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="fas fa-home me-2" style="color:#4ECDC4"></i>Bien</h6>
                            <p class="mb-1"><strong>{{ $contrat->bien->titre_bien ?? 'N/A' }}</strong></p>
                            <p class="text-muted small mb-1">{{ $contrat->bien->ville->nom_ville ?? '' }} — {{ $contrat->bien->departement->nom_departement ?? '' }}</p>
                            <p class="text-muted small mb-0">Prix : {{ number_format($contrat->bien->prix ?? 0, 0, ',', ' ') }} CFA</p>
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold mb-3"><i class="fas fa-history me-2" style="color:#4ECDC4"></i>Historique des paiements</h6>
                            @if($contrat->paiements->isEmpty())
                                <p class="text-muted small">Aucun paiement enregistré.</p>
                            @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr><th>Date</th><th>Type</th><th>Montant</th><th>Référence</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contrat->paiements as $p)
                                        <tr>
                                            <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-info">{{ $p->type_paiement }}</span></td>
                                            <td class="fw-bold" style="color:#2ecc71">{{ number_format($p->montant, 0, ',', ' ') }} F</td>
                                            <td><small class="text-muted">{{ $p->reference }}</small></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $contrats->withQueryString()->links() }}
</div>
@endif

@endsection
