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

            $rootProductId = $this->reviewModel->getRootProductId($productId);
            $existingReview = $this->reviewModel->getUserReview($userId, $rootProductId);

            if ($existingReview) {
                // Thực hiện SỬA
                $this->reviewModel->updateReview($existingReview['id'], $rating, $comment);
                $_SESSION['success'] = "Cập nhật đánh giá thành công!";
            } else {
                // Thực hiện THÊM MỚI
                $this->reviewModel->insertReview($userId, $rootProductId, $rating, $comment);
                $_SESSION['success'] = "Cảm ơn bạn đã đánh giá sản phẩm!";
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
            $reviewId = $_POST['review_id']; // Cần gửi thêm ID này từ form
            $userId = $_SESSION['user']['id'];
            $rating = $_POST['rating'];
            $comment = trim($_POST['comment']);

            // Kiểm tra quyền sở hữu trước khi cho sửa
            $review = $this->reviewModel->getReviewById($reviewId);
            if ($review && $review['user_id'] == $userId) {
                $this->reviewModel->updateReview($reviewId, $rating, $comment);
                $_SESSION['success'] = "Cập nhật đánh giá thành công!";
            } else {
                $_SESSION['error'] = "Bạn không có quyền sửa đánh giá này!";
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Bước 1: Phải đăng nhập mới được xóa
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // Bước 2: Lấy thông tin bài review để kiểm tra chủ sở hữu
        $review = $this->reviewModel->getReviewById($id); 

        // Bước 3: CHỈ xóa nếu đúng là người đó viết bài đó
        // (Đây là logic bảo mật cho Client, khác với file Admin)
        if ($review && $review['user_id'] == $_SESSION['user']['id']) {
            $this->reviewModel->deleteReview($id);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}