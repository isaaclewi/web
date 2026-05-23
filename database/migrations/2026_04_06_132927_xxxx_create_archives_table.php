<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('annee_academique', 20);
            $table->enum('categorie', [
                'complet', 'apprenants', 'enseignants', 'bulletins',
                'finances', 'disciplinaire', 'classes', 'planning', 'staff',
            ]);
            $table->enum('type_export', ['annuel', 'trimestriel', 'manuel'])->default('annuel');
            $table->text('description')->nullable();
            $table->string('fichier_path');
            $table->unsignedBigInteger('taille_octets')->default(0);
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['institution_id', 'annee_academique', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
