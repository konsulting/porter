<?php

namespace Database\Factories;

use App\Models\PhpVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhpVersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PhpVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'version_number' => $this->faker->randomElement(['5.6', '7.0', '7.1', '7.2']),
            'default'        => 'false',
        ];
    }

    public function default()
    {
        return $this->state(function (array $attributes) {
            return [
                'default' => true,
            ];
        });
    }
}
