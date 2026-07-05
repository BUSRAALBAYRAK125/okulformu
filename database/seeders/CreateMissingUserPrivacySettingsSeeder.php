<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPrivacySetting;
use Illuminate\Database\Seeder;

class CreateMissingUserPrivacySettingsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();

        foreach ($users as $user) {
            UserPrivacySetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_visibility' => 'public',
                    'email_visibility' => 'private',
                    'clubs_visibility' => 'public',
                    'connections_visibility' => 'private',
                    'social_links_visibility' => 'public',
                ]
            );
        }
    }
}