<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('user_name')->nullable();

            $table->enum('action', [
                'login', 'logout', 'create', 'read', 'update', 'delete',
                'export', 'import', 'password_reset', 'profile_update',
                'approve_user', 'reject_user', 'assign_admin', 'revoke_admin',
            ]);

            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default('success');
            $table->text('error_message')->nullable();

            // Scope by commune for fast filtering in admin commune dashboards
            $table->unsignedBigInteger('commune_id')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('action');
            $table->index('auditable_type');
            $table->index('commune_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
