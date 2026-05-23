<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programmes_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            // Libellé de la tranche
            $table->string('libelle', 200);  // ex: "1ère tranche — Inscription"

            // Cible : toute l'institution, un niveau, une classe
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();

            // Montant
            $table->decimal('montant', 12, 2);
            $table->string('devise', 20)->default('FCFA');

            // Dates
            $table->date('date_echeance');
            $table->date('date_debut_rappel')->nullable();   // quand commencer à rappeler
            $table->integer('jours_grace')->default(0);      // jours de grâce après échéance

            // Type de frais
            $table->enum('type_frais', [
                'inscription',
                'scolarite',
                'examen',
                'tenue',
                'transport',
                'cantine',
                'activite',
                'autre',
            ])->default('scolarite');

            // Période académique
            $table->string('annee_academique', 20)->nullable();
            $table->enum('periode', [
                'annuel',
                'trimestre1','trimestre2','trimestre3',
                'semestre1','semestre2',
                'mensuel',
            ])->default('annuel');

            // Statut
            $table->enum('statut', ['actif','suspendu','archive'])->default('actif');

            // Notes
            $table->text('description')->nullable();
            $table->boolean('obligatoire')->default(true);

            // Ordre d'affichage
            $table->unsignedSmallInteger('ordre')->default(1);

            $table->timestamps();

            $table->index(['institution_id','annee_academique']);
            $table->index(['date_echeance']);
        });
    }
    public function down(): void { Schema::dropIfExists('programmes_paiement'); }
};