<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHaveRolesTable extends Migration
{
    private $tableName;

    public function __construct()
    {
        $this->tableName = config('blogify.tables.role_user');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName))
        {
            Schema::create($this->tableName, function ($table)
            {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->integer('role_id')->unsigned()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
