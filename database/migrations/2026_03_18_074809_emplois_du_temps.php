<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emplois_du_temps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();

            // Créneau horaire
            $table->enum('jour', ['lundi','mardi','mercredi','jeudi','vendredi','samedi']);
            $table->time('heure_debut');
            $table->time('heure_fin');

            // Type de créneau
            $table->enum('type', ['cours','evaluation','examen','rattrapage','activite','pause'])->default('cours');

            // Salle / lieu
            $table->string('salle', 60)->nullable();

            // Période académique
            $table->string('annee_academique', 20)->nullable();
            $table->enum('periode', ['trimestre1','trimestre2','trimestre3','semestre1','semestre2','annee'])->default('annee');

            // Couleur d'affichage (hex)
            $table->string('couleur', 10)->nullable();

            // Notes libres
            $table->text('notes')->nullable();

            // Statut
            $table->enum('statut', ['actif','suspendu','annule'])->default('actif');

            $table->timestamps();

            // Index
            $table->index(['institution_id','classe_id','jour']);
            $table->index(['teacher_id','jour']);
        });
    }
    public function down(): void { Schema::dropIfExists('emplois_du_temps'); }
};




