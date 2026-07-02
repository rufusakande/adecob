@php
    $status = $status ?? ($infrastructure->status ?? 'validated');
    $map = [
        'draft' => ['bg' => 'bg-secondary', 'label' => 'Brouillon', 'icon' => 'fa-pen'],
        'pending' => ['bg' => 'bg-warning text-dark', 'label' => 'En attente de validation', 'icon' => 'fa-hourglass-half'],
        'validated' => ['bg' => 'bg-success', 'label' => 'Validée', 'icon' => 'fa-check-circle'],
        'rejected' => ['bg' => 'bg-danger', 'label' => 'Rejetée', 'icon' => 'fa-times-circle'],
    ];
    $s = $map[$status] ?? $map['validated'];
@endphp
<span class="badge {{ $s['bg'] }} d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size:.75rem;">
    <i class="fas {{ $s['icon'] }}"></i> {{ $s['label'] }}
</span>
