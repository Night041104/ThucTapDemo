<?php
require_once __DIR__ . '/BaseModel.php';

class CouponModel extends BaseModel {

    // Hàm kiểm tra mã giảm giá có hợp lệ không
    public function checkCoupon($code, $totalOrderAmount) {
        $code = $this->escape($code);
        $today = date('Y-m-d');

        // 1. Tìm mã trong database
        $sql = "SELECT * FROM coupons WHERE code = '$code' AND status = 1 LIMIT 1";
        $result = $this->_query($sql);
        $coupon = mysqli_fetch_assoc($result);

        // 2. Kiểm tra các điều kiện
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

        // 3. Nếu tất cả OK -> Trả về thông tin mã
        return [
            'valid' => true, 
            'data' => $coupon,
            'msg' => 'Áp dụng mã giảm giá thành công!'
        ];
    }

    // Hàm trừ số lượng mã sau khi đặt hàng thành công
    public function decreaseQuantity($code) {
        $code = $this->escape($code);
        $sql = "UPDATE coupons SET quantity = quantity - 1 WHERE code = '$code'";
        return $this->conn->query($sql);
    }
}
?>