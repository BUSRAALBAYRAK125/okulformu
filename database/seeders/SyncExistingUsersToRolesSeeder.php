<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SyncExistingUsersToRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roleMap = [
            'admin' => 'admin',
            'academic' => 'academic',
            'student' => 'student',
            'graduate' => 'graduate',
        ];

        $roles = Role::query()
            ->whereIn('slug', array_values($roleMap))
            ->get()
            ->keyBy('slug');

        $users = User::query()->get();

        foreach ($users as $user) {
            $roleSlug = $roleMap[$user->user_type] ?? null;

            if (!$roleSlug) {
                continue;
            }

            $role = $roles->get($roleSlug);

            if (!$role) {
                continue;
            }

            $alreadyAttached = $user->roles()
                ->where('roles.id', $role->id)
                ->exists();

            if ($alreadyAttached) {
                $user->roles()->updateExistingPivot($role->id, [
                    'is_active' => true,
                    'assigned_at' => now(),
                ]);

                continue;
            }

            $user->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}