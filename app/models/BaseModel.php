<?php
require_once 'app/Config/Database.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
}
?>