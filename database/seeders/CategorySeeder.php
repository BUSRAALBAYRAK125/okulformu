<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Genel Duyurular',
                'slug' => 'genel-duyurular',
                'description' => 'Bölüm ve forum duyuruları',
                'sort_order' => 1,
            ],
            [
                'name' => 'Dersler ve Sınavlar',
                'slug' => 'dersler-ve-sinavlar',
                'description' => 'Ders, sınav ve akademik içerikler',
                'sort_order' => 2,
            ],
            [
                'name' => 'Staj ve Kariyer',
                'slug' => 'staj-ve-kariyer',
                'description' => 'Staj, iş ve kariyer paylaşımları',
                'sort_order' => 3,
            ],
            [
                'name' => 'Kaynak Paylaşımı',
                'slug' => 'kaynak-paylasimi',
                'description' => 'Not, belge ve kaynak paylaşımları',
                'sort_order' => 4,
            ],
            [
                'name' => 'Serbest Tartışma',
                'slug' => 'serbest-tartisma',
                'description' => 'Genel sohbet ve serbest başlıklar',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}