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
            ['email' => 'admin@bookduatoken.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                // ❌ Remove status from here too if users table has no status
            ]
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════╗');
        $this->command->info('║       SUPER ADMIN CREATED ✅          ║');
        $this->command->info('╠══════════════════════════════════════╣');
        $this->command->info('║  Email   : admin@bookduatoken.com    ║');
        $this->command->info('║  Password: Admin@12345               ║');
        $this->command->info('╚══════════════════════════════════════╝');
    }
}
