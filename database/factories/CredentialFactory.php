<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Credential;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Credential>
 */
class CredentialFactory extends Factory
{

    protected $model = Credential::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'destination' => $this->faker->url(),
            'username' => $this->faker->safeEmail,
            'password' => Crypt::encryptString('password123'),
            'user_id' => User::factory()->create()->id,
        ];
    }
}
