<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Table principale : suivi mensuel par apprenant ──
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('apprenant_id')->constrained()->cascadeOnDelete();

            // Période
            $table->string('annee_academique', 20);   // ex: "2024-2025"
            $table->tinyInteger('mois');               // 1–12
            $table->string('mois_label', 20);          // "Janvier", "Février"…

            // Montants
            $table->decimal('montant_du',   10, 2)->default(0);   // mensualité attendue
            $table->decimal('montant_paye', 10, 2)->default(0);   // montant réellement payé
            $table->decimal('montant_reste', 10, 2)->default(0);  // reste à payer (calculé)

            // Statut paiement
            $table->enum('statut', ['paye', 'partiel', 'impaye'])->default('impaye');

            // Date de paiement (nullable si impayé)
            $table->date('date_paiement')->nullable();

            // Mode de paiement
            $table->enum('mode_paiement', ['especes', 'virement', 'mobile_money', 'cheque', 'autre'])->nullable();

            // Référence (numéro reçu, etc.)
            $table->string('reference', 100)->nullable();

            // Notes libres
            $table->text('notes')->nullable();

            // ── Signature / Transparence ──
            // Qui a enregistré ce paiement
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->nullable();

            // Qui a vérifié/validé (directeur ou comptable)
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // Unicité : un seul enregistrement par apprenant × mois × année
            $table->unique(['apprenant_id', 'annee_academique', 'mois'], 'unique_apprenant_mois');

            // Index pour requêtes fréquentes
            $table->index(['institution_id', 'annee_academique']);
            $table->index(['apprenant_id', 'annee_academique']);
            $table->index('statut');
        });

        // ── Table des tarifs mensuels par institution / filière ──
        Schema::create('financial_tariffs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('annee_academique', 20);
            $table->string('label', 100);                  // "Scolarité standard", "Filière scientifique"…
            $table->decimal('montant_mensuel', 10, 2);
            $table->foreignId('filiere_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('niveau_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('actif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_records');
        Schema::dropIfExists('financial_tariffs');
    }
};