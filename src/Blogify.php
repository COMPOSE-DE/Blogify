<?php

namespace Donatix\Blogify;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\DatabaseManager;

class Blogify
{

    /**
     * Holds the available char sets
     *
     * @var mixed
     */
    protected $char_sets;

    /**
     * @var \Illuminate\Database\Connection
     */
    protected $db;

    public function __construct(DatabaseManager $db, Config $config)
    {
        $this->char_sets = $config['blogify']['char_sets'];
        $this->db = $db->connection();
    }

    /**
     * @param null $table
     * @param null $field
     * @param bool $unique
     * @param int $min
     * @param int $max
     * @return string
     */
    public function makeHash($table = null, $field = null, $unique = false, $min = 5, $max = 10)
    {
        if (! $unique) {
            return str_random(rand($min, $max));
        }

       do {
            $hash = str_random(rand($min, $max));
        } while($this->db->table($table)->where($field, '=', $hash)->exists());

        return $hash;
    }
}
