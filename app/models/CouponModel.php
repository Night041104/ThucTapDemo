<?php
require_once __DIR__ . '/BaseModel.php';

class CouponModel extends BaseModel {
    // --- [PHẦN ADMIN] ---

    // 1. Lấy tất cả coupon (có phân trang hoặc lấy hết)
    // Trong models/CouponModel.php

// 1. [NÂNG CẤP] Lấy danh sách Coupon có lọc
// File: models/CouponModel.php

    // 1. [THAY THẾ] Lấy danh sách Coupon có lọc & Phân trang
    public function getAllCoupons($keyword = '', $status = '', $type = '', $page = 1, $limit = 10) {
        $where = "1=1";

        // Lọc theo từ khóa
        if (!empty($keyword)) {
            $kw = $this->escape($keyword);
            $where .= " AND (code LIKE '%$kw%' OR description LIKE '%$kw%')";
        }

        // Lọc theo Trạng thái
        if ($status !== '') {
            $st = (int)$status;
            $where .= " AND status = $st";
        }

        // Lọc theo Loại
        if (!empty($type)) {
            $ty = $this->escape($type);
            $where .= " AND type = '$ty'";
        }

        // Tính Offset
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM coupons WHERE $where ORDER BY id DESC LIMIT $offset, $limit";

        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // 2. [THÊM MỚI] Đếm tổng số bản ghi (cho Phân trang)
    public function countAll($keyword = '', $status = '', $type = '') {
        $where = "1=1";
        // Logic lọc y hệt getAllCoupons
        if (!empty($keyword)) {
            $kw = $this->escape($keyword);
            $where .= " AND (code LIKE '%$kw%' OR description LIKE '%$kw%')";
        }
        if ($status !== '') {
            $st = (int)$status;
            $where .= " AND status = $st";
        }
        if (!empty($type)) {
            $ty = $this->escape($type);
            $where .= " AND type = '$ty'";
        }

        $sql = "SELECT COUNT(*) as total FROM coupons WHERE $where";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // 2. Lấy 1 coupon theo ID
    public function getCouponById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM coupons WHERE id = $id LIMIT 1";
        $result = $this->_query($sql);
        return mysqli_fetch_assoc($result);
    }

    // 3. Thêm mới
    public function createCoupon($data) {
        // Chuẩn bị dữ liệu an toàn
        $code = $this->escape($data['code']);
        $type = $this->escape($data['type']);
        $value = (int)$data['value'];
        $max_discount = (int)$data['max_discount_amount'];
        $min_order = (int)$data['min_order_amount'];
        $quantity = (int)$data['quantity'];
        $usage_limit = (int)$data['usage_limit_per_user'];
        $start = $this->escape($data['start_date']);
        $end = $this->escape($data['end_date']);
        $status = (int)$data['status'];
        $desc = $this->escape($data['description']);

        $sql = "INSERT INTO coupons 
                (code, type, value, max_discount_amount, min_order_amount, quantity, usage_limit_per_user, start_date, end_date, status, description) 
                VALUES 
                ('$code', '$type', $value, $max_discount, $min_order, $quantity, $usage_limit, '$start', '$end', $status, '$desc')";
        
        return $this->conn->query($sql);
    }

    // 4. Cập nhật
    public function updateCoupon($id, $data) {
        $id = (int)$id;
        $code = $this->escape($data['code']);
        $type = $this->escape($data['type']);
        $value = (int)$data['value'];
        $max_discount = (int)$data['max_discount_amount'];
        $min_order = (int)$data['min_order_amount'];
        $quantity = (int)$data['quantity'];
        $usage_limit = (int)$data['usage_limit_per_user'];
        $start = $this->escape($data['start_date']);
        $end = $this->escape($data['end_date']);
        $status = (int)$data['status'];
        $desc = $this->escape($data['description']);

        $sql = "UPDATE coupons SET 
                code = '$code', 
                type = '$type', 
                value = $value, 
                max_discount_amount = $max_discount, 
                min_order_amount = $min_order, 
                quantity = $quantity,
                usage_limit_per_user = $usage_limit,
                start_date = '$start', 
                end_date = '$end', 
                status = $status,
                description = '$desc'
                WHERE id = $id";

        return $this->conn->query($sql);
    }

    // 5. Xóa
    public function deleteCoupon($id) {
        $id = (int)$id;
        // Xóa lịch sử dùng trước (để tránh lỗi khóa ngoại)
        $this->conn->query("DELETE FROM coupon_usages WHERE coupon_id = $id");
        // Xóa coupon
        return $this->conn->query("DELETE FROM coupons WHERE id = $id");
    }

    // 1. NÂNG CẤP: Hàm kiểm tra mã toàn diện
    public function checkCoupon($code, $totalOrderAmount, $userId = null) {
        $code = $this->escape($code);
        $today = date('Y-m-d H:i:s'); // Dùng giờ phút giây cho chính xác

        // Lấy thông tin mã
        $sql = "SELECT * FROM coupons WHERE code = '$code' AND status = 1 LIMIT 1";
        $result = $this->_query($sql);
        $coupon = mysqli_fetch_assoc($result);

        // --- KIỂM TRA CƠ BẢN ---
        if (!$coupon) {
            return ['valid' => false, 'msg' => 'Mã giảm giá không tồn tại!'];
        }

        if ($coupon['quantity'] <= 0) {
            return ['valid' => false, 'msg' => 'Mã này đã hết lượt sử dụng!'];
        }

        if ($coupon['start_date'] > $today || $coupon['end_date'] < $today) {
            return ['valid' => false, 'msg' => 'Mã này chưa bắt đầu hoặc đã hết hạn!'];
        }

        if ($totalOrderAmount < $coupon['min_order_amount']) {
            $minFmt = number_format($coupon['min_order_amount'], 0, ',', '.');
            return ['valid' => false, 'msg' => "Đơn hàng phải từ $minFmt ₫ mới được dùng mã này!"];
        }

        // --- NÂNG CẤP 1: KIỂM TRA GIỚI HẠN NGƯỜI DÙNG ---
        // (Nếu khách đã đăng nhập và mã có giới hạn per user)
        if ($userId && isset($coupon['usage_limit_per_user']) && $coupon['usage_limit_per_user'] > 0) {
            $sqlCheckUser = "SELECT COUNT(*) as count FROM coupon_usages WHERE coupon_id = {$coupon['id']} AND user_id = '$userId'";
            $resUser = $this->_query($sqlCheckUser);
            $rowUser = mysqli_fetch_assoc($resUser);
            
            if ($rowUser['count'] >= $coupon['usage_limit_per_user']) {
                return ['valid' => false, 'msg' => 'Bạn đã hết lượt sử dụng mã này!'];
            }
        }

        // --- NÂNG CẤP 2: TÍNH TOÁN SỐ TIỀN GIẢM (Xử lý % và Max giảm) ---
        $discountAmount = 0;

        if ($coupon['type'] == 'percent') {
            // Tính %
            $discountAmount = ($totalOrderAmount * $coupon['value']) / 100;
            
            // Kiểm tra trần giảm giá (nếu có set > 0)
            if ($coupon['max_discount_amount'] > 0 && $discountAmount > $coupon['max_discount_amount']) {
                $discountAmount = $coupon['max_discount_amount'];
            }
        } else {
            // Loại tiền cố định (fixed)
            $discountAmount = $coupon['value'];
        }

        // Đảm bảo không giảm quá giá trị đơn hàng
        if ($discountAmount > $totalOrderAmount) {
            $discountAmount = $totalOrderAmount;
        }

        // Trả về kết quả kèm số tiền đã tính toán
        return [
            'valid' => true, 
            'data' => $coupon,
            'discount_amount' => $discountAmount, // Trả về số tiền đã tính sẵn
            'msg' => 'Áp dụng mã giảm giá thành công!'
        ];
    }

    // 2. NÂNG CẤP: Hàm lưu lịch sử sử dụng (Gọi khi đặt hàng thành công)
    public function logCouponUsage($couponId, $userId, $orderId, $discountAmount) {
        // Trừ số lượng chung
        $sqlMinus = "UPDATE coupons SET quantity = quantity - 1 WHERE id = $couponId";
        $this->conn->query($sqlMinus);

        // Lưu log (nếu có userId)
        if ($userId) {
            $discountAmount = (float)$discountAmount;
            $sqlLog = "INSERT INTO coupon_usages (coupon_id, user_id, order_id, discount_amount, used_at) 
                       VALUES ($couponId, '$userId', $orderId, $discountAmount, NOW())";
            $this->conn->query($sqlLog);
        }
    }
    // Trong file CouponModel.php

// File: models/CouponModel.php

public function getAllActiveCoupons($userId = null) {
    $today = date('Y-m-d H:i:s');
    
    if ($userId) {
        // Nếu đã đăng nhập: Dùng subquery để đếm xem user này đã dùng mã này bao nhiêu lần (cột user_used_count)
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM coupon_usages u WHERE u.coupon_id = c.id AND u.user_id = '$userId') as user_used_count
                FROM coupons c 
                WHERE c.status = 1 
                AND c.start_date <= '$today' 
                AND c.end_date >= '$today' 
                AND c.quantity > 0 
                ORDER BY c.value DESC";
    } else {
        // Nếu chưa đăng nhập: Mặc định số lần dùng là 0
        $sql = "SELECT c.*, 0 as user_used_count 
                FROM coupons c 
                WHERE c.status = 1 
                AND c.start_date <= '$today' 
                AND c.end_date >= '$today' 
                AND c.quantity > 0 
                ORDER BY c.value DESC";
    }
    
    $result = $this->_query($sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
}
?>