<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPrivacySetting;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class RegisterUserService
{
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'surname' => $data['surname'],
                'email' => $data['email'],
                'password' => $data['password'],
                'user_type' => $data['user_type'],
                'student_no' => $data['student_no'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'department' => 'Bilgisayar Öğretmenliği',
                'approval_status' => 'pending',
            ]);

            $role = Role::query()
                ->where('slug', $data['user_type'])
                ->where('is_active', true)
                ->first();

            if ($role) {
                $user->roles()->syncWithoutDetaching([
                    $role->id => [
                        'assigned_at' => now(),
                        'assigned_by' => null,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            UserProfile::create([
                'user_id' => $user->id,
                'headline' => null,
                'bio' => null,
                'photo' => null,
                'cover_photo' => null,
                'city' => null,
                'linkedin_url' => null,
                'github_url' => null,
                'website_url' => null,
            ]);

            UserPrivacySetting::create([
                'user_id' => $user->id,
                'profile_visibility' => 'public',
                'email_visibility' => 'private',
                'clubs_visibility' => 'public',
                'connections_visibility' => 'private',
                'social_links_visibility' => 'public',
            ]);

            return $user;
        });
    }
}