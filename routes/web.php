<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\InfrastructureWorkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\Admin\CommuneAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Auth;

// Pages publiques (accessibles sans connexion)
Route::get('/', [App\Http\Controllers\PublicController::class, 'landing'])->name('public.landing');
Route::get('/infrastructures/public', [App\Http\Controllers\PublicController::class, 'infrastructures'])
    ->name('public.infrastructures');

// Documents légaux & conformité (Code du numérique du Bénin)
Route::get('/pssi', [App\Http\Controllers\LegalController::class, 'pssi'])->name('legal.pssi');
Route::get('/politique-confidentialite', [App\Http\Controllers\LegalController::class, 'confidentialite'])->name('legal.confidentialite');
Route::get('/cgu', [App\Http\Controllers\LegalController::class, 'cgu'])->name('legal.cgu');
Route::get('/registre-traitements', [App\Http\Controllers\LegalController::class, 'registreTraitements'])->name('legal.registre');

// Authentication Routes (avec rate-limit anti brute-force)
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])
    ->middleware('throttle:register')->name('register');
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])
    ->middleware('throttle:login')->name('login');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// MFA email pour comptes admin
Route::middleware(['auth'])->group(function () {
    Route::get('/mfa', [App\Http\Controllers\MfaController::class, 'show'])->name('mfa.show');
    Route::post('/mfa/verify', [App\Http\Controllers\MfaController::class, 'verify'])
        ->middleware('throttle:login')->name('mfa.verify');
    Route::post('/mfa/resend', [App\Http\Controllers\MfaController::class, 'resend'])
        ->middleware('throttle:password-reset')->name('mfa.resend');
});

// Password Reset Routes (rate-limit pour éviter l'énumération et le spam mail)
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:password-reset')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:password-reset')->name('password.update');

// Google OAuth Routes
Route::get('/auth/google/redirect', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Home page - liste des communes (après connexion)
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Commune selection routes
    Route::get('/commune/{commune}/select', [CommuneController::class, 'select'])->name('commune.select');
    Route::post('/commune/{commune}/verify', [CommuneController::class, 'verifyCode'])->name('commune.verify');
    Route::post('/commune/logout', [CommuneController::class, 'logout'])->name('commune.logout');
    Route::get('/commune/{commune}/dashboard', [CommuneController::class, 'dashboard'])->name('commune.dashboard');
});

use App\Http\Controllers\MairieAgentController;

// Infrastructure Routes (protected by auth middleware)
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::get('/infrastructures', [App\Http\Controllers\InfrastructureController::class, 'index'])->name('infrastructures.index');
    Route::post('/infrastructures/import', [App\Http\Controllers\InfrastructureController::class, 'import'])->name('infrastructures.import');
    Route::get('/infrastructures/export', [App\Http\Controllers\InfrastructureController::class, 'export'])->name('infrastructures.export');
    Route::delete('infrastructures/{id}', [InfrastructureController::class, 'destroy'])->name('infrastructures.destroy');
    Route::get('/infrastructures/create', [App\Http\Controllers\InfrastructureController::class, 'create'])->name('infrastructures.create');
    Route::post('/infrastructures', [App\Http\Controllers\InfrastructureController::class, 'store'])->name('infrastructures.store');
    Route::get('infrastructures/{id}', [InfrastructureController::class, 'show'])->name('infrastructures.show');
    Route::get('/infrastructures/{infrastructure}/edit', [App\Http\Controllers\InfrastructureController::class, 'edit'])->name('infrastructures.edit');
    Route::put('/infrastructures/{infrastructure}', [App\Http\Controllers\InfrastructureController::class, 'update'])->name('infrastructures.update');

    // Infrastructure work management routes
    Route::prefix('infrastructures/{infrastructure}')->group(function () {
        Route::post('works', [InfrastructureWorkController::class, 'store'])->name('infrastructures.works.store');
        Route::put('works/{work}', [InfrastructureWorkController::class, 'update'])->name('infrastructures.works.update');
        Route::delete('works/{work}', [InfrastructureWorkController::class, 'destroy'])->name('infrastructures.works.destroy');
    });
});

