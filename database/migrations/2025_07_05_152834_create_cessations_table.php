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
        Schema::create('cessations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conge_id')->index('cessations_conge_id_foreign');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->text('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('fiche_cessation_pdf')->nullable();
            $table->timestamps();
            $table->integer('nombre_jours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cessations');
    }
};
