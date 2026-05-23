<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Filiere;
use App\Models\Institution;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        // Institution secondaire uniquement
        $institution = Institution::where('type', 'secondaire')->first();

        if (!$institution) {
            $this->command->error("Aucune institution secondaire trouvée.");
            return;
        }

        // 20 filières (orientées secondaire)
        $filieres = [
            'Scientifique',
            'Littéraire',
            'Commerciale',
            'Pédagogique',
            'Technique Industrielle',
            'Électricité',
            'Mécanique',
            'Bâtiment',
            'Secrétariat',
            'Informatique',
            'Agriculture',
            'Élevage',
            'Hôtellerie',
            'Tourisme',
            'Arts Plastiques',
            'Musique',
            'Mode et Couture',
            'Coiffure',
            'Esthétique',
            'Menuiserie',
        ];

        foreach ($filieres as $name) {
            Filiere::firstOrCreate([
                'institution_id' => $institution->id,
                'name' => $name,
            ]);
        }

        $this->command->info("✅ 20 filières secondaires créées avec succès.");
    }
}