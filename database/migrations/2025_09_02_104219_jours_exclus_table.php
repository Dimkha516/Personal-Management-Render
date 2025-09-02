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
        Schema::create('jours_exclus', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->comment('Date spécifique à exclure');
            $table->tinyInteger('jour_semaine')->nullable()->comment('1 = Lundi, ... 7 = Dimanche');
            $table->string('motif')->nullable();
            $table->enum('type_exclusion',  ['unique', 'recurrent']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jours_exclus');
    }
};
