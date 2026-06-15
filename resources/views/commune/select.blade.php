@extends('layouts.app')

@section('content')
<div class="select-container">
    <!-- Background Gradient -->
    <div class="select-background"></div>

    <!-- Main Content -->
    <div class="select-content">
        <!-- Card Container -->
        <div class="select-card">
            <!-- Back Button -->
            <a href="{{ route('home') }}" class="back-button">
                <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Retour</span>
            </a>

            <!-- Commune Header -->
            <div class="commune-header">
                <div class="commune-badge">
                    @if($commune->logo)
                        <img src="{{ asset('storage/' . $commune->logo) }}" 
                             alt="{{ $commune->name }}" 
                             class="commune-image">
                    @else
                        <div class="commune-icon-placeholder">
                            <svg class="icon-large" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <h1 class="commune-title">{{ $commune->name }}</h1>
                <p class="commune-subtitle">Sélection et accès aux données</p>
            </div>

            <!-- Alert Messages -->
            @if($errors->any())
                <div class="alert alert-error">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Role Info Alert -->
            <div class="role-alert">
                @if(auth()->user()->isAgent())
                    <div class="alert-type agent">
                        <span class="alert-emoji">👤</span>
                        <div class="alert-text">
                            <strong>Agent Collecteur</strong>
                            <p>Entrez le code d'accès pour collecter les données de cette commune.</p>
                        </div>
                    </div>
                @elseif(auth()->user()->isCommuneAdmin())
                    <div class="alert-type admin">
                        <span class="alert-emoji">👨‍💼</span>
                        <div class="alert-text">
                            <strong>Admin Commune</strong>
                            <p>Vous avez accès complet aux données de cette commune.</p>
                        </div>
                    </div>
                @else
                    <div class="alert-type public">
                        <span class="alert-emoji">📊</span>
                        <div class="alert-text">
                            <strong>Mode Consultation</strong>
                            <p>Consultez les statistiques et informations publiques de cette commune.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Access Form -->
            <form action="{{ route('commune.verify', $commune->id) }}" method="POST" class="access-form">
                @csrf

                @if(auth()->user()->isAgent() || auth()->user()->isCommuneAdmin())
                    <div class="form-group">
                        <label for="access_code" class="form-label">Code d'accès</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="access_code" 
                                   name="access_code" 
                                   placeholder="●●●●●●●●"
                                   maxlength="50"
                                   class="form-input" 
                                   required>
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <small class="form-hint">Le code vous a été communiqué par l'administrateur</small>
                    </div>
                @else
                    <div class="form-group">
                        <label for="access_code" class="form-label">Code d'accès <span class="optional">(optionnel)</span></label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="access_code" 
                                   name="access_code" 
                                   placeholder="●●●●●●●●"
                                   maxlength="50"
                                   class="form-input">
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    <span>Accéder aux données</span>
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </form>

            <!-- Statistics Section -->
            <div class="stats-section">
                <h3 class="stats-title">Aperçu de la commune</h3>
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-number">{{ $commune->getInfrastructureCount() }}</div>
                        <div class="stat-label">Infrastructure{{ $commune->getInfrastructureCount() > 1 ? 's' : '' }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $commune->getAgentCount() }}</div>
                        <div class="stat-label">Agent{{ $commune->getAgentCount() > 1 ? 's' : '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .select-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .select-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f7fafc;
        z-index: 0;
    }

    .select-content {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 500px;
        animation: slideUp 0.6s ease-out;
    }

    .select-card {
        background: #fff;
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        border: 1.5px solid #e5e7eb;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #006600;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        padding: 8px 12px;
        border-radius: 8px;
    }

    .back-button:hover {
        background: #e6f4ea;
        color: #004d00;
    }

    .back-icon {
        width: 20px;
        height: 20px;
    }

    /* Commune Header */
    .commune-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .commune-badge {
        width: 100px;
        height: 100px;
        margin: 0 auto 25px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .commune-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .commune-icon-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-large {
        width: 60px;
        height: 60px;
        color: white;
    }

    .commune-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #006600;
        margin-bottom: 8px;
    }

    .commune-subtitle {
        font-size: 0.95rem;
        color: #6b7280;
    }

    /* Alerts */
    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .alert-icon {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .alert p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* Role Alert */
    .role-alert {
        margin-bottom: 30px;
    }

    .alert-type {
        padding: 20px;
        border-radius: 12px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .alert-type.agent {
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        color: #1e40af;
    }

    .alert-type.admin {
        background: #f0fdf4;
        border: 2px solid #bbf7d0;
        color: #166534;
    }

    .alert-type.public {
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        color: #374151;
    }

    .alert-emoji {
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .alert-text strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .alert-text p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.85;
    }

    /* Form */
    .access-form {
        display: flex;
        flex-direction: column;
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .optional {
        color: #9ca3af;
        font-weight: 400;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-input {
        width: 100%;
        padding: 14px 45px 14px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }

    .form-input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        color: #9ca3af;
        transition: all 0.3s ease;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-password:hover {
        color: #10b981;
    }

    .toggle-icon {
        width: 20px;
        height: 20px;
    }

    .form-hint {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 8px;
    }

    /* Button */
    .btn-submit {
        background: #006600;
        color: #fff;
        border: none;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background: #004d00;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 102, 0, 0.08);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-icon {
        width: 20px;
        height: 20px;
    }

    /* Statistics */
    .stats-section {
        padding-top: 30px;
        border-top: 2px solid #f3f4f6;
    }

    .stats-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .stat-box {
        text-align: center;
        padding: 15px;
        background: #f9fafb;
        border-radius: 10px;
        border: 2px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        border-color: #006600;
        background: #e6f4ea;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #006600;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 5px;
    }

    /* Animations */
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 600px) {
        .select-card {
            padding: 25px;
        }

        .commune-title {
            font-size: 1.8rem;
        }

        .commune-badge {
            width: 80px;
            height: 80px;
        }

        .access-form {
            gap: 20px;
        }

        .form-input {
            padding: 12px 40px 12px 14px;
            font-size: 0.95rem;
        }

        .btn-submit {
            padding: 14px 20px;
            font-size: 1rem;
        }

        .stats-grid {
            gap: 12px;
        }

        .stat-number {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 400px) {
        .select-card {
            padding: 20px;
        }

        .commune-title {
            font-size: 1.5rem;
        }

        .back-button {
            margin-bottom: 20px;
        }
    }
</style>

<script>
function togglePasswordVisibility() {
    const input = document.getElementById('access_code');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    // Update button appearance
    const button = event.target.closest('.toggle-password');
    if (isPassword) {
        button.classList.add('active');
    } else {
        button.classList.remove('active');
    }
}
</script>
@endsection
