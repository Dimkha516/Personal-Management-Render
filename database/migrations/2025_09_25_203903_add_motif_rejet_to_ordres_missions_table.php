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
            $table->string('motif_rejet')->nullable()->after('chef_service_validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordres_missions', function (Blueprint $table) {
            $table->dropColumn('motif_rejet');
        });
    }
};
