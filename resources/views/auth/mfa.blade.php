@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-3 text-center">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Vérification en deux étapes
                    </h4>
                    <p class="text-muted small text-center mb-4">
                        Un code à 6 chiffres a été envoyé à <strong>{{ $email }}</strong>.
                        Il est valable 10 minutes.
                    </p>

                    @if (session('message'))
                        <div class="alert alert-success py-2">{{ session('message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            @foreach ($errors->all() as $e)
                                <div>{{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mfa.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code reçu par email</label>
                            <input type="text"
                                   name="code"
                                   inputmode="numeric"
                                   autocomplete="one-time-code"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   class="form-control form-control-lg text-center"
                                   style="letter-spacing: 0.5rem; font-weight: 600;"
                                   required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            Valider le code
                        </button>
                    </form>

                    <form method="POST" action="{{ route('mfa.resend') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-link w-100 text-decoration-none">
                            <i class="fas fa-paper-plane me-1"></i> Renvoyer un code
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            Annuler et se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
