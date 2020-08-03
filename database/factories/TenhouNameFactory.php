<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TenhouName;
use Faker\Generator as Faker;

$factory->define(TenhouName::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
