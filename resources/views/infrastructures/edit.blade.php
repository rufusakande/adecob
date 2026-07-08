@extends('layouts.app')
@section('title', 'Modifier une infrastructure')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="text-center mb-4">MODIFIER UNE INFRASTRUCTURE</h2>

    @include('infrastructures._form', [
        'action' => route('infrastructures.update', $infrastructure->id),
        'method' => 'PUT',
        'isEdit' => true,
        'infrastructure' => $infrastructure,
        'submitLabel' => 'Enregistrer les modifications',
    ])
</div>
@endsection
