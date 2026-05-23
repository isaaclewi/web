<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// migration 2
return new class extends Migration {
    public function up(): void
    {
        Schema::create('seances_cours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('emploi_du_temps_id')->nullable()->constrained('emplois_du_temps')->nullOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();

            // Date réelle de la séance
            $table->date('date_seance');
            $table->time('heure_debut');
            $table->time('heure_fin');

            $table->enum('type', ['cours','evaluation','examen','rattrapage','activite'])->default('cours');
            $table->string('salle', 60)->nullable();

            // Contenu de la séance
            $table->string('titre', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('objectifs')->nullable();

            // Statut réel
            $table->enum('statut', [
                'planifiee',    // prévue
                'realisee',     // effectuée
                'annulee',      // annulée
                'reportee',     // reportée
            ])->default('planifiee');

            $table->text('motif_annulation')->nullable();
            $table->date('date_report')->nullable();

            // Évaluation associée (si type = evaluation/examen)
            $table->foreignId('evaluation_id')->nullable()->constrained('evaluations')->nullOnDelete();

            $table->string('annee_academique', 20)->nullable();
            $table->timestamps();

            $table->index(['institution_id','classe_id','date_seance']);
            $table->index(['teacher_id','date_seance']);
        });
    }
    public function down(): void { Schema::dropIfExists('seances_cours'); }
};
