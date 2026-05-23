<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;
use App\Models\Student;
use App\Models\Parent as ParentModel;
use App\Models\SchoolParent;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rôles
        |--------------------------------------------------------------------------
        */
        $roles = ['super_admin', 'admin', 'parent', 'student', 'teacher'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Institution par défaut
        |--------------------------------------------------------------------------
        */
        $institution = Institution::first();

        if (!$institution) {
            $institution = Institution::create([
                'name' => 'École Démo',
                'status' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@syntriforg.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'institution_id' => null,
                'status' => true,
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        /*
        |--------------------------------------------------------------------------
        | Admin Institution
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'admin@ecole.com'],
            [
                'name' => 'Admin École',
                'password' => Hash::make('password'),
                'institution_id' => $institution->id,
                'status' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        /*
        |--------------------------------------------------------------------------
        | Enseignant
        |--------------------------------------------------------------------------
        */
        $teacher = User::firstOrCreate(
            ['email' => 'enseignant@ecole.com'],
            [
                'name' => 'Enseignant Démo',
                'password' => Hash::make('password'),
                'institution_id' => $institution->id,
                'status' => true,
            ]
        );
        $teacher->syncRoles(['teacher']);

        /*
        |--------------------------------------------------------------------------
        | Parent
        |--------------------------------------------------------------------------
        */
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@ecole.com'],
            [
                'name' => 'Parent Démo',
                'password' => Hash::make('password'),
                'institution_id' => $institution->id,
                'status' => true,
            ]
        );
        $parentUser->syncRoles(['parent']);

        $parent = SchoolParent::firstOrCreate([
            'user_id' => $parentUser->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Étudiant
        |--------------------------------------------------------------------------
        */
        $studentUser = User::firstOrCreate(
            ['email' => 'etudiant@ecole.com'],
            [
                'name' => 'Étudiant Démo',
                'password' => Hash::make('password'),
                'institution_id' => $institution->id,
                'status' => true,
            ]
        );
        $studentUser->syncRoles(['student']);

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'institution_id' => $institution->id,
                'matricule' => 'STD-0001',
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Lien Parent ↔ Étudiant
        |--------------------------------------------------------------------------
        */
        $parent->students()->syncWithoutDetaching([$student->id]);
    }
}
