<?php
require_once __DIR__ . '/../../models/ReviewModel.php';

class ReviewController {
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // 2. Kiểm tra quyền Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
    }

    // Hàm liệt kê tất cả review
    public function index() {
        // (Logic của bạn chưa có code phần này, tôi giữ nguyên)
    }

    public function reply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra lại quyền Admin cho chắc chắn
            if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
                die("Bạn không có quyền thực hiện chức năng này.");
            }

            $reviewId = $_POST['review_id'];
            $replyText = trim($_POST['reply_text']);
            $adminId = $_SESSION['user']['id'];

            if (!empty($replyText)) {
                $reviewModel = new ReviewModel();
                $reviewModel->addReply($reviewId, $adminId, $replyText);
            }
            
            // Quay lại trang trước đó (Giữ nguyên logic này vì nó tiện lợi)
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    public function delete() {
        // 1. Lấy ID từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            // 2. Khởi tạo Model (Đảm bảo đã require ReviewModel ở đầu file)
            $reviewModel = new ReviewModel();
            
            // 3. Thực hiện xóa
            $result = $reviewModel->deleteReview($id);
            
            if($result) {
                $_SESSION['success'] = "Xóa đánh giá thành công!";
            } else {
                $_SESSION['error'] = "Không thể xóa bài này!";
            }
        }

        // 4. Quay lại trang trước đó
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            // Nếu không có Referer thì về trang danh sách (tùy ní đặt tên)
            header("Location: index.php?module=admin&controller=review");
        }
        exit();
    }
}
?>