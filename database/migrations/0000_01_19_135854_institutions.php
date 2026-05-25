<?php

// database/migrations/xxxx_xx_xx_create_institutions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('name');
            $table->string('code')->unique(); // matricule ou code interne
            $table->enum('type', ['primaire', 'secondaire', 'universite', 'centre','lycee','autre']);
            $table->enum('statut_juridique', ['public', 'prive', 'confessionnel']);

            // Année scolaire
            $table->string('academic_year');

            // Localisation
            $table->string('pays')->default('Congo');
            $table->string('departement');
            $table->string('commune');
            $table->string('adresse');

            // Contact
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('site_web')->nullable();

            // Infos diverses
            $table->string('devise')->nullable();
            $table->date('date_creation')->nullable();
            $table->boolean('autorisation_etat')->default(false);

            // Médias & statut
            $table->string('logo')->nullable();
            $table->boolean('status')->default(true);
            $table->string('description')->nullable();
            $table->string('historique')->nullable();
            $table->string('mission')->nullable();
            $table->string('vision')->nullable();
            $table->string('valeurs')->nullable();
            $table->string('partenariats')->nullable();
            $table->string('realisations')->nullable();
            $table->string('projets')->nullable();
            $table->string('evenements')->nullable();
            $table->string('actualites')->nullable();
            $table->string('temoignages')->nullable();
            $table->string('faq')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('social_links')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
