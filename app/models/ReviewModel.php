<?php
require_once __DIR__ . '/BaseModel.php';

class ReviewModel extends BaseModel {


    // Thêm hàm này vào ReviewModel.php
    public function getRootProductId($productId) {
        $productId = (int)$productId;
        // Truy vấn bảng products để xem có parent_id không
        $sql = "SELECT parent_id FROM products WHERE id = $productId";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        
        // Nếu parent_id không trống và khác 0, trả về parent_id, ngược lại trả về chính nó
        return (!empty($row['parent_id'])) ? $row['parent_id'] : $productId;
    }
    
    // Lấy danh sách đánh giá của sản phẩm (kèm tên user và phản hồi của admin)
    public function getReviewsByProduct($productId) {
        $parentId = $this->getRootProductId((int)$productId); // Luôn đưa về ID cha
    
        $sql = "SELECT r.*, u.fname, u.lname 
                FROM product_reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = $parentId
                ORDER BY r.created_at DESC";
                
        $result = $this->_query($sql);
        $reviews = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

        foreach ($reviews as &$rev) {
            $rev['replies'] = $this->getReplies($rev['id']);
        }
        return $reviews;
    }

    // Lấy phản hồi của Admin
    public function getReplies($reviewId) {
        $sql = "SELECT rp.*, u.fname, u.lname 
                FROM review_replies rp
                JOIN users u ON rp.user_id = u.id
                WHERE rp.review_id = $reviewId";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Tính toán thống kê sao (Dùng cho phần progress bar)
    public function getReviewStats($productId) {
        $parentId = $this->getRootProductId((int)$productId); // Luôn đưa về ID cha
    
        $stats = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 'total'=>0, 'avg'=>0];
        $sql = "SELECT rating, COUNT(*) as count FROM product_reviews WHERE product_id = $parentId GROUP BY rating";
        $result = $this->_query($sql);
        
        $sum = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $stats[$row['rating']] = (int)$row['count'];
            $stats['total'] += $row['count'];
            $sum += ($row['rating'] * $row['count']);
        }
        
        if ($stats['total'] > 0) {
            $stats['avg'] = round($sum / $stats['total'], 1);
        }
        return $stats;
    }

    public function insertReview($userId, $productId, $rating, $comment) {
        $productId = (int)$productId;
        $userId = $this->escape($userId);
        $rating = (int)$rating;
        $comment = $this->escape($comment);
        
        $sql = "INSERT INTO product_reviews (product_id, user_id, rating, comment, created_at) 
                VALUES ($productId, '$userId', $rating, '$comment', NOW())"; 
        return $this->_query($sql);
    }
    // Kiểm tra xem user đã đánh giá sản phẩm này chưa, nếu rồi thì trả về thông tin đánh giá đó
    public function getUserReview($userId, $productId) {
        $sql = "SELECT * FROM product_reviews WHERE user_id = '$userId' AND product_id = '$productId' LIMIT 1";
        $result = $this->_query($sql);
        return mysqli_fetch_assoc($result);
    }

    // Hàm cập nhật đánh giá cũ
    public function updateReview($reviewId, $rating, $comment) {
        $rating = (int)$rating;
        $comment = $this->escape($comment);
        $sql = "UPDATE product_reviews SET rating = '$rating', comment = '$comment', created_at = NOW() WHERE id = '$reviewId'";
        return $this->_query($sql);
    }

    public function deleteReview($id) {
        $id = (int)$id;
        return $this->_query("DELETE FROM product_reviews WHERE id = $id");
    }
    // Thêm hàm này vào file ReviewModel.php
    public function addReply($reviewId, $userId, $content) {
        $reviewId = (int)$reviewId;
        $userId = $this->escape($userId);
        $content = $this->escape($content);
        
        $sql = "INSERT INTO review_replies (review_id, user_id, reply_content, created_at) 
                VALUES ($reviewId, '$userId', '$content', NOW())";
        return $this->_query($sql);
    }
}