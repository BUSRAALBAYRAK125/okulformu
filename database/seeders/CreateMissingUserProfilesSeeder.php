<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class CreateMissingUserProfilesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();

        foreach ($users as $user) {
            UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => null,
                    'bio' => null,
                    'photo' => null,
                    'cover_photo' => null,
                    'city' => null,
                    'linkedin_url' => null,
                    'github_url' => null,
                    'website_url' => null,
                ]
            );
        }
    }
}