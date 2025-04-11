<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'excerpt' => $this->faker->sentence(6),
            'content' => $this->faker->paragraph(3),
            'created_by' => User::factory(),
        ];
    }
}
