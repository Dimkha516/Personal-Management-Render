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
        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employe_id')->constrained()->onDelete('cascade');

            $table->date('date_demande');
            $table->date('date_debut');
            $table->date('date_fin');

            $table->integer('nombre_jours')->nullable(); // calculé automatiquement

            $table->boolean('avec_solde')->default(false);

            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');

            $table->text('motif')->nullable();
            $table->string('piece_jointe')->nullable(); // document justificatif
            $table->string('fiche_disponibilite_pdf')->nullable(); // Note PDF de validation

            $table->text('commentaire')->nullable(); // Par RH (validation/refus)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
