{{-- Navigation contextuelle selon le rôle de l'utilisateur connecté --}}
@php
    $u = auth()->user();
    $pendingScope = \App\Models\User::query()
        ->where('is_approved', false)
        ->whereNull('rejected_at')
        ->where('role', '!=', 'super_admin');
    if ($u->isCommuneAdmin()) {
        $pendingScope->where('commune_id', $u->commune_id)->where('role', 'agent');
    }
    $pendingCount = ($u->isSuperAdmin() || $u->isCommuneAdmin()) ? $pendingScope->count() : 0;
@endphp

@if($u->isSuperAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.communes.index') }}">Communes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}">Utilisateurs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}">Infrastructures</a>
    </li>
@elseif($u->isCommuneAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('commune-admin.dashboard') }}">Tableau de bord</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}">Infrastructures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('infrastructures.planned') }}">Infrastructures planifiées</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('mairie-agent.dashboard') }}">Planification</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('commune-admin.access-code.edit') }}">Code d'accès</a>
    </li>
@elseif($u->isAgent())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('mairie-agent.dashboard') }}">Mon tableau</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}">Mes infrastructures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('infrastructures.create') }}">Ajouter</a>
    </li>
@endif

@if($u->isSuperAdmin() || $u->isCommuneAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.pending-registrations') }}">
            Inscriptions
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
@endif
