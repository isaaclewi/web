<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Niveau;
use App\Models\Institution;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        // Associe au premier établissement, ou adapte selon ton besoin
        $institution = Institution::first();

        if (! $institution) {
            $this->command->warn('Aucune institution trouvée — seeder ignoré.');
            return;
        }

        $classes  = ['6e', '5e', '4e', '3e', '2nde', '1ère', 'Terminale'];
        $series   = ['A', 'C', 'D', 'E', 'F'];
        $groupes  = ['A', 'B', 'C', 'D'];
        $niveaux  = [];

        foreach ($classes as $classe) {
            foreach ($series as $serie) {
                foreach ($groupes as $groupe) {
                    $niveaux[] = [
                        'institution_id' => $institution->id,
                        'name'           => "{$classe} {$serie}{$groupe}",
                        'cycle'          => 'secondaire',
                    ];
                    if (count($niveaux) >= 50) break 3;
                }
            }
        }

        foreach ($niveaux as $niveau) {
            Niveau::firstOrCreate(
                // Clé d'unicité : nom + institution
                ['name' => $niveau['name'], 'institution_id' => $niveau['institution_id']],
                ['cycle' => $niveau['cycle']]
            );
        }

        $this->command->info('✅ 50 niveaux secondaires générés pour : ' . $institution->name);
    }
}