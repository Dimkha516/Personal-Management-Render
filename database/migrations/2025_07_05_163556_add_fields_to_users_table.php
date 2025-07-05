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
        Schema::table('users', function (Blueprint $table) {
            // Ajoute la colonne firstConnexion après password
            $table->boolean('firstConnexion')
                ->nullable()
                ->default(true)
                ->after('password');

            // Ajoute la colonne status
            $table->enum('status', ['attente_attribution', 'actif', 'inactif'])
                ->default('attente_attribution');

            // Ajoute la clé étrangère vers la table roles (elle doit exister avant !)
            $table->foreignId('role_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('firstConnexion');
            $table->dropColumn('status');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
