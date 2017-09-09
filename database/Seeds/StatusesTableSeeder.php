<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Status;

class StatusesTableSeeder extends Seeder
{

    public function run()
    {
        Status::create([
            "hash" => str_random(),
            "name" => "Draft",
        ]);

        Status::create([
            "hash" => str_random(),
            "name" => "Pending review",
        ]);

        Status::create([
            "hash" => str_random(),
            "name" => "Reviewed",
        ]);
    }
}
