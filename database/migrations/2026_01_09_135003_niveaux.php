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
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // CP, CE1, 6e, Terminale, Licence 1, Master 2...
            $table->enum('cycle', ['primaire', 'secondaire', 'universite']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('niveaux');
    }
};
