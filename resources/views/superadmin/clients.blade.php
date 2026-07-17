@extends('layouts.superadmin')
@section('title', 'Gestion des Clients')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tous les Clients</h4>
        <p class="text-muted mb-0">{{ $clients->total() }} client(s) inscrit(s)</p>
    </div>
    <form action="{{ route('superadmin.clients') }}" method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" placeholder="Rechercher..."
               value="{{ request('q') }}" style="border-radius:10px; width:220px">
        <button type="submit" class="btn" style="background:#4ECDC4;color:white;border-radius:10px">
            <i class="fas fa-search"></i>
        </button>
        @if(request('q'))
        <a href="{{ route('superadmin.clients') }}" class="btn btn-outline-secondary" style="border-radius:10px">
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
                        <th>Client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th class="text-center">Réservations</th>
                        <th>Inscrit le</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                     style="width:38px;height:38px;background:#e8fffe;color:#4ECDC4;font-weight:700;font-size:14px">
                                    {{ strtoupper(substr($client->prenom_client,0,1)) }}{{ strtoupper(substr($client->nom_client,0,1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $client->prenom_client }} {{ $client->nom_client }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $client->email }}</td>
                        <td class="text-muted small">{{ $client->tel_client ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge" style="background:#e8fffe;color:#4ECDC4;border:1px solid #4ECDC4">
                                {{ $client->contrats_count }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $client->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <form action="{{ route('superadmin.clients.supprimer', $client->id_client) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer {{ $client->prenom_client }} {{ $client->nom_client }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            Aucun client trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $clients->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
