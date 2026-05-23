<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : transfer_requests
 *
 * Trace chaque demande de consultation de dossier inter-établissements.
 * L'établissement demandeur recherche un apprenant via son matricule
 * et son ancienne école. L'établissement source peut accepter ou refuser.
 *
 * Lancez : php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();

            // L'apprenant concerné
            $table->foreignId('apprenant_id')
                  ->constrained('apprenants')
                  ->cascadeOnDelete();

            // Établissement SOURCE (celui qui possède l'historique)
            $table->foreignId('institution_source_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete();

            // Établissement DESTINATAIRE (celui qui fait la demande)
            $table->foreignId('institution_dest_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete();

            // Admin qui a fait la demande
            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Admin de l'école source qui a traité la demande
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Statut : pending | approved | rejected | completed
            $table->enum('statut', ['pending', 'approved', 'rejected', 'completed'])
                  ->default('pending');

            // Ce que le destinataire veut consulter (flags JSON)
            // ex: ["notes","discipline","finances","identity"]
            $table->json('scope')->nullable();

            // Motif de la demande (texte libre)
            $table->text('motif')->nullable();

            // Motif du refus éventuel
            $table->text('motif_refus')->nullable();

            // Date de traitement
            $table->timestamp('processed_at')->nullable();

            // Jeton unique pour consultation sécurisée (lien partageable)
            $table->string('access_token', 64)->unique()->nullable();

            // Expiration du jeton d'accès
            $table->timestamp('token_expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('apprenant_id');
            $table->index('institution_source_id');
            $table->index('institution_dest_id');
            $table->index('statut');
            $table->index('access_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};