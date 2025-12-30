<?php
// Tương tự UserController, check quyền role_id == 1 trong __construct
require_once __DIR__ . '/../../models/ReviewModel.php';
class ReviewController {
    // Hàm liệt kê tất cả review để admin duyệt/xoá
    public function index() {
        // Gọi model lấy toàn bộ review
    }

    public function reply() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Kiểm tra quyền Admin (Giả sử bạn lưu role_id trong session)
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
        
        // Quay lại trang trước đó hoặc trang quản lý review
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
        }
    }
}