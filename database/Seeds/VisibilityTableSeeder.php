<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Visibility;

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
