<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructures', 'status')) {
                $table->enum('status', ['draft', 'pending', 'validated', 'rejected'])
                    ->default('pending')
                    ->after('rehabilitation');
            }
            if (!Schema::hasColumn('infrastructures', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('status');
                $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('infrastructures', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('validated_by');
            }
            if (!Schema::hasColumn('infrastructures', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('validated_at');
            }
            if (!Schema::hasColumn('infrastructures', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('submitted_at');
            }
        });

        // Toutes les infrastructures existantes sont considérées comme validées
        // pour ne pas casser l'analyse déjà en place.
        DB::table('infrastructures')
            ->whereNull('status')
            ->orWhere('status', 'pending')
            ->update([
                'status' => 'validated',
                'validated_at' => DB::raw('COALESCE(validated_at, created_at)'),
            ]);

        Schema::table('infrastructures', function (Blueprint $table) {
            try {
                $table->index(['status', 'commune_id'], 'infra_status_commune_idx');
            } catch (\Throwable $e) {
                // index déjà présent
            }
        });
    }

    public function down(): void
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            try { $table->dropIndex('infra_status_commune_idx'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('infrastructures', 'validated_by')) {
                try { $table->dropForeign(['validated_by']); } catch (\Throwable $e) {}
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('infrastructures', 'status') ? 'status' : null,
                Schema::hasColumn('infrastructures', 'validated_by') ? 'validated_by' : null,
                Schema::hasColumn('infrastructures', 'validated_at') ? 'validated_at' : null,
                Schema::hasColumn('infrastructures', 'submitted_at') ? 'submitted_at' : null,
                Schema::hasColumn('infrastructures', 'rejection_reason') ? 'rejection_reason' : null,
            ]));
        });
    }
};
