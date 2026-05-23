<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();

            // Compte utilisateur lié (unique)
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Informations de connexion
            $table->string('email')->unique();
            $table->string('password'); // stocké hashé
            $table->string('matricule')->unique();
            $table->string('name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};