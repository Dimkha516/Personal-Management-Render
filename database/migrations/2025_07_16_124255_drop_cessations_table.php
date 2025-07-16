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
        Schema::dropIfExists('cessations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('cessations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('type_conge_id');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->text('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('fiche_cessation_pdf')->nullable();
            $table->integer('nombre_jours')->nullable();
            $table->timestamps();

            $table->foreign('type_conge_id')->references('id')->on('types_conges')->onDelete('cascade');
        });
    }
};
