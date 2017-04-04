<?php

namespace Models;

class BaseModel{
    protected $db;

    function __construct($db){
        $this->db = $db;
    }
}