<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Visibility;

class VisibilityTableSeeder extends Seeder
{
    public function run()
    {
        Visibility::create(['name' => Visibility::PUBLIC]);
        Visibility::create(['name' => Visibility::PROTECTED]);
        Visibility::create(['name' => Visibility::PRIVATE]);
        Visibility::create(['name' => Visibility::RECOMMENDED]);
    }
}
