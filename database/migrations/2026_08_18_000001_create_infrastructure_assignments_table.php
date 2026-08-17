<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Système d'affectation des infrastructures :
     * un administrateur (super admin / admin de commune) affecte une ou plusieurs
     * infrastructures à un ou plusieurs agents collecteurs afin qu'ils puissent
     * mettre à jour leurs données. L'agent soumet sa mise à jour, l'admin la
     * revoit puis valide ou rejette. Une fois validée, l'agent perd l'accès.
     */
    public function up(): void
    {
        Schema::create('infrastructure_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('infrastructure_id');
            $table->unsignedBigInteger('assigned_to'); // agent collecteur
            $table->unsignedBigInteger('assigned_by'); // admin qui affecte
            // assigned | submitted | validated | rejected
            $table->string('status')->default('assigned')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('infrastructure_id')->references('id')->on('infrastructures')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['assigned_to', 'status']);
            $table->index('infrastructure_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_assignments');
    }
};
