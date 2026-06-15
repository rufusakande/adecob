@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<link rel="stylesheet" href="{{ asset('css/custom-auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth-enhancements.css') }}">
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="mb-4">Mot de passe oublié</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-4">
                    Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                </p>

                <form method="POST" action="{{ route('password.email') }}" data-loader>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail :</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="vous@exemple.com"
                        >
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            Envoyer le lien de réinitialisation
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <p class="text-center">
                    <a href="{{ route('login.form') }}" class="btn btn-link">← Retour à la connexion</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
