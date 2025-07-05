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
            $table->foreign(['conge_id'])->references(['id'])->on('conges')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cessations', function (Blueprint $table) {
            $table->dropForeign('cessations_conge_id_foreign');
        });
    }
};
