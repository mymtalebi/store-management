<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */
use App\Models\Store;

/*
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

/*
 * Here you may define all of your model factories. Model factories give
 * you a convenient way to create models for testing and seeding your
 * database. Just tell the factory how a default model should look.
 */
$factory->defineAs(Store::class, 'root', function (Faker\Generator $faker) {
    $id = $faker->randomDigitNotNull;

    return [
        'id' => $id,
        // prepend id to make sure it'll be unique.
        'name' => $id.$faker->word,
        'parent_id' => NULL,
        'root_id' => $id,
        'lft' => 1,
        'rgt' => 2,
    ];
});

$factory->define(Store::class, function (Faker\Generator $faker) use ($factory) {
    $parent = $factory->rawOf(Store::class, 'root', [
        'rgt' => 4,
    ]);

    $id = $faker->randomDigitNotNull;

    return [
        'id' => $id,
        // prepend id to make sure it will be unique.
        'name' => $id.$faker->word,
        'parent_id' => $parent['id'],
        'root_id' => $parent['id'],
        'lft' => 2,
        'rgt' => 3,
    ];
});
