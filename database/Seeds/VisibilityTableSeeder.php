<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Visibility;

class VisibilityTableSeeder extends Seeder
{
    public function run()
    {
        Visibility::create([
            "hash" => str_random(),
            "name" => Visibility::PUBLIC,
        ]);

        Visibility::create([
            "hash" => str_random(),
            "name" => Visibility::PROTECTED,
        ]);

        Visibility::create([
            "hash" => str_random(),
            "name" => Visibility::PRIVATE,
        ]);

        Visibility::create([
            "hash" => str_random(),
            "name" => Visibility::RECOMMENDED,
        ]);
    }
}
