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
    public function createSlug($str){
        if (!$str) return '';
        //Chuyển sang chữ thường
        $str = mb_strtolower($str);
        //Xử lý riêng chữ đ/Đ vì iconv thường bỏ sót nó
        $str = preg_replace('/(đ|đ)/u', 'd', $str);
        //Dùng hàm iconv để lột bỏ dấu tiếng việt 
        //ASCII//TRANSLIT để cố gắng chuyển kí tự lạ sang kí tự tương đương trong bảng ACSCII
        $str = iconv('UTF-8', 'ASCII//TRANSLIT',$str);
        //Xóa các kí tự ko phải chử cái hoặc số
        $str = preg_replace('/[^a-z0-9\- ]/', '', $str);
        //Thay khoảng trắng bằng dấu gạch ngang
        $str = str_replace(' ','-',$str);
        //Xóa các dấu gạch ngang dư thừa
        $str = preg_replace('/-+/','-',$str);
        //Cắt gạch ngang ở đầu và cuối chuỗi
        return trim($str, '-');
    }
}
?>