<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Site::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'nginx_conf' => 'default',
        'php_version_id' => function () {
            return factory(\App\Models\PhpVersion::class)->create()->id;
        },
        'secure' => false,
    ];
});