// Mairie Agent Routes (auth + approbation + rôles autorisés uniquement)
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::get('/mairie-agent/form/{infrastructure_id?}', [MairieAgentController::class, 'create'])->name('mairie-agent.form');
    Route::post('/mairie-agent/form', [MairieAgentController::class, 'store'])->name('mairie-agent.store');
    Route::put('/mairie-agent/form/{id}', [MairieAgentController::class, 'update'])->name('mairie-agent.update');
    Route::get('/mairie-agent/dashboard', [MairieAgentController::class, 'dashboard'])->name('mairie-agent.dashboard');
    Route::get('/mairie-agent/monitoring-dashboard', [MairieAgentController::class, 'monitoringDashboard'])->name('mairie-agent.monitoring-dashboard');
    Route::get('/mairie-agent/export-pdf', [MairieAgentController::class, 'exportPdf'])->name('mairie-agent.export-pdf');
});

// Contact Routes
// Contact Routes (rate-limit pour éviter le spam du formulaire public)
Route::get('/contact', [ContactController::class, 'show'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:contact')->name('contact.submit');

// Routes pour la validation des inscriptions
Route::get('/registration/pending', function () {
    return view('auth.pending');
})->name('registration.pending');

// Route d'approbation des inscriptions - accessible aux super admin et commune admin
Route::middleware(['auth', 'admin.access', 'mfa.verified'])->group(function () {
    Route::get('/admin/pending-registrations', [App\Http\Controllers\Admin\UserValidationController::class, 'index'])->name('admin.pending-registrations');
    Route::post('/admin/approve-user/{user}', [App\Http\Controllers\Admin\UserValidationController::class, 'approve'])->name('admin.approve-user');
    Route::post('/admin/reject-user/{user}', [App\Http\Controllers\Admin\UserValidationController::class, 'reject'])->name('admin.reject-user');
});

// Routes admin - réservées au super admin
Route::middleware(['auth', 'super.admin', 'mfa.verified'])->group(function () {
    // Tableau de bord Super Admin
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\SuperAdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Routes pour la gestion des utilisateurs
    Route::get('/admin/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::put('/admin/users/{user}/toggle-admin', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    
    // Routes pour la gestion des communes
    Route::resource('/admin/communes', CommuneAdminController::class, ['as' => 'admin']);
    Route::post('/admin/communes/{commune}/set-access-code', [CommuneAdminController::class, 'setAccessCode'])->name('admin.communes.set-access-code');
    Route::post('/admin/communes/{commune}/assign-admin', [CommuneAdminController::class, 'assignCommuneAdmin'])->name('admin.communes.assign-admin');
});

// Routes pour les admins communes (gestion de leur propre commune)
Route::middleware(['auth', 'commune.admin', 'mfa.verified'])->prefix('dashboard')->group(function () {
    Route::get('/commune/dashboard', [App\Http\Controllers\Admin\CommuneAdminDashboardController::class, 'dashboard'])->name('commune-admin.dashboard');
    Route::get('/commune/access-code', [App\Http\Controllers\Admin\CommuneAdminDashboardController::class, 'editAccessCode'])->name('commune-admin.access-code.edit');
    Route::post('/commune/access-code', [App\Http\Controllers\Admin\CommuneAdminDashboardController::class, 'updateAccessCode'])->name('commune-admin.access-code.update');
    Route::get('/commune/details', [App\Http\Controllers\Admin\CommuneAdminDashboardController::class, 'details'])->name('commune-admin.details');
});

// Audit Logs Routes (super admin only)
Route::middleware(['auth', 'super.admin', 'mfa.verified'])->prefix('admin/audit')->group(function () {
    Route::get('/', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    Route::get('{auditLog}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('audit.show');
    Route::get('user/{user}/history', [App\Http\Controllers\AuditLogController::class, 'userHistory'])->name('audit.user-history');
    Route::get('model/history', [App\Http\Controllers\AuditLogController::class, 'modelHistory'])->name('audit.model-history');
    Route::post('export', [App\Http\Controllers\AuditLogController::class, 'export'])->name('audit.export');
    Route::post('clear-old', [App\Http\Controllers\AuditLogController::class, 'clearOldLogs'])->name('audit.clear-old');
});
