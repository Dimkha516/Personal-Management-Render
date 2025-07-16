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
        Schema::table('cessations', function (Blueprint $table) {
            // Supprimer la contrainte étrangère si elle existe
            $table->dropForeign(['conge_id']);
            $table->dropColumn('conge_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cessations', function (Blueprint $table) {
            $table->unsignedBigInteger('conge_id')->after('id');
            $table->foreign('conge_id')->references('id')->on('conges')->onDelete('cascade');
        });
    }
};
