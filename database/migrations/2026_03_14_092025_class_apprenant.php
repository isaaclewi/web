<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 |──────────────────────────────────────────────────────────────
 |  Migration : table pivot class_apprenant
 |
 |  Même structure que class_teacher.
 |  On abandonne le class_id direct sur apprenants (nullable)
 |  au profit d'une vraie table pivot, ce qui permet :
 |   - un élève dans plusieurs classes (multi-année)
 |   - des métadonnées sur l'inscription (date, statut...)
 |   - des suppressions propres via cascadeOnDelete
 |
 |  NOTE : si vous souhaitez garder class_id sur apprenants
 |  comme cache de lecture rapide, vous pouvez laisser les deux.
 |──────────────────────────────────────────────────────────────
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_apprenant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')     ->constrained('classes')   ->cascadeOnDelete();
            $table->foreignId('apprenant_id') ->constrained('apprenants')->cascadeOnDelete();

            // Métadonnées utiles
            $table->string('annee_academique', 20)->nullable(); // ex: 2025-2026
            $table->date('date_inscription')->nullable();
            $table->enum('statut', ['actif', 'transfere', 'exclu', 'diplome'])->default('actif');

            // Un apprenant ne peut être qu'une fois dans une classe pour une année
            $table->unique(['class_id', 'apprenant_id', 'annee_academique'], 'class_apprenant_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_apprenant');
    }
};