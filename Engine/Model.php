<?php

class Model {

    protected $pdo;

    public function __construct($dbConn) {
        $this->pdo = $dbConn;
    }

}
