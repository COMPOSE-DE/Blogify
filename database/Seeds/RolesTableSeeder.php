<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use BlogifyRoleModel;

class RolesTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $roles;

    public function __construct(BlogifyRoleModel $roles)
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
