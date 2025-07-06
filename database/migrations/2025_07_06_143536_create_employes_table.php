<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->unsignedBigInteger('fonction_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('type_agent_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
