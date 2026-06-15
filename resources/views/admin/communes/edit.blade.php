@extends('layouts.app')

@section('title', 'Modifier ' . $commune->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Header -->
            <div class="mb-5">
                <a href="{{ route('admin.communes.index') }}" class="btn btn-link text-decoration-none" style="color: #006600;">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la gestion
                </a>
                <h1 class="display-6 fw-bold text-dark mt-3 mb-2">
                    <i class="bi bi-pencil-square me-2" style="color: #006600;"></i>Modifier: {{ $commune->name }}
                </h1>
                <p class="text-muted">Mettez à jour les informations de la commune</p>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <form action="{{ route('admin.communes.update', $commune) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Erreurs -->
                        @if($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 mb-4" style="background-color: #fee2e2;">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-circle me-3" style="color: #dc2626; font-size: 1.25rem;"></i>
                                    <div>
                                        <h6 class="mb-2" style="color: #dc2626;">Erreurs détectées</h6>
                                        <ul class="mb-0 ps-3">
                                            @foreach($errors->all() as $error)
                                                <li style="color: #991b1b;">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Champ Nom -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark mb-3">
                                <i class="bi bi-chat-square-text me-2" style="color: #006600;"></i>Nom de la Commune <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $commune->name) }}"
                                   class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   style="border-radius: 8px; border-color: #e5e7eb; border-width: 2px;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Champ Logo -->
                        <div class="mb-5">
                            <label for="logo" class="form-label fw-bold text-dark mb-3">
                                <i class="bi bi-image me-2" style="color: #006600;"></i>Logo de la Commune
                            </label>

                            <!-- Logo Actuel -->
                            @if($commune->logo)
                                <div class="mb-4 p-4 rounded-3" style="background-color: #f9fafb;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <img src="{{ asset('storage/' . $commune->logo) }}" 
                                                 alt="{{ $commune->name }}" 
                                                 class="rounded-2"
                                                 style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #e5e7eb;">
                                        </div>
                                        <div>
                                            <h6 class="text-dark fw-bold mb-1">Logo actuel</h6>
                                            <small class="text-muted">Remplacez par une nouvelle image ou laissez vide</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Input File -->
                            <div class="input-group input-group-lg" style="border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-light border-0" style="border-bottom: 2px solid #e5e7eb;">
                                    <i class="bi bi-upload" style="color: #6b7280;"></i>
                                </span>
                                <input type="file" 
                                       id="logo" 
                                       name="logo" 
                                       accept="image/*"
                                       class="form-control {{ $errors->has('logo') ? 'is-invalid' : '' }}"
                                       style="border-radius: 0; border-color: #e5e7eb; border-width: 2px;">
                            </div>
                            <div class="mt-3 p-3 rounded-2" style="background-color: #f0fdf4;">
                                <small style="color: #059669;">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Formats acceptés:</strong> JPG, PNG, WebP
                                    <strong>Taille max:</strong> 2 MB
                                </small>
                            </div>
                            @error('logo')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Info Stats -->
                        <div class="row mb-5 g-3">
                            <div class="col-6">
                                <div class="p-3 rounded-2" style="background-color: #dbeafe;">
                                    <small class="text-muted d-block">Infrastructures</small>
                                    <h5 class="fw-bold mb-0" style="color: #0369a1;">{{ $commune->getInfrastructureCount() }}</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-2" style="background-color: #ddd6fe;">
                                    <small class="text-muted d-block">Agents</small>
                                    <h5 class="fw-bold mb-0" style="color: #6d28d9;">{{ $commune->getAgentCount() }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-grid gap-3 pt-3">
                            <button type="submit" class="btn btn-lg text-white rounded-2 fw-bold" style="background-color: #006600; padding: 0.875rem;">
                                <i class="bi bi-check-circle me-2"></i>Mettre à Jour
                            </button>
                            <a href="{{ route('admin.communes.index') }}" class="btn btn-lg btn-outline-secondary rounded-2 fw-bold">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info border-0 rounded-3 mt-4" style="background-color: #dbeafe;">
                            <i class="bi bi-lightbulb me-2" style="color: #0369a1;"></i>
                            <span style="color: #0369a1;">
                                <strong>Conseil:</strong> Vous pouvez modifier les informations de la commune et son logo depuis cette page.
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-0 shadow-sm mt-4" style="background-color: #fef2f2; border-left: 4px solid #dc2626;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color: #7f1d1d;">
                        <i class="bi bi-exclamation-triangle me-2"></i>Zone Dangereuse
                    </h6>
                    <p class="text-muted small mb-3">Supprimer cette commune supprimera toutes les données associées de façon permanente.</p>
                    <form action="{{ route('admin.communes.destroy', $commune) }}" method="POST" class="d-inline" onsubmit="return confirm('Cette action est irréversible. Êtes-vous absolument sûr de vouloir supprimer la commune ' + '{{ $commune->name }}' + ' ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-2" style="background-color: #dc2626;">
                            <i class="bi bi-trash me-2"></i>Supprimer cette commune
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

