<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();

            // NULL = livre global (superadmin) | sinon = livre d'une institution
            $table->foreignId('institution_id')
                  ->nullable()
                  ->constrained('institutions')
                  ->nullOnDelete();

            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn', 30)->nullable();
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('level', 100)->nullable();

            $table->string('file_path');
            $table->string('file_type', 20)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('cover_path')->nullable();

            $table->boolean('allow_download')->default(true);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_global')->default(false); // true = visible toutes institutions

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('downloads')->default(0);

            $table->timestamps();

            // Index utiles
            $table->index(['is_global', 'is_published']);
            $table->index(['institution_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
