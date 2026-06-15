<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMairieAgentDataTable extends Migration
{
    public function up()
    {
        Schema::create('mairie_agent_data', function (Blueprint $table) {
            $table->id();
            $table->string('commune');
            $table->string('designation');
            $table->string('localisation')->nullable();
            $table->string('activites')->nullable();
            $table->string('responsables')->nullable();
            $table->integer('personnes_associes')->nullable();
            $table->string('source_financement')->nullable();
            $table->decimal('montant', 15, 2)->nullable();
            $table->boolean('periode_2023')->default(false);
            $table->boolean('periode_2024')->default(false);
            $table->boolean('periode_2025')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mairie_agent_data');
    }
}
