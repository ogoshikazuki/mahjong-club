<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GameResult;
use Faker\Generator as Faker;

$factory->define(GameResult::class, function (Faker $faker) {
    return [
        'rate' => $faker->randomElement(Constant\Rate::getConstants()),
    ];
});
