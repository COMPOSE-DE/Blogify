<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * @var array
     */
    protected $fields;

    public function __construct()
    {
        $this->fillFieldsArray();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('users')) {
            $this->createUsersTable();
        } else {
            $this->updateUsersTable();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

    /**
     * Fill the fields array
     */
    private function fillFieldsArray()
    {
        $this->fields =  [
            'id' => [
                'type' => 'increments',
            ],
            'email' => [
                'type' => 'string',
                'length' => 70,
                'extra' => 'unique'
            ],
            'password' => [
                'type' => 'string',
                'length' => 100,
            ],
            'remember_token' => [
                'type' => 'string',
                'length' => 100,
                'extra' => 'nullable'
            ],
            'role_id' => [
                'type' => 'integer',
                'extra' => 'unsigned'
            ],
            'profilepicture' => [
                'type' => 'string',
                'length' => 200,
                'extra' => 'nullable'
            ],
        ];
    }

    /**
     * Create a new Users table with
     * all the required fields
     */
    private function createUsersTable()
    {
        Schema::create('users', function ($table) {
            foreach ($this->fields as $field => $value) {
                $query = $table->{$value['type']}($field);

                if (isset($value['extra'])) {
                    $query->{$value['extra']}();
                }
            }

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Add the not existing columns
     * to the existing users table
     */
    private function updateUsersTable()
    {
        Schema::table('users', function ($table) {
            foreach ($this->fields as $field => $value) {
                if (!Schema::hasColumn('users', $field)) {
                    $type  = $value['type'];
                    $query = $table->$type($field);

                    if (isset($value['extra'])) {
                        $extra = $value['extra'];
                        $query->$extra();
                    }
                }
            }

            if (!Schema::hasColumn('users', 'created_at') && !Schema::hasColumn('users', 'updated_at')) {
                $table->timestamps();
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

}
