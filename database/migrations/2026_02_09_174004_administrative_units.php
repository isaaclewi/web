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
        Schema::create('administrative_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            // Direction, Scolarité, Intendance, Département Informatique…

            $table->enum('type', [
                'direction',
                'scolarite',
                'finances',
                'discipline',
                'pedagogie',
                'archives',
                'departement',
                'service',
            ]);

            $table->string('responsable')->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('administrative_units');
    }
};
