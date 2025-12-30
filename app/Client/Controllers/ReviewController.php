<?php
require_once __DIR__ . '/../../models/ReviewModel.php';

class ReviewController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new ReviewModel();
    }

    public function submit() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['id'];
        $productId = $_POST['product_id'];
        $rating = $_POST['rating'];
        $comment = trim($_POST['comment']);

        // --- BẮT ĐẦU PHẦN SỬA ---
        // Gọi hàm getRootProductId từ Model để lấy ID cha (Parent ID)
        // Nếu là sản phẩm cha, nó trả về chính nó. Nếu là con, nó trả về ID cha.
        $rootProductId = $this->reviewModel->getRootProductId($productId);
        // --- KẾT THÚC PHẦN SỬA ---

        // 1. Kiểm tra đánh giá dựa trên $rootProductId (ID cha)
        $existingReview = $this->reviewModel->getUserReview($userId, $rootProductId);

        if ($existingReview) {
            // 2. Nếu có rồi -> CẬP NHẬT
            $this->reviewModel->updateReview($existingReview['id'], $rating, $comment);
            $_SESSION['success'] = "Đã cập nhật đánh giá của bạn!";
        } else {
            // 3. Nếu chưa có -> THÊM MỚI (Lưu vào database với ID cha)
            $this->reviewModel->insertReview($userId, $rootProductId, $rating, $comment);
            $_SESSION['success'] = "Cảm ơn bạn đã đánh giá sản phẩm!";
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        // Kiểm tra quyền (phải là chủ sở hữu hoặc admin)
        // ... (logic kiểm tra giống UserController bạn đã gửi)
        $this->reviewModel->deleteReview($id);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    
}