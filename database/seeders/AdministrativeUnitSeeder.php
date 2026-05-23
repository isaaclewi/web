<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdministrativeUnit;
use App\Models\Institution;

class AdministrativeUnitSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::first();

        $units = [
            ['name' => 'Direction', 'type' => 'direction'],
            ['name' => 'Scolarité', 'type' => 'scolarite'],
            ['name' => 'Intendance', 'type' => 'finances'],
            ['name' => 'Discipline', 'type' => 'discipline'],
            ['name' => 'Pédagogie', 'type' => 'pedagogie'],
            ['name' => 'Archives', 'type' => 'archives'],
        ];

        foreach ($units as $unit) {
            AdministrativeUnit::firstOrCreate(
                [
                    'institution_id' => $institution->id,
                    'name' => $unit['name'],
                ],
                [
                    'type' => $unit['type'],
                    'status' => true,
                ]
            );
        }
    }
}
