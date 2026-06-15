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
        Schema::create('infrastructure_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infrastructure_id')->constrained()->onDelete('cascade');
            $table->string('work_type');
            $table->text('description')->nullable();
            $table->date('completion_date');
            $table->text('observations')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_contact')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructure_works');
    }
};
