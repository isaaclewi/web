<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('annee_academique', 20);
            $table->string('periode', 30); // trimestre1, trimestre2, semestre1, annuel, etc.

            // Moyennes calculées
            $table->decimal('moyenne_generale', 5, 2)->nullable();
            $table->decimal('moyenne_devoirs', 5, 2)->nullable();
            $table->decimal('moyenne_examens', 5, 2)->nullable();
            $table->integer('rang')->nullable();
            $table->integer('effectif_classe')->nullable();
            $table->string('mention', 50)->nullable();
            $table->boolean('admis')->default(false);

            // Détail matières (JSON)
            $table->json('detail_matieres')->nullable();
            // [{subject_id, nom, coeff, moy_devoirs, moy_examens, moyenne, mention, rang}]

            // Appréciations
            $table->text('appreciation_conseil')->nullable();
            $table->text('appreciation_directeur')->nullable();

            // Publication
            $table->boolean('publie')->default(false);
            $table->timestamp('publie_at')->nullable();
            $table->foreignId('publie_par')->nullable()->constrained('users')->nullOnDelete();

            // Calcul
            $table->timestamp('calcule_at')->nullable();
            $table->foreignId('calcule_par')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->unique(['apprenant_id', 'annee_academique', 'periode']);
        });
    }

    public function down(): void { Schema::dropIfExists('bulletins'); }
};