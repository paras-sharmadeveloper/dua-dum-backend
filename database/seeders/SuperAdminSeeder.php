<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Run Permission Seeder ──────────────────────
        $this->call(PermissionSeeder::class);

        // ── 2. Create Super Admin Role ────────────────────
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );

        // Give ALL permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        $this->command->info('✅ Super Admin role assigned all permissions.');

        // ── 3. Create Super Admin User ────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@bookduatoken.org'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                // ❌ Remove status from here too if users table has no status
            ]
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        // ── 4. Create common roles with default permissions ───
        $roles = [
            'Admin' => [
                'user-list','user-create','user-edit','user-delete',
                'venue-list','venue-create','venue-edit','venue-delete',
                'location-list','location-create','location-edit','location-delete',
                'token-list','token-create','token-edit','token-delete','token-print',
                'working-lady-list','working-lady-create','working-lady-edit','working-lady-delete',
                'facial-recognition-list','facial-recognition-mapping','facial-recognition-delete',
            ],
            'Site Admin' => [
                'venue-list',
                'token-list','token-create','token-print',
                'working-lady-list',
                'site-admin',
                'facial-recognition-list','facial-recognition-mapping',
            ],
            'Field Admin' => [
                'token-list','token-create','token-print',
                'working-lady-list',
            ],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $permissionNames)->get());
            $this->command->info("✅ Role seeded: {$roleName}");
        }

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════╗');
        $this->command->info('║       SUPER ADMIN CREATED ✅          ║');
        $this->command->info('╠══════════════════════════════════════╣');
        $this->command->info('║  Email   : admin@bookduatoken.org    ║');
        $this->command->info('║  Password: Admin@12345               ║');
        $this->command->info('╚══════════════════════════════════════╝');
    }
}
