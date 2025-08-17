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
        Schema::create('frais_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordre_mission_id')->constrained('ordres_missions')->onDelete('cascade');
            $table->string('libelle');
            $table->decimal('montant', 10, 2);
            $table->boolean('payable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frais_missions');
    }
};
