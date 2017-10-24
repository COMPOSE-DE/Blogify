<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use BlogifyStatusModel;

class StatusesTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Status
     */
    private $statuses;

    public function __construct(BlogifyStatusModel $statuses)
    {
        $this->statuses = $statuses;
    }


    public function run()
    {
        $this->statuses->create(['name' => $this->statuses->getDraftStatusName()]);
        $this->statuses->create(['name' => $this->statuses->getPendingStatusName()]);
        $this->statuses->create(['name' => $this->statuses->getReviewedStatusName()]);
    }
}
