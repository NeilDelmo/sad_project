<?php
// database/seeders/ForumCategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumCategory;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Equipment & Maintenance',
                'description' => 'Discuss boats, engines, nets, and gear upkeep.'
            ],
            [
                'name' => 'Fish & Fishing Spots',
                'description' => 'Share your favorite fishing locations and what’s biting!'
            ],
            [
                'name' => 'FAQ & Advice',
                'description' => 'Ask questions or share fishing knowledge with others.'
            ],
            [
                'name' => 'General Discussion',
                'description' => 'Anything else related to fishing life and community.'
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
