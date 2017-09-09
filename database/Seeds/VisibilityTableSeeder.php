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
            "name" => "Public",
        ]);

        Visibility::create([
            "hash" => str_random(),
            "name" => "Protected",
        ]);

        Visibility::create([
            "hash" => str_random(),
            "name" => "Private",
        ]);
    }
}
