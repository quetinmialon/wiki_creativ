<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{

    protected $model = Favorite::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=> User::factory()->create()->id,
            'document_id'=> Document::factory()->create()->id
        ];
    }
}
