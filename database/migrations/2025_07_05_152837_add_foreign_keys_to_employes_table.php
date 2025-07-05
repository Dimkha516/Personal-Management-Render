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
        Schema::table('employes', function (Blueprint $table) {
            $table->foreign(['fonction_id'])->references(['id'])->on('fonctions')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['service_id'])->references(['id'])->on('services')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['type_agent_id'])->references(['id'])->on('types_agent')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropForeign('employes_fonction_id_foreign');
            $table->dropForeign('employes_service_id_foreign');
            $table->dropForeign('employes_type_agent_id_foreign');
            $table->dropForeign('employes_user_id_foreign');
        });
    }
};
