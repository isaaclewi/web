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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Examen 1, Contrôle 2
            $table->enum('type', ['controle', 'examen', 'tp', 'projet']);
            $table->date('date');
            $table->decimal('max_score', 5, 2)->default(20);
            $table->timestamps();
        });

        // migration d'ajout de colonnes sur evaluations
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->after('subject_id')->constrained('classes')->nullOnDelete();
            $table->string('periode', 30)->nullable()->after('type'); // trimestre1, semestre1...
            $table->string('type_evaluation', 20)->default('devoir')->after('type'); // devoir | examen
            $table->boolean('publiee')->default(false)->after('max_score');
            $table->boolean('compte_dans_moyenne')->default(true)->after('publiee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('evaluations');
    }
};
