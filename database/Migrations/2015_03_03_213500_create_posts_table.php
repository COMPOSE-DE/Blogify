<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

    private $tableName;

    public function __construct()
    {
        $this->tableName = config('blogify.tables.posts');
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
            $table->string('title', 100);
            $table->string('slug', 120)->unique();
            // $table->text('short_description');
            $table->longtext('content');
            $table->unsignedInteger('views_count')->default(0)->index();
            $table->integer('user_id')->unsigned();
            $table->integer('reviewer_id');
            $table->integer('category_id')->unsigned();
            $table->integer('status_id');
            $table->integer('visibility_id')->index();
            $table->integer('being_edited_by')->nullable()->default(null);
            $table->string('password')->nullable();
            $table->timestamp('publish_date')->nullable()->index();
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
