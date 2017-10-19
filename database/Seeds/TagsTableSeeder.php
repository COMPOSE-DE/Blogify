<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Tag;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        Tag::firstOrcreate(['name' => 'bg']);
        Tag::firstOrcreate(['name' => 'en']);
    }
}
