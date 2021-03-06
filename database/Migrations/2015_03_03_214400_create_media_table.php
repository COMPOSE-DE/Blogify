<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration {

    private $tableName;

    public function __construct()
    {
        $this->tableName = config('blogify.tables.media');
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
            $table->string('type', 25);
            $table->string('path', 200);
            $table->integer('post_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
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
