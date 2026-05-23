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

            // Propriétaire du livre
            $table->foreignId('institution_id')
                  ->nullable()
                  ->constrained('institutions')
                  ->nullOnDelete()
                  ->comment('NULL = visible par tous (ajouté par superadmin)');

            // Qui a uploadé ?
            $table->unsignedBigInteger('uploaded_by');       // users.id
            $table->string('uploader_role', 30);              // superadmin | directeur | teacher

            // Métadonnées du livre
            $table->string('title', 255);
            $table->string('author', 255)->nullable();
            $table->string('isbn', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_path', 500)->nullable();   // image de couverture (storage)
            $table->string('file_path', 500);                // fichier principal (PDF, DOCX, EPUB…)
            $table->string('file_type', 20);                 // pdf | docx | epub | pptx | xlsx | other
            $table->unsignedBigInteger('file_size')->default(0); // octets

            // Classification
            $table->string('category', 100)->nullable();      // ex. Mathématiques, Histoire…
            $table->string('level', 100)->nullable();         // ex. Terminale, Licence 1…
            $table->string('language', 10)->default('fr');

            // Accès & droits
            $table->boolean('allow_download')->default(true);
            $table->boolean('is_published')->default(true);

            // Statistiques légères
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('downloads')->default(0);

            $table->softDeletes();
            $table->timestamps();

            // Index fréquents
            $table->index('institution_id');
            $table->index('uploader_role');
            $table->index('category');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};