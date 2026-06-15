<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfrastructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('nom_enqueteur')->nullable();
            $table->string('numero_telephone')->nullable();
            $table->string('commune')->nullable();
            $table->json('arrondissement')->nullable();
            $table->string('village')->nullable();
            $table->string('hameau')->nullable();
            $table->string('secteur_domaine')->nullable();
            $table->string('type_infrastructure')->nullable();
            $table->string('nom_infrastructure')->nullable();
            $table->string('annee_realisation')->nullable();
            $table->string('bailleur')->nullable();
            $table->string('type_materiaux')->nullable();
            $table->string('etat_fonctionnement')->nullable();
            $table->string('niveau_degradation')->nullable();
            $table->string('mode_gestion')->nullable();
            $table->string('mode_gestion_preciser')->nullable();
            $table->text('defectuosites_relevees')->nullable();
            $table->text('mesures_proposees')->nullable();
            $table->text('observation_generale')->nullable();
            $table->string('photo1')->nullable();
            $table->string('photo2')->nullable();
            $table->string('photo3')->nullable();
            $table->string('photo4')->nullable();
            $table->string('rehabilitation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infrastructures');
    }
}
