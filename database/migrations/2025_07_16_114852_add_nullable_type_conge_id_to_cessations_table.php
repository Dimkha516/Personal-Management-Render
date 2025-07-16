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
            $table->unsignedBigInteger('type_conge_id')->nullable()->after('id');

            $table->foreign('type_conge_id')
                ->references('id')
                ->on('types_conges')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cessations', function (Blueprint $table) {
            $table->dropForeign(['type_conge_id']);
            $table->dropColumn('type_conge_id');
        });
    }
};
