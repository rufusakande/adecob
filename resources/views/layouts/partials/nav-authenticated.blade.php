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
        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="adminNavDropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cog"></i> Administration
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminNavDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('admin.communes.index') }}"><i class="fas fa-city"></i> Communes</a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> Utilisateurs</a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.pending-registrations') }}">
                    <i class="fas fa-user-check"></i> Inscriptions en attente
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}"><i class="fas fa-building"></i> Infrastructures</a>
    </li>
@elseif($u->isCommuneAdmin())
    <li class="nav-item">
        <a class="nav-link" href="{{ route('commune-admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="infraNavDropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-building"></i> Infrastructures
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="infraNavDropdown">
            <li>
                <a class="dropdown-item" href="{{ url('/infrastructures') }}"><i class="fas fa-list"></i> Mes infrastructures</a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('infrastructures.planned') }}"><i class="fas fa-calendar-check"></i> Infrastructures planifiées</a>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.pending-registrations', ['status' => 'approved']) }}">
            <i class="fas fa-users"></i> Agents de la commune
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-1" title="{{ $pendingCount }} en attente">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
@elseif($u->isAgent())
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/infrastructures') }}"><i class="fas fa-building"></i> Mes infrastructures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('infrastructures.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a>
    </li>
@endif

<li class="nav-item">
    <a class="nav-link" href="{{ route('infrastructures.offline') }}">
        <i class="fas fa-cloud-download-alt"></i> Fiches hors-ligne
    </a>
</li>
