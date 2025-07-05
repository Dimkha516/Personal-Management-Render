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
        Schema::create('employes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->nullable();
            $table->string('adresse');
            $table->date('date_naiss');
            $table->string('lieu_naiss');
            $table->string('situation_matrimoniale');
            $table->date('date_prise_service');
            $table->string('genre');
            $table->string('type_contrat');
            $table->integer('solde_conge_jours')->default(0);
            $table->unsignedBigInteger('fonction_id')->index('employes_fonction_id_foreign');
            $table->unsignedBigInteger('service_id')->index('employes_service_id_foreign');
            $table->unsignedBigInteger('type_agent_id')->index('employes_type_agent_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('employes_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
