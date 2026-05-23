<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grade_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('annee_academique', 20);

            // Système de notation
            $table->decimal('note_max', 5, 2)->default(20);   // /20 ou /100
            $table->decimal('note_passage', 5, 2)->default(10); // note minimale pour valider

            // Composition de la moyenne finale (%)
            $table->decimal('pct_devoirs', 5, 2)->default(40);   // contrôles continus
            $table->decimal('pct_examen', 5, 2)->default(60);    // examen final

            // Arrondis
            $table->integer('decimales')->default(2);

            // Mentions
            $table->json('mentions')->nullable();
            // Exemple: [{"libelle":"Excellent","min":16},{"libelle":"Bien","min":14},...]

            // Règles de compensation
            $table->boolean('compensation_active')->default(false);
            $table->decimal('seuil_compensation', 5, 2)->nullable(); // note min pour compenser

            // Périodes
            $table->string('type_periodes')->default('trimestres'); // trimestres | semestres
            $table->integer('nb_periodes')->default(3);

            $table->timestamps();
            $table->unique(['institution_id', 'annee_academique']);
        });
    }

    public function down(): void { Schema::dropIfExists('grade_configs'); }
};