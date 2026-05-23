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
        //
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administrative_unit_id')->constrained();

            $table->string('poste');
            $table->string('matricule')->unique();

            $table->string('nom');
            $table->string('prenom');

            $table->string('telephone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('genre')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv')->nullable();
            $table->string('diplome')->nullable();
            $table->string('experience')->nullable();
            $table->string('competences')->nullable();
            $table->string('langues')->nullable();
            $table->string('certifications')->nullable();
            $table->string('references')->nullable();
            $table->string('notes')->nullable();
            $table->date('date_embauche')->nullable();
            $table->date('date_depart')->nullable();
            $table->string('motivation')->nullable();
            $table->string('hobbies')->nullable();
            $table->string('projets')->nullable();
            $table->string('realisations')->nullable();
            $table->string('publications')->nullable();
            $table->string('distinctions')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('staff');
    }
};
