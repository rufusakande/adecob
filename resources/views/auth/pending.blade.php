@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10"
                              style="width:80px;height:80px;">
                            <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-3">Compte en attente de validation</h3>

                    @if(session('message'))
                        <p class="text-muted">{{ session('message') }}</p>
                    @else
                        <p class="text-muted">
                            Votre inscription a bien été enregistrée sur la plateforme
                            <strong>ADECOB Infrastructure Plannification</strong>.
                        </p>
                    @endif

                    <hr class="my-4">

                    <div class="text-start small text-muted">
                        <p class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i>
                            Votre demande va être examinée par&nbsp;:</p>
                        <ul class="mb-3 ps-4">
                            <li>l'<strong>administrateur de votre commune</strong>, ou</li>
                            <li>un <strong>super administrateur</strong> de l'ADECOB.</li>
                        </ul>
                        <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>
                            Vous recevrez un email dès que votre compte sera approuvé ou rejeté.</p>
                    </div>

                    <div class="mt-4 d-flex gap-2 justify-content-center">
                        <a href="{{ route('public.landing') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-1"></i> Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
