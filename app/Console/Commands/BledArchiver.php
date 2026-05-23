<?php
namespace App\Console\Commands;
 
use App\Models\Archive;
use App\Models\Institution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
 
class BledArchiver extends Command
{
    protected $signature   = 'bled:archiver {--annee= : Année académique (ex: 2023-2024)} {--institution= : ID établissement}';
    protected $description = 'Génère automatiquement les archives BLED pour tous les établissements';
 
    public function handle(): void
    {
        $annee       = $this->option('annee');
        $institutionId = $this->option('institution');
 
        $query = Institution::query();
        if ($institutionId) { $query->where('id', $institutionId); }
        $institutions = $query->get();
 
        $bledController = app(\App\Http\Controllers\Bledcontroller::class);
 
        foreach ($institutions as $inst) {
            $yr = $annee ?? $inst->academic_year ?? date('Y') . '-' . (date('Y') + 1);
            $this->info("→ Archivage « {$inst->name} » [{$yr}]…");
 
            $categories = ['apprenants', 'enseignants', 'bulletins', 'finances', 'disciplinaire', 'classes', 'planning', 'staff'];
            foreach ($categories as $cat) {
                $csv  = $bledController->generateCsvPublic($inst->id, $cat, $yr, '');
                $path = "archives/{$inst->id}/{$yr}/{$cat}_" . now()->format('Ymd_His') . ".csv";
                Storage::disk('local')->put($path, $csv);
                Archive::create([
                    'institution_id'   => $inst->id,
                    'nom'              => "Archive automatique — {$cat} ({$yr})",
                    'annee_academique' => $yr,
                    'categorie'        => $cat,
                    'type_export'      => 'annuel',
                    'fichier_path'     => $path,
                    'taille_octets'    => Storage::disk('local')->size($path),
                    'cree_par'         => null,
                ]);
                $this->line("   ✓ {$cat}");
            }
        }
 
        $this->info("Archivage terminé.");
    }
}