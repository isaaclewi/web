<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration OPTIONNELLE — ajoute les colonnes `avatar` et `phone` à la table users
 * si elles n'existent pas déjà.
 *
 * Votre modèle User a déjà `phone` dans $fillable, donc cette migration
 * est fournie pour ceux qui ne l'ont pas encore en base.
 *
 * Lancez : php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Colonne phone (peut déjà exister)
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }

            // Colonne avatar
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar', 500)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'avatar')) $columns[] = 'avatar';
            if (! empty($columns)) $table->dropColumn($columns);
        });
    }
};