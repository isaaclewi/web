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
        Schema::create('administrative_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administrative_unit_id')->constrained();

            $table->string('title');
            // Inscription élève, Paiement scolarité, Organisation examen…

            $table->text('description')->nullable();

            $table->enum('category', [
                'inscription',
                'pedagogie',
                'finance',
                'discipline',
                'examen',
                'archive',
                'communication',
            ]);

            $table->enum('status', ['en_cours', 'terminee', 'annulee']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('administrative_activities');
    }
};
