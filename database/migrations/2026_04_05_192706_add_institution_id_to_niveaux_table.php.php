<?php
// database/migrations/xxxx_add_institution_id_to_niveaux_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            $table->foreignId('institution_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('institutions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Institution::class);
            $table->dropColumn('institution_id');
        });
    }
};