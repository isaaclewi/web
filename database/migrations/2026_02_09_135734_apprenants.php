<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('apprenants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('niveau_id')->constrained();
            $table->foreignId('filiere_id')->nullable(); // NULL pour primaire/secondaire
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();

            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique()->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('contact')->nullable();
            $table->string('adresse')->nullable();
            $table->enum('sexe', ['M', 'F']);

            $table->string('annee_academique');
            $table->boolean('status')->default(true);
            $table->string('photo')->nullable();
            $table->string('cni')->nullable();
            $table->string('certificat')->nullable();
            $table->string('releve_notes')->nullable();
            $table->string('attestation_inscription')->nullable();
            $table->string('autres_documents')->nullable();
            $table->string('password')->nullable();


            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
