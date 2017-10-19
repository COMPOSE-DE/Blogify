<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Status;

class StatusesTableSeeder extends Seeder
{
    public function run()
    {
        Status::create(['name' => Status::DRAFT]);
        Status::create(['name' => Status::PENDING]);
        Status::create(['name' => Status::REVIEWED]);
    }
}
