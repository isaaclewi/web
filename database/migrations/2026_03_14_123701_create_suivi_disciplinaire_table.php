<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 |──────────────────────────────────────────────────────────────
 |  Migration : table suivi_disciplinaire
 |
 |  Commande :
 |    php artisan make:migration create_suivi_disciplinaire_table
 |    # coller ce contenu, puis :
 |    php artisan migrate
 |──────────────────────────────────────────────────────────────
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suivi_disciplinaire', function (Blueprint $table) {
            $table->id();

            // Liens
            $table->foreignId('apprenant_id')
                  ->constrained('apprenants')
                  ->cascadeOnDelete();

            $table->foreignId('institution_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete();

            // Qui a saisi l'incident (enseignant ou admin)
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Période
            $table->string('annee_civile', 10);        // ex: 2026
            $table->string('annee_academique', 20)->nullable(); // ex: 2025-2026
            $table->date('date_incident');

            // Catégorie d'incident
            $table->enum('type', [
                'absence',          // Absence injustifiée
                'retard',           // Retard
                'insolence',        // Insolence / manque de respect
                'violence',         // Violence physique ou verbale
                'triche',           // Triche ou fraude
                'perturbation',     // Perturbation en classe
                'tenue',            // Problème de tenue
                'autre',            // Autre
            ]);

            // Niveau de gravité : 1 (mineur) → 3 (grave)
            $table->unsignedTinyInteger('gravite')->default(1);

            // Description libre
            $table->text('description')->nullable();

            // Sanction appliquée
            $table->enum('sanction', [
                'aucune',
                'avertissement',
                'blame',
                'exclusion_cours',       // Exclusion de cours
                'exclusion_temp',        // Exclusion temporaire
                'exclusion_def',         // Exclusion définitive
                'convocation_parents',   // Convocation des parents
                'travail_supplementaire',
                'autre',
            ])->default('aucune');

            $table->text('sanction_detail')->nullable();

            // Suivi : la sanction a-t-elle été exécutée ?
            $table->boolean('sanction_executee')->default(false);
            $table->date('sanction_date_execution')->nullable();

            // Notification parents
            $table->boolean('parents_notifies')->default(false);
            $table->date('date_notification')->nullable();

            // Observations complémentaires
            $table->text('observations')->nullable();

            // Statut du dossier
            $table->enum('statut', ['ouvert', 'en_suivi', 'clos'])->default('ouvert');

            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->index(['apprenant_id', 'annee_civile']);
            $table->index(['institution_id', 'annee_civile']);
            $table->index(['type', 'gravite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suivi_disciplinaire');
    }
};