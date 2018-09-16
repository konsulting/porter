<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Setting::class, function (Faker $faker) {
    return [
        'name'  => $faker->word,
        'value' => $faker->word,
    ];
});
