<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Yönetici',
                'slug' => 'admin',
                'description' => 'Sistem genelinde tam yetkili kullanıcı',
                'is_active' => true,
            ],
            [
                'name' => 'Akademisyen',
                'slug' => 'academic',
                'description' => 'Akademik içerik ve topluluk yönetimi yetkilerine sahip kullanıcı',
                'is_active' => true,
            ],
            [
                'name' => 'Öğrenci',
                'slug' => 'student',
                'description' => 'Aktif öğrenci kullanıcısı',
                'is_active' => true,
            ],
            [
                'name' => 'Mezun',
                'slug' => 'graduate',
                'description' => 'Mezun kullanıcı',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}