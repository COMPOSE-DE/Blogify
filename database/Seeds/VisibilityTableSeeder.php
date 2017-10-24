<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use BlogifyVisibilityModel;

class VisibilityTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Visibility
     */
    private $visibilities;

    public function __construct(BlogifyVisibilityModel $visibilities)
    {
        $this->visibilities = $visibilities;
    }


    public function run()
    {
        $this->visibilities->create(['name' => $this->visibilities->getPublicVisibilityName()]);
        $this->visibilities->create(['name' => $this->visibilities->getProtectedVisibilityName()]);
        $this->visibilities->create(['name' => $this->visibilities->getPrivateVisibilityName()]);
        $this->visibilities->create(['name' => $this->visibilities->getRecommendedVisibilityName()]);
    }
}
