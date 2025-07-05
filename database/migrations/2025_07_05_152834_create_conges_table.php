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
        Schema::create('conges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employe_id')->index('conges_employe_id_foreign');
            $table->unsignedBigInteger('type_conge_id')->index('conges_type_conge_id_foreign');
            $table->date('date_demande')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->integer('nombre_jours')->nullable();
            $table->enum('statut', ['en_attente', 'approuve', 'refuse'])->default('en_attente');
            $table->text('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('piece_jointe')->nullable();
            $table->string('note_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
