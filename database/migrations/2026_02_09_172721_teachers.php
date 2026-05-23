<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // Compte utilisateur — nullable car un enseignant peut exister sans compte
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();

            // Institution
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            // Identité
            $table->string('matricule')->nullable()->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->enum('sexe', ['M', 'F'])->nullable();

            // Contact
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // Professionnel
            $table->string('specialite')->nullable();
            $table->enum('type_contrat', ['fonctionnaire', 'vacataire', 'prive', 'CDI', 'CDD', 'benevole'])->nullable();
            $table->date('date_recrutement')->nullable();
            $table->date('date_depart')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv')->nullable();
            $table->string('diplome')->nullable();
            $table->string('experience')->nullable();
            $table->string('competences')->nullable();
            $table->string('langues')->nullable();
            $table->string('certifications')->nullable();
            $table->string('publications')->nullable();
            $table->string('projets')->nullable();
            $table->string('reseaux_sociaux')->nullable();
            $table->string('autres_infos')->nullable();
            $table->string('notes')->nullable();
            $table->string('documents')->nullable();
            $table->string('observations')->nullable();
            $table->string('recommandations')->nullable();
            $table->string('avis')->nullable();
            $table->string('evaluation')->nullable();
            $table->string('competences_pedagogiques')->nullable();
            $table->string('competences_techniques')->nullable();
            $table->string('competences_relationnelles')->nullable();
            $table->string('competences_organisationnelles')->nullable();
            $table->string('competences_gestion')->nullable();

            // Statut
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};