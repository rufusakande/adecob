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
        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.communes.index') }}"><i class="bi bi-building"></i> Communes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Utilisateurs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}"><i class="bi bi-list-ul"></i> Infrastructures</a>
    </li>
@elseif($u->isCommuneAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('commune-admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}"><i class="bi bi-list-ul"></i> Infrastructures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('infrastructures.planned') }}"><i class="bi bi-calendar-check"></i> Infrastructures planifiées</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('mairie-agent.dashboard') }}"><i class="bi bi-bullseye"></i> Planification</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('commune-admin.access-code.edit') }}"><i class="bi bi-key"></i> Code d'accès</a>
    </li>
@elseif($u->isAgent())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('mairie-agent.dashboard') }}"><i class="bi bi-speedometer2"></i> Mon tableau</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}"><i class="bi bi-list-ul"></i> Mes infrastructures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('infrastructures.create') }}"><i class="bi bi-plus-circle"></i> Ajouter</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('mairie-agent.form') }}"><i class="bi bi-file-earmark-text"></i> Planifier</a>
    </li>
@endif

@if($u->isSuperAdmin() || $u->isCommuneAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.pending-registrations') }}">
            <i class="bi bi-person-plus"></i> Inscriptions
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
@endif
