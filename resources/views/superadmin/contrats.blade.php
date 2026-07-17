@extends('layouts.superadmin')
@section('title', 'Tous les Contrats')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tous les Contrats</h4>
        <p class="text-muted mb-0">Vue globale de toutes les agences</p>
    </div>
    {{-- Filtres --}}
    <form action="{{ route('superadmin.contrats') }}" method="GET" class="d-flex gap-2 flex-wrap justify-content-end">
        <select name="id_agence" class="form-select form-select-sm" style="border-radius:10px;width:180px" onchange="this.form.submit()">
            <option value="">Toutes les agences</option>
            @foreach($agences as $ag)
                <option value="{{ $ag->id_agence }}" {{ request('id_agence') == $ag->id_agence ? 'selected':'' }}>
                    {{ $ag->nom_agence }}
                </option>
            @endforeach
        </select>
        <select name="statut" class="form-select form-select-sm" style="border-radius:10px;width:150px" onchange="this.form.submit()">
            <option value="">Tous statuts</option>
            <option value="en_attente" {{ request('statut')=='en_attente'?'selected':'' }}>En attente</option>
            <option value="confirme"   {{ request('statut')=='confirme'?'selected':'' }}>Confirmé</option>
            <option value="annule"     {{ request('statut')=='annule'?'selected':'' }}>Annulé</option>
        </select>
        @if(request('id_agence') || request('statut'))
        <a href="{{ route('superadmin.contrats') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:10px">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </form>
</div>

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Bien</th>
                        <th>Agence</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Payé</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contrats as $contrat)
                    @php
                        $mt = $contrat->type_contrat==='location'
                            ? ($contrat->location->montant_total_location ?? 0)
                            : ($contrat->vente->montant_total_vente ?? 0);
                        $tp = $contrat->paiements->sum('montant');
                    @endphp
                    <tr>
                        <td class="fw-bold text-muted">#{{ $contrat->id_contrat }}</td>
                        <td>
                            <p class="mb-0 fw-semibold small">{{ $contrat->client->prenom_client ?? '' }} {{ $contrat->client->nom_client ?? 'N/A' }}</p>
                            <small class="text-muted">{{ $contrat->client->tel_client ?? '' }}</small>
                        </td>
                        <td>
                            <p class="mb-0 small fw-semibold">{{ $contrat->bien->titre_bien ?? 'N/A' }}</p>
                            <small class="text-muted">{{ $contrat->bien->ville->nom_ville ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge" style="background:#e8fffe;color:#4ECDC4;border:1px solid #4ECDC4;font-size:11px">
                                {{ $contrat->bien->agence->nom_agence ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $contrat->type_contrat }}</span>
                        </td>
                        <td class="fw-bold small">{{ number_format($mt,0,',',' ') }} F</td>
                        <td class="small" style="color:#2ecc71">{{ number_format($tp,0,',',' ') }} F</td>
                        <td>
                            @if($contrat->statut_contrat==='confirme')
                                <span class="badge bg-success">Confirmé</span>
                            @elseif($contrat->statut_contrat==='en_attente')
                                <span class="badge bg-warning text-dark">En attente</span>
                            @else
                                <span class="badge bg-danger">Annulé</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $contrat->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-file-contract fa-3x mb-3 d-block"></i>
                            Aucun contrat trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $contrats->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
