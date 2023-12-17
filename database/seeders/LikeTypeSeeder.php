<?php

namespace Database\Seeders;

use App\Models\LikeType;
use Illuminate\Database\Seeder;

class LikeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultLikeTypes = [
            [
                'name' => 'Like',
                'icon' => '/images/like.png',
            ],
            [
                'name' => 'Dislike',
                'icon' => '/images/dislike.png',
            ],
        ];

        foreach ($defaultLikeTypes as $defaultLikeType) {
            LikeType::create($defaultLikeType);
        }

    }
}
