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
            $table->unsignedBigInteger('employe_id')->nullable()->after('id');

            $table->foreign('employe_id')
                ->references('id')
                ->on('employes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cessations', function (Blueprint $table) {
            $table->dropForeign(['employe_id']);
            $table->dropColumn('employe_id');
        });
    }
};
