<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PhpVersion::class, function (Faker $faker) {
    return [
        'version_number' => $faker->randomElement(['5.6', '7.0', '7.1', '7.2']),
        'default'        => 'false',
    ];
});

$factory->state(\App\Models\PhpVersion::class, 'default', function () {
    return [
        'default' => true,
    ];
});
