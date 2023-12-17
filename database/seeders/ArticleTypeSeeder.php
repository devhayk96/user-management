<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultArticleTypes = [
            'blogs',
            'news',
        ];

        foreach ($defaultArticleTypes as $defaultArticleType) {
            ArticleType::create([
                'title' => $defaultArticleType
            ]);
        }
    }
}
