{{-- Barre d'onglets mobile (visible uniquement < 768px via CSS) --}}
@auth
@php
    $u = auth()->user();
    $is = fn($pattern) => request()->routeIs($pattern) || request()->is($pattern);

    if ($u->isSuperAdmin()) {
        $tabs = [
            ['route' => route('admin.dashboard'), 'icon' => 'bi-speedometer2', 'label' => 'Tableau', 'active' => $is('admin.dashboard')],
            ['route' => url('/infrastructures'), 'icon' => 'bi-buildings', 'label' => 'Infras', 'active' => $is('infrastructures.index')],
            ['route' => route('infrastructure-assignments.index'), 'icon' => 'bi-hand-index', 'label' => 'Affect.', 'active' => $is('infrastructure-assignments.*')],
            ['route' => route('admin.communes.index'), 'icon' => 'bi-geo-alt', 'label' => 'Communes', 'active' => $is('admin.communes.*')],
            ['route' => route('admin.users.index'), 'icon' => 'bi-people', 'label' => 'Users', 'active' => $is('admin.users.*')],
            ['route' => route('guide.show'), 'icon' => 'bi-book', 'label' => 'Guide', 'active' => false, 'target' => '_blank'],
        ];
    } elseif ($u->isCommuneAdmin()) {
        $tabs = [
            ['route' => route('commune-admin.dashboard'), 'icon' => 'bi-speedometer2', 'label' => 'Tableau', 'active' => $is('commune-admin.dashboard')],
            ['route' => url('/infrastructures'), 'icon' => 'bi-buildings', 'label' => 'Infras', 'active' => $is('infrastructures.index')],
            ['route' => route('infrastructure-assignments.index'), 'icon' => 'bi-hand-index', 'label' => 'Affect.', 'active' => $is('infrastructure-assignments.*')],
            ['route' => route('infrastructures.planned'), 'icon' => 'bi-calendar-check', 'label' => 'Planifiées', 'active' => $is('infrastructures.planned')],
            ['route' => route('admin.pending-registrations'), 'icon' => 'bi-person-check', 'label' => 'Agents', 'active' => $is('admin.pending-registrations')],
            ['route' => route('guide.show'), 'icon' => 'bi-book', 'label' => 'Guide', 'active' => false, 'target' => '_blank'],
        ];
    } else {
        $tabs = [
            ['route' => url('/infrastructures'), 'icon' => 'bi-buildings', 'label' => 'Mes fiches', 'active' => $is('infrastructures.index')],
            ['route' => route('infrastructures.create'), 'icon' => 'bi-plus-circle', 'label' => 'Ajouter', 'active' => $is('infrastructures.create')],
            ['route' => route('infrastructure-assignments.index'), 'icon' => 'bi-hand-index', 'label' => 'Affectées', 'active' => $is('infrastructure-assignments.*')],
            ['route' => route('infrastructures.offline'), 'icon' => 'bi-cloud-arrow-up', 'label' => 'Hors-ligne', 'active' => $is('infrastructures.offline')],
            ['route' => route('guide.show'), 'icon' => 'bi-book', 'label' => 'Guide', 'active' => false, 'target' => '_blank'],
        ];
    }
@endphp

<nav class="adc-tabbar" aria-label="Navigation principale mobile">
    @foreach($tabs as $tab)
        <a href="{{ $tab['route'] }}" class="{{ $tab['active'] ? 'active' : '' }}" @if($tab['active']) aria-current="page" @endif @if(!empty($tab['target'])) target="{{ $tab['target'] }}" rel="noopener" @endif>
            <i class="bi {{ $tab['icon'] }}" aria-hidden="true"></i>
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</nav>
@endauth
