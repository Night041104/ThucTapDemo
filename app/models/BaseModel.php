<?php
class BaseModel {
    protected $conn;

    public function __construct() {
        // 1. Dùng __DIR__ để đi từ thư mục 'models' ra ngoài 'config'
        // Đảm bảo không bao giờ sai đường dẫn dù gọi từ đâu
        require_once __DIR__ . '/../../config/db.php';
        
        $this->conn = Database::getInstance()->conn;
    }

    // 2. Hàm dọn dẹp dữ liệu (Giúp Model con code ngắn hơn)
    // Thay vì viết: $this->conn->real_escape_string($str)
    // Chỉ cần viết: $this->escape($str)
    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    // 3. (Tùy chọn) Hàm chạy SQL có bắt lỗi
    // Giúp debug dễ hơn nếu viết sai câu lệnh SQL
    public function _query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            // Khi đang code thì hiện lỗi ra màn hình cho dễ sửa
            die("Lỗi SQL: " . $this->conn->error . "<br>Câu lệnh: " . $sql);
             
        }
        return $result;
    }
    // Trong app/models/BaseModel.php
    public function createSlug($str) {
        if (!$str) return '';
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}
?>