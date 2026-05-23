<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        Institution::firstOrCreate(
            [
                'code' => 'IPS-001', // Clé unique logique
            ],
            [
                // Identification
                'name' => 'Institut Pilote Syntriforg',
                'type' => 'secondaire',
                'statut_juridique' => 'prive',

                // Année académique
                'academic_year' => '2025-2026',

                // Localisation
                'pays' => 'Congo',
                'departement' => 'Brazzaville',
                'commune' => 'Bacongo',
                'adresse' => 'Avenue des Écoles, Bacongo',

                // Contact
                'email' => 'contact@syntriforg.edu',
                'telephone' => '+242060000000',
                'site_web' => 'https://www.syntriforg.edu',

                // Infos institutionnelles
                'devise' => 'Former pour transformer',
                'date_creation' => '2015-09-15',
                'autorisation_etat' => true,

                // Médias & identité
                'logo' => 'logo_syntriforg.png',
                'status' => true,

                // Présentation
                'description' => 'Établissement d’enseignement moderne axé sur l’excellence académique et l’innovation pédagogique.',
                'historique' => 'Fondé en 2015 à Brazzaville pour répondre aux besoins éducatifs émergents.',
                'mission' => 'Offrir une éducation de qualité adaptée aux réalités africaines.',
                'vision' => 'Devenir une référence éducative en Afrique centrale.',
                'valeurs' => 'Discipline, Excellence, Innovation, Intégrité.',
                'partenariats' => 'Partenariats avec universités et ONG locales.',
                'realisations' => 'Taux de réussite supérieur à 90% au baccalauréat.',
                'projets' => 'Digitalisation complète du système académique.',
                'evenements' => 'Journées scientifiques, forums carrière.',
                'actualites' => 'Ouverture prochaine d’un nouveau campus.',
                'temoignages' => 'Des anciens élèves témoignent de leur réussite.',
                'faq' => 'Questions fréquentes disponibles sur le site web.',
                'contact_info' => 'Service administratif ouvert du lundi au vendredi.',
                'social_links' => 'facebook.com/syntriforg | linkedin.com/syntriforg',
            ]
        );
    }
}