<?php

class DbConn {

    private $host;
    private $database;
    private $user;
    private $password;
    private $encoding;

    public function __construct($connData, $encoding = 'utf8') {
        $this->host = $connData->host;
        $this->database = $connData->database;
        $this->user = $connData->user;
        $this->password = $connData->password;
        $this->encoding = $encoding;
    }

    public function Connect() {
        try {
            $dbh = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';', $this->user, $this->password);
            $dbh->exec("set names " . $this->encoding);
            return $dbh;
        } catch (Exception $exc) {
            die('Uh-oh...something terrible happened...<br />' . $exc->getMessage());
        }
    }

}
