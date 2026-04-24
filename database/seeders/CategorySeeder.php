<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Seminar', 'description' => 'Seminar dan workshop pengembangan diri'],
            ['name' => 'Konser', 'description' => 'Konser musik dan pertunjukan'],
            ['name' => 'Olahraga', 'description' => 'Event dan kompetisi olahraga'],
            ['name' => 'Pameran', 'description' => 'Pameran seni, budaya, dan teknologi'],
            ['name' => 'Komunitas', 'description' => 'Gathering dan event komunitas'],
            ['name' => 'Pendidikan', 'description' => 'Event pendidikan dan pelatihan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}