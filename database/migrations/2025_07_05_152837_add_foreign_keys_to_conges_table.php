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
        Schema::table('conges', function (Blueprint $table) {
            $table->foreign(['employe_id'])->references(['id'])->on('employes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['type_conge_id'])->references(['id'])->on('types_conges')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conges', function (Blueprint $table) {
            $table->dropForeign('conges_employe_id_foreign');
            $table->dropForeign('conges_type_conge_id_foreign');
        });
    }
};
