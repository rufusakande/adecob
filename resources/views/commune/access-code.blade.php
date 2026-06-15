@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h3 class="mb-0" style="color:#006600;font-weight:700;">
                        <i class="bi bi-key me-2"></i>Code d'accès de la commune
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold">Commune</label>
                        <div class="d-flex align-items-center gap-3">
                            @if($commune->logo)
                                <img src="{{ asset('storage/'.$commune->logo) }}" alt="Logo" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                            @endif
                            <span class="fw-bold" style="color:#006600;font-size:1.1rem;">{{ $commune->name }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('commune-admin.access-code.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="access_code" class="form-label fw-bold">Code d'accès actuel</label>
                            <input type="text" class="form-control bg-light" value="{{ $commune->access_code_plain }}" readonly style="color:#006600;font-weight:600;letter-spacing:2px;">
                        </div>
                        <div class="mb-3">
                            <label for="new_access_code" class="form-label fw-bold">Nouveau code d'accès</label>
                            <input type="text" name="access_code" id="new_access_code" class="form-control" minlength="4" maxlength="50" placeholder="Entrer un nouveau code (min. 4 caractères)" required>
                            <small class="text-muted">Le code doit contenir au moins 4 caractères et sera chiffré.</small>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3" style="background:#006600;border-color:#006600;">
                            <i class="bi bi-check-circle me-2"></i>Mettre à jour le code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
