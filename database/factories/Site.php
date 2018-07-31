<?php

use Faker\Generator as Faker;

$factory->define(App\Site::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'nginx_type' => 'default',
        'php_version_id' => function () {
            return factory(App\PhpVersion::class)->create()->id;
        },
        'secure' => false,
    ];
});
