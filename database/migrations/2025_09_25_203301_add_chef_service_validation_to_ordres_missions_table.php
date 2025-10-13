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
        Schema::table('ordres_missions', function (Blueprint $table) {
            Schema::table('ordres_missions', function (Blueprint $table) {
                // Ajoute une colonne boolean avec valeur par défaut false.
                // ->after('statut') est optionnel : place la colonne après 'statut' si le SGBD le supporte.
                $table->boolean('chef_service_validation')->default(false)->after('statut');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordres_missions', function (Blueprint $table) {
            $table->dropColumn('chef_service_validation');
        });
    }
};
