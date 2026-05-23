<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Catalogue des modules disponibles
        Schema::create('staff_task_modules', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique(); // ex: 'absences', 'accueil', 'paiements'
            $table->string('label', 100);
            $table->string('description', 300)->nullable();
            $table->string('icone', 50)->nullable();   // nom d'icône SVG
            $table->json('roles_autorises');           // ['surveillant','comptable',...]
            $table->string('route_prefix', 100)->nullable(); // 'staff.absences'
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // Assignations par membre du staff
        Schema::create('staff_task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('staff_task_modules')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();           // instructions spécifiques du directeur
            $table->foreignId('assigne_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigne_at')->nullable();
            $table->timestamp('desactive_at')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_task_assignments');
        Schema::dropIfExists('staff_task_modules');
    }
};