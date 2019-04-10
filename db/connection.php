<?php

require_once PUBLIC_FILES . '/include/config.php';

class DatabaseConnection {

    private $conn;

    public function __construct($host, $username, $password, $dbname) {
        try {
            $this->conn = new PDO("mysql:$host;dbname=$dbname");
        } catch(Exception $e) {

        }
    }

    public function startTransaction() {

    }

    public function rollback() {

    }

    public function commit() {

    }

    public function execute() {

    }

    public function close() {
        $this->conn = null;
    }
}

$db = new DatabaseConnection('','','','');