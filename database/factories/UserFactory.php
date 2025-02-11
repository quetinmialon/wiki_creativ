<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'name'  => $this->faker->name(),
            'email'=> $this->faker->unique()->safeEmail,
            'password' => hash('sha256','password'), // password is 'password'
            'role_id' => Role::factory()->create()->id, // use RoleFactory to create a Role record
        ];
    }
}
