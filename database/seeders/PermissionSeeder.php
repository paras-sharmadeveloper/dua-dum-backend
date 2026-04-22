<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ── User Management ──────────────────────────
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // ── Role Management ───────────────────────────
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // ── Permission Management ─────────────────────
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',

            // ── Venue Management ──────────────────────────
            'venue-list',
            'venue-create',
            'venue-edit',
            'venue-delete',

            // ── Location Management ───────────────────────
            'location-list',
            'location-create',
            'location-edit',
            'location-delete',

            // ── Token Management ──────────────────────────
            'token-list',
            'token-create',
            'token-edit',
            'token-delete',
            'token-print',

            // ── Working Lady Management ───────────────────
            'working-lady-list',
            'working-lady-create',
            'working-lady-edit',
            'working-lady-delete',

            // ── Site Admin ────────────────────────────────
            'site-admin',

            // ── Facial Recognition ────────────────────────
            'facial-recognition-list',
            'facial-recognition-mapping',
            'facial-recognition-delete',
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['status' => 'Active']
            );
        }
    }
}
