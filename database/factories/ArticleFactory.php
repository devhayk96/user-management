<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(4, true),
            'description' => $this->faker->paragraph(),
            'image_path' => $this->faker->imageUrl(),
            'article_type_id' => $this->faker->randomElement(ArticleType::all())['id'],
        ];
    }
}
