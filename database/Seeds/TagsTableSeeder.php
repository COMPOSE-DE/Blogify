<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use BlogifyTagModel;

class TagsTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Tag
     */
    private $tags;

    public function __construct(BlogifyTagModel $tags)
    {
        $this->tags = $tags;
    }

    public function run()
    {
        $this->tags->firstOrcreate(['name' => 'bg']);
        $this->tags->firstOrcreate(['name' => 'en']);
    }
}
