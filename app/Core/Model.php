<?php

namespace Core;

use Helpers\Database;

abstract class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::get();
    }
}
