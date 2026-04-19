<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'access admin',
            'manage users',
            'manage roles',
            'manage platform settings',
            'manage content',
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super admin gets all permissions via a gate usually, but we'll assign them all here for clarity
        $superAdminRole->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'Admin'])->syncPermissions([
            'access admin',
            'manage content',
            'view dashboard',
        ]);

        Role::firstOrCreate(['name' => 'Student'])->syncPermissions(['view dashboard']);
        Role::firstOrCreate(['name' => 'Researcher'])->syncPermissions(['view dashboard', 'manage content']);
        Role::firstOrCreate(['name' => 'Educator'])->syncPermissions(['view dashboard', 'manage content']);
        Role::firstOrCreate(['name' => 'Contributor'])->syncPermissions(['view dashboard', 'manage content']);

        // Create Default Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@anthroconnect.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'status' => 'active',
                'user_type' => 'researcher', // Example user_type
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create Default Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@anthroconnect.com'],
            [
                'name' => 'Standard Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'user_type' => 'educator',
            ]
        );
        $admin->assignRole('Admin');
    }
}
