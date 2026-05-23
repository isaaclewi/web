<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Institutions
            'manage institution',

            // Users
            'manage users',

            // Students
            'create student',
            'view student',
            'edit student',
            'delete student',

            // Grades
            'create grade',
            'view grade',

            // Classes & subjects
            'manage classes',
            'manage subjects',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'super_admin',
            'admin',
            'teacher',
            'parent',
            'student',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Attribution permissions
        Role::findByName('super_admin')->givePermissionTo(Permission::all());

        Role::findByName('admin')->givePermissionTo([
            'manage users',
            'create student',
            'view student',
            'edit student',
            'manage classes',
            'manage subjects',
            'view grade',
        ]);

        Role::findByName('teacher')->givePermissionTo([
            'create grade',
            'view student',
        ]);

        Role::findByName('parent')->givePermissionTo([
            'view student',
            'view grade',
        ]);

        Role::findByName('student')->givePermissionTo([
            'view grade',
        ]);
    }
}
