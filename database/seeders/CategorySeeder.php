<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Living Room',
                'slug' => 'living-room',
                'description' => 'Furniture and decor for your living room',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Bedroom',
                'slug' => 'bedroom',
                'description' => 'Bedroom furniture and accessories',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Dining Room',
                'slug' => 'dining-room',
                'description' => 'Dining tables, chairs, and dining room furniture',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Kitchen',
                'slug' => 'kitchen',
                'description' => 'Kitchen furniture and storage solutions',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Office',
                'slug' => 'office',
                'description' => 'Office furniture and workspace solutions',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
