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
         Schema::create('sujet_examens', function (Blueprint $table) {
             $table->id();
             $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
             $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
             $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
             $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
             $table->string('titre');
             $table->enum('type', ['controle','examen','composition','tp','interro','devoir','rattrapage']);
             $table->date('date_evaluation')->nullable();
             $table->unsignedSmallInteger('duree_minutes')->nullable();
             $table->text('instructions')->nullable();
             $table->enum('statut', ['en_attente','recu','valide','rejete','archive'])->default('en_attente');
             $table->text('feedback_admin')->nullable();
             $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
             $table->timestamp('traite_at')->nullable();
             $table->timestamps();

             $table->index(['institution_id', 'statut']);
             $table->index(['teacher_id', 'statut']);
         });
 
         Schema::create('sujet_fichiers', function (Blueprint $table) {
             $table->id();
             $table->foreignId('sujet_examen_id')->constrained()->cascadeOnDelete();
             $table->string('nom_original');
             $table->string('chemin');
             $table->string('mime_type', 100)->nullable();
             $table->unsignedBigInteger('taille_octets')->default(0);
             $table->string('extension', 20)->nullable();
             $table->timestamps();
         });
     }
 
     public function down(): void
     {
         Schema::dropIfExists('sujet_fichiers');
         Schema::dropIfExists('sujet_examens');
     }
};
