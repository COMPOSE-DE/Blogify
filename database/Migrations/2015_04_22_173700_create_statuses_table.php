<?php

use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration {

    private $tableName;

    public function __construct()
    {
        $this->tableName = config('blogify.tables.statuses');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function($table)
        {
            $table->increments('id');
            $table->string('hash', 80)->unique();
            $table->string('name', 25)->unique();
        });
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
