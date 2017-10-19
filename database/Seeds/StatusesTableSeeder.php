<?php

namespace ComposeDe\Blogify\database\Seeds;

use Illuminate\Database\Seeder;
use ComposeDe\Blogify\Models\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * @var \ComposeDe\Blogify\Models\Status
     */
    private $statuses;

    public function __construct(Status $statuses)
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
