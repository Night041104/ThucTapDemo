<?php
    Class Database {
        private static $instance = null; #sử dụng mẫu singleton để chỉ tạo đúng 1 kết nốt trong quá trình chay web
        public $conn;
        private function __construct(){
            $hostname = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = 'shop_mobile_test';
            $this->conn = new  mysqli($hostname,$username,$password,$dbname);
            if($this->conn->connect_error){
                die("Kết nốt tới sql thất bại: " .$this->conn->connect_error);
            }
            $this->conn->set_charset("UTF8");
        }
        public static function getInstance(){ #mẫu singleton 
            if(!self::$instance)
            {
                self::$instance = new Database();
            }
            return self::$instance;
        }
    }
?>
