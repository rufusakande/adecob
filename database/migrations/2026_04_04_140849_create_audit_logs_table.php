<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // User qui a effectué l'action
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('user_name')->nullable(); // Nom pour les cas où l'utilisateur est supprimé
            
            // Type d'action
            $table->enum('action', ['login', 'logout', 'create', 'read', 'update', 'delete', 'export', 'import', 'password_reset', 'profile_update']);
            
            // Type d'entité audité
            $table->string('auditable_type')->nullable(); // Classe du modèle (ex: App\Models\Infrastructure)
            $table->unsignedBigInteger('auditable_id')->nullable(); // ID du modèle
            
            // Description de l'action
            $table->text('description')->nullable();
            
            // Détails (JSON)
            $table->json('old_values')->nullable(); // Anciennes valeurs avant modification
            $table->json('new_values')->nullable(); // Nouvelles valeurs après modification
            
            // Informations du contexte
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->string('url')->nullable();
            $table->string('status')->default('success'); // success, error, warning
            $table->text('error_message')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes pour les performances
            $table->index('user_id');
            $table->index('action');
            $table->index('auditable_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
