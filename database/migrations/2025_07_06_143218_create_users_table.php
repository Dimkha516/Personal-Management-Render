<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('firstConnexion')->nullable()->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->enum('status', ['attente_attribution', 'actif', 'inactif'])->default('attente_attribution');
            $table->unsignedBigInteger('role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
