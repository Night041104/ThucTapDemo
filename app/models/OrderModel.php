<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel {

    // =================================================================
    // 1. DÀNH CHO KHÁCH HÀNG (CLIENT)
    // =================================================================

    /**
     * TẠO ĐƠN HÀNG MỚI (Full tính năng: Coupon, Payment, Stock, Transaction)
     */
    public function createOrder($userId, $customerData, $cartItems, $couponCode = null, $discountMoney = 0) {
        // Bắt đầu giao dịch bảo vệ dữ liệu (Transaction)
        $this->conn->begin_transaction();

        try {
            // A. Sinh mã đơn hàng (VD: FBT2512...)
            $orderCode = "FBT" . date("ymd") . "-" . strtoupper(substr(md5(uniqid()), 0, 4));

            // B. Chuẩn bị dữ liệu khách hàng
            // Xử lý User ID: Nếu có ID thì bao quanh bởi dấu nháy đơn, nếu không thì là NULL
            $uid = $userId ? "'".$this->escape($userId)."'" : 'NULL';
            
            $name    = $this->escape($customerData['fullname']);
            $email   = $this->escape($customerData['email']); 
            $phone   = $this->escape($customerData['phone']);
            $addr    = $this->escape($customerData['address']);
            $note    = $this->escape($customerData['note']);
            $payment = isset($customerData['payment_method']) ? $this->escape($customerData['payment_method']) : 'COD';

            // Xử lý Coupon
            $cpCode  = $couponCode ? "'".$this->escape($couponCode)."'" : 'NULL';
            $dcMoney = (int)$discountMoney;

            // C. Tính tổng tiền hàng & Kiểm tra tồn kho thực tế
            $totalMoney = 0;
            $finalItems = [];
            
            // Lấy danh sách ID sản phẩm từ giỏ
            $ids = implode(',', array_keys($cartItems));
            if(empty($ids)) throw new Exception("Giỏ hàng trống!");

            // Query lấy giá và tồn kho thực tế từ DB
            $sqlProd = "SELECT id, name, price, quantity FROM products WHERE id IN ($ids)";
            $resProd = $this->_query($sqlProd);
            
            while ($prod = mysqli_fetch_assoc($resProd)) {
                $buyQty = $cartItems[$prod['id']];
                
                // Kiểm tra tồn kho
                if ($prod['quantity'] < $buyQty) {
                    throw new Exception("Sản phẩm '{$prod['name']}' vừa hết hàng (Còn: {$prod['quantity']}).");
                }

                $prod['buy_qty'] = $buyQty;
                $finalItems[] = $prod;
                $totalMoney += ($prod['price'] * $buyQty);
            }

            // Tính tổng thanh toán cuối cùng (Sau khi trừ mã giảm giá)
            $finalTotal = $totalMoney - $dcMoney;
            if ($finalTotal < 0) $finalTotal = 0;

            // D. INSERT vào bảng ORDERS
            // Lưu ý: Lưu finalTotal vào total_money (số tiền khách phải trả)
            $sqlOrder = "INSERT INTO orders (order_code, user_id, fullname, email, phone, address, note, total_money, payment_method, coupon_code, discount_money, status) 
                         VALUES ('$orderCode', $uid, '$name', '$email', '$phone', '$addr', '$note', '$finalTotal', '$payment', $cpCode, '$dcMoney', 1)";
            
            if (!$this->conn->query($sqlOrder)) {
                throw new Exception("Lỗi tạo đơn: " . $this->conn->error);
            }
            $orderId = $this->conn->insert_id;

            // E. Insert Chi tiết đơn hàng & Trừ kho
            foreach ($finalItems as $item) {
                $pid = $item['id'];
                $pname = $this->escape($item['name']);
                $price = $item['price'];
                $qty = $item['buy_qty'];

                // 1. Lưu chi tiết
                $this->conn->query("INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                                    VALUES ('$orderId', '$pid', '$pname', '$price', '$qty')");

                // 2. Trừ tồn kho (Quan trọng: AND quantity >= qty để chặn âm kho)
                $this->conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = '$pid' AND quantity >= $qty");
                
                if ($this->conn->affected_rows == 0) {
                    throw new Exception("Xung đột tồn kho! Sản phẩm '$pname' không đủ số lượng.");
                }
            }

            // F. Trừ số lượng Mã giảm giá (Nếu có dùng)
            if ($couponCode) {
                $cpCodeClean = $this->escape($couponCode);
                $this->conn->query("UPDATE coupons SET quantity = quantity - 1 WHERE code = '$cpCodeClean'");
            }

            // Mọi thứ Ok -> Commit (Lưu thật)
            $this->conn->commit();
            return $orderCode;

        } catch (Exception $e) {
            // Có lỗi -> Rollback (Hoàn tác mọi thay đổi)
            $this->conn->rollback();
            return false;
        }
    }

    // Lấy danh sách đơn hàng theo User ID (Lịch sử mua hàng)
    public function getOrdersByUserId($userId) {
        $userId = $this->escape($userId);
        $sql = "SELECT * FROM orders WHERE user_id = '$userId' ORDER BY created_at DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Lấy chi tiết đơn hàng bằng Mã đơn (Dùng cho VNPAY gửi mail)
    public function getOrderByCode($code) {
        $code = $this->escape($code);
        
        // 1. Lấy thông tin chung
        $sqlInfo = "SELECT * FROM orders WHERE order_code = '$code'";
        $info = mysqli_fetch_assoc($this->_query($sqlInfo));
        if (!$info) return null;

        // 2. Lấy danh sách sản phẩm
        $sqlItems = "SELECT * FROM order_details WHERE order_id = '{$info['id']}'";
        $itemsResult = $this->_query($sqlItems);
        $items = $itemsResult ? mysqli_fetch_all($itemsResult, MYSQLI_ASSOC) : [];

        return ['info' => $info, 'items' => $items];
    }

    // Cập nhật trạng thái dựa trên Mã đơn hàng (Dùng cho VNPAY update status)
    public function updateStatusByCode($orderCode, $status) {
        $code = $this->escape($orderCode);
        $status = (int)$status;
        
        $sql = "UPDATE orders SET status = '$status' WHERE order_code = '$code'";
        return $this->conn->query($sql);
    }

    // Kiểm tra quyền sở hữu đơn hàng
    public function isOrderOwner($orderId, $userId) {
        $orderId = (int)$orderId;
        $userId = $this->escape($userId);
        
        $sql = "SELECT id FROM orders WHERE id = '$orderId' AND user_id = '$userId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }

    // =================================================================
    // 2. DÀNH CHO ADMIN (QUẢN LÝ)
    // =================================================================

    // Lấy tất cả đơn hàng
    public function getAllOrders() {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Lấy chi tiết đơn hàng (Dùng chung cho cả Admin và Client)
    public function getOrderDetail($orderId) {
        $orderId = (int)$orderId;
        
        // Thông tin chung
        $info = mysqli_fetch_assoc($this->_query("SELECT * FROM orders WHERE id = '$orderId'"));
        if (!$info) return null;

        // Danh sách sản phẩm
        $itemsResult = $this->_query("SELECT * FROM order_details WHERE order_id = '$orderId'");
        $items = $itemsResult ? mysqli_fetch_all($itemsResult, MYSQLI_ASSOC) : [];

        return ['info' => $info, 'items' => $items];
    }

    // Cập nhật trạng thái đơn hàng (Xử lý kho 2 chiều thông minh)
    public function updateStatus($orderId, $newStatus) {
        $orderId = (int)$orderId;
        $newStatus = (int)$newStatus;

        // B1: Lấy trạng thái CŨ
        $orderInfo = $this->_query("SELECT status FROM orders WHERE id = '$orderId'")->fetch_assoc();
        $oldStatus = (int)$orderInfo['status'];

        if ($oldStatus == $newStatus) return true;

        // B2: Xử lý kho
        // Chiều 1: Đang bình thường -> HỦY (5) ==> HOÀN KHO (+)
        if ($oldStatus != 5 && $newStatus == 5) {
            $this->adjustStock($orderId, '+'); 
        }
        // Chiều 2: Đang HỦY (5) -> Quay lại bình thường ==> TRỪ KHO (-)
        elseif ($oldStatus == 5 && $newStatus != 5) {
            if (!$this->adjustStock($orderId, '-')) {
                return "Không thể khôi phục đơn hàng! Kho không đủ sản phẩm.";
            }
        }

        // B3: Cập nhật
        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    // Helper điều chỉnh kho (Private)
    private function adjustStock($orderId, $operator) {
        $items = $this->_query("SELECT product_id, quantity FROM order_details WHERE order_id = '$orderId'");
        while ($row = mysqli_fetch_assoc($items)) {
            $pid = $row['product_id'];
            $qty = (int)$row['quantity'];
            
            if ($operator == '+') {
                $this->conn->query("UPDATE products SET quantity = quantity + $qty WHERE id = '$pid'");
            } else {
                // Trừ kho và kiểm tra xem có bị âm không
                $this->conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = '$pid' AND quantity >= $qty");
                if ($this->conn->affected_rows == 0) return false; // Thất bại do thiếu hàng
            }
        }
        return true;
    }
}
?>