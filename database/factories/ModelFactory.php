<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'is_active' => true,
        'is_admin' => false
    ];
});

$factory->define(\App\Poll::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->realText(20),
        'description' => $faker->realText(),
    ];
});

$factory->define(\App\Page::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->realText(20),
    ];
});

