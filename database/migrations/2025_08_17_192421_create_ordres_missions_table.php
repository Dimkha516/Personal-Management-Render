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
        Schema::create('ordres_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demandeur_id')->constrained('employes')->onDelete('cascade');
            $table->string('destination');
            $table->integer('kilometrage')->nullable();
            $table->decimal('qte_carburant', 8, 2)->nullable();
            $table->foreignId('vehicule_id')->nullable()->constrained('vehicules')->nullOnDelete();
            $table->foreignId('chauffeur_id')->nullable()->constrained('chauffeurs')->nullOnDelete();

            $table->decimal('total_frais', 10, 2)->default(0);
            $table->string('numero_identification')->unique()->nullable();

            $table->date('date_depart')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->integer('nb_jours')->nullable();

            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->boolean('carburant_valide')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordres_missions');
    }
};
