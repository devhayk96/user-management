<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->has(
                Article::factory()
                    ->count(3)
                    ->has(
                        Comment::factory()
                            ->count(2)
                            ->for(
                                User::factory()
                            )
                    )
                    ->has(
                        Tag::factory()
                            ->count(2)
                    )
            )
            ->count(5)
            ->create();
    }
}
