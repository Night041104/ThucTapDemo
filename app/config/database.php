<?php
class Database {
    private $host = "localhost";
    private $db_name = "shop_mobile_test";
    private $username = "root";
    private $password = "";
    private $port = "3307"; // Đã set cứng port 3307

    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối Database: " . $exception->getMessage();
            exit();
        }
        return $this->conn;
    }
}
?>