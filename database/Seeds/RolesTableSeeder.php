<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $roles;

    public function __construct(Role $roles)
    {
        $this->roles = $roles;
    }

    public function run()
    {
        $this->roles->firstOrcreate(['name' => $this->roles->getAdminRoleName()]);
        $this->roles->firstOrcreate(['name' => $this->roles->getAuthorRoleName()]);
        $this->roles->firstOrcreate(['name' => $this->roles->getReviewerRoleName()]);
        $this->roles->firstOrcreate(['name' => $this->roles->getMemberRoleName()]);
    }
}
