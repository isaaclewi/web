<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();

            // Lien utilisateur — nullable : un parent peut exister sans compte
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();

            // Identité
            $table->string('nom');
            $table->string('prenom');
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->string('matricule')->unique()->nullable();

            // Contact
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // Infos sociales
            $table->string('profession')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('photo')->nullable();
            $table->string('cin')->nullable();
            $table->string('nationalite')->nullable();
            $table->string('nombre_enfants')->nullable();

            // Statut
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};