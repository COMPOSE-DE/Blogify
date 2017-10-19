<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BlogifySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('ComposeDe\Blogify\database\Seeds\RolesTableSeeder');
        $this->call('ComposeDe\Blogify\database\Seeds\UsersTableSeeder');
        $this->call('ComposeDe\Blogify\database\Seeds\StatusesTableSeeder');
        $this->call('ComposeDe\Blogify\database\Seeds\VisibilityTableSeeder');
        $this->call('ComposeDe\Blogify\database\Seeds\TagsTableSeeder');
    }
}
