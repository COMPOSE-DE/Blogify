<?php

namespace Donatix\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Donatix\Blogify\Models\Tag;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        Tag::firstOrcreate(['name' => 'bg']);
        Tag::firstOrcreate(['name' => 'en']);
    }
}
