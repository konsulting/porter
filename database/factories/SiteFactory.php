<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\PhpVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'           => $this->faker->word,
            'nginx_conf'     => 'default',
            'php_version_id' => function () {
                return PhpVersion::factory()->create()->id;
            },
            'secure' => false,
        ];
    }
}
