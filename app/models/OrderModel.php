<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel {
    private $ghnToken  = '9e958cee-c146-11f0-a621-f2a9392e54c8';
    private $ghnShopId = '198125';   
    private $ghnUrl    = 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/switch-status/cancel';


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
            // A. Sinh mã đơn hàng ngẫu nhiên (VD: FBT2512...)
            $orderCode = "FBT" . date("ymd") . "-" . strtoupper(substr(md5(uniqid()), 0, 4));

            // B. Chuẩn bị dữ liệu khách hàng
            $uid = $userId ? "'".$this->escape($userId)."'" : 'NULL';
            
            $name    = $this->escape($customerData['fullname']);
            $email   = $this->escape($customerData['email']); 
            $phone   = $this->escape($customerData['phone']);
            $note    = $this->escape($customerData['note']);
            
            // --- [SỬA] LẤY 4 TRƯỜNG ĐỊA CHỈ TÁCH BIỆT ---
            $street  = $this->escape($customerData['street_address']);
            $city    = $this->escape($customerData['city']);
            $dist    = $this->escape($customerData['district']);
            $ward    = $this->escape($customerData['ward']);
            
            // Vẫn lưu chuỗi địa chỉ đầy đủ vào cột 'address' cũ (để hiển thị nhanh)
            $addr    = $this->escape($customerData['address']); 

            // ID địa lý cho GHN (Đã có trong DB)
            $distId   = isset($customerData['district_id']) ? (int)$customerData['district_id'] : 'NULL';
            $wardCode = isset($customerData['ward_code']) ? "'".$this->escape($customerData['ward_code'])."'" : 'NULL';

            // Payment & Coupon
            $payment = isset($customerData['payment_method']) ? $this->escape($customerData['payment_method']) : 'COD';
            $cpCode  = $couponCode ? "'".$this->escape($couponCode)."'" : 'NULL';
            $dcMoney = (int)$discountMoney;

            // C. Tính tổng tiền & Kiểm tra kho
            $totalMoney = 0;
            $finalItems = [];
            
            $ids = implode(',', array_keys($cartItems));
            if(empty($ids)) throw new Exception("Giỏ hàng trống!");

            $sqlProd = "SELECT id, name, price, quantity FROM products WHERE id IN ($ids)";
            $resProd = $this->_query($sqlProd);
            
            while ($prod = mysqli_fetch_assoc($resProd)) {
                $buyQty = $cartItems[$prod['id']];
                if ($prod['quantity'] < $buyQty) {
                    throw new Exception("Sản phẩm '{$prod['name']}' vừa hết hàng (Chỉ còn: {$prod['quantity']}).");
                }
                $prod['buy_qty'] = $buyQty;
                $finalItems[] = $prod;
                $totalMoney += ($prod['price'] * $buyQty);
            }

            $finalTotal = $totalMoney - $dcMoney;
            if ($finalTotal < 0) $finalTotal = 0;

            // D. INSERT vào bảng ORDERS (Đã thêm 4 cột mới)
            $sqlOrder = "INSERT INTO orders 
            (order_code, user_id, fullname, email, phone, street_address, city, district, ward, address, district_id, ward_code, note, total_money, payment_method, coupon_code, discount_money, status) 
            VALUES 
            ('$orderCode', $uid, '$name', '$email', '$phone', '$street', '$city', '$dist', '$ward', '$addr', $distId, $wardCode, '$note', '$finalTotal', '$payment', $cpCode, '$dcMoney', 1)";
            
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

                $this->conn->query("INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                                    VALUES ('$orderId', '$pid', '$pname', '$price', '$qty')");

                $this->conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = '$pid' AND quantity >= $qty");
                
                if ($this->conn->affected_rows == 0) {
                    throw new Exception("Xung đột tồn kho! Sản phẩm '$pname' không đủ số lượng.");
                }
            }

            // F. Trừ Coupon
            if ($couponCode) {
                $cpCodeClean = $this->escape($couponCode);
                $this->conn->query("UPDATE coupons SET quantity = quantity - 1 WHERE code = '$cpCodeClean'");
            }

            $this->conn->commit();
            return $orderCode;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Lấy danh sách đơn hàng theo User ID (Cho trang Lịch sử mua hàng)
    public function getOrdersByUserId($userId) {
        $userId = $this->escape($userId);
        $sql = "SELECT * FROM orders WHERE user_id = '$userId' ORDER BY created_at DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Lấy chi tiết đơn hàng bằng Mã đơn (Dùng cho VNPAY gửi mail & Trang Success)
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

    // Cập nhật trạng thái dựa trên Mã đơn hàng (Dùng cho VNPAY update sau khi thanh toán)
    public function updateStatusByCode($orderCode, $status) {
        $code = $this->escape($orderCode);
        $status = (int)$status;
        
        $sql = "UPDATE orders SET status = '$status' WHERE order_code = '$code'";
        return $this->conn->query($sql);
    }

    // Kiểm tra quyền sở hữu đơn hàng (Bảo mật cho trang chi tiết)
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
   // Lấy tất cả đơn hàng (Có hỗ trợ tìm kiếm theo keyword)
   // Hàm lấy danh sách đơn hàng (Có Lọc theo Keyword, Trạng thái, Phương thức thanh toán)
    // File: models/OrderModel.php

    // [THAY THẾ] Hàm getAllOrders hỗ trợ Lọc & Phân trang
    public function getAllOrders($keyword = '', $status = '', $paymentMethod = '', $page = 1, $limit = 10) {
        $where = "1=1";

        // 1. Lọc theo Keyword
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND (order_code LIKE '%$kw%' OR fullname LIKE '%$kw%' OR phone LIKE '%$kw%')";
        }

        // 2. Lọc theo Trạng thái
        if ($status !== '') {
            $st = (int)$status;
            $where .= " AND status = '$st'";
        }

        // 3. Lọc theo PTTT
        if ($paymentMethod) {
            $pay = $this->escape($paymentMethod);
            $where .= " AND payment_method = '$pay'";
        }

        // Tính Offset
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM orders WHERE $where ORDER BY created_at DESC LIMIT $offset, $limit";
        
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // [THÊM MỚI] Hàm đếm tổng số đơn hàng (để tính số trang)
    public function countAll($keyword = '', $status = '', $paymentMethod = '') {
        $where = "1=1";
        // Logic lọc y hệt getAllOrders
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND (order_code LIKE '%$kw%' OR fullname LIKE '%$kw%' OR phone LIKE '%$kw%')";
        }
        if ($status !== '') {
            $st = (int)$status;
            $where .= " AND status = '$st'";
        }
        if ($paymentMethod) {
            $pay = $this->escape($paymentMethod);
            $where .= " AND payment_method = '$pay'";
        }

        $sql = "SELECT COUNT(*) as total FROM orders WHERE $where";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
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

        // B1: Lấy thông tin đơn hàng cũ
        $orderInfo = $this->_query("SELECT status, order_code, tracking_code FROM orders WHERE id = '$orderId'")->fetch_assoc();
        if (!$orderInfo) return false;

        $oldStatus = (int)$orderInfo['status'];

        if ($oldStatus == $newStatus) return true;

        // B2: Xử lý Logic Hủy / Khôi phục
        
        // --- TRƯỜNG HỢP 1: ĐANG BÌNH THƯỜNG -> CHUYỂN SANG HỦY (5) ---
        if ($oldStatus != 5 && $newStatus == 5) {
            // 1. Cộng lại số lượng vào kho
            $this->adjustStock($orderId, '+'); 
            
            // 2. TỰ ĐỘNG GỌI API HỦY ĐƠN BÊN GHN SANDBOX
            $ghnCode = !empty($orderInfo['tracking_code']) ? $orderInfo['tracking_code'] : $orderInfo['order_code'];
            
            if ($ghnCode) {
                $this->cancelGhnOrder($ghnCode); // Gọi hàm hủy GHN
            }
        }
        
        // --- TRƯỜNG HỢP 2: ĐANG HỦY (5) -> KHÔI PHỤC LẠI (VỀ 1, 2, 3...) ---
        elseif ($oldStatus == 5 && $newStatus != 5) {
            
            // 1. Trừ lại số lượng trong kho
            if (!$this->adjustStock($orderId, '-')) {
                return "Không thể khôi phục đơn hàng! Kho không đủ sản phẩm.";
            }

            // 2. [QUAN TRỌNG] XÓA MÃ VẬN ĐƠN CŨ (RESET VỀ NULL)
            // Logic: Mã cũ bên GHN đã bị hủy rồi, không dùng được nữa.
            // Ta xóa đi để hệ thống hiểu đây là đơn chưa có vận đơn.
            // Sau đó Admin sẽ thao tác "Tạo đơn hàng" lại trên giao diện quản trị.
            $this->conn->query("UPDATE orders SET tracking_code = NULL WHERE id = '$orderId'");
        }

        // B3: Cập nhật trạng thái mới vào Database
        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    // Helper điều chỉnh kho (Private - Chỉ dùng nội bộ class này)
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
    public function updateTrackingCode($orderId, $code) {
    $orderId = (int)$orderId;
    $code = $this->escape($code);
    $sql = "UPDATE orders SET tracking_code = '$code' WHERE id = '$orderId'";
    return $this->conn->query($sql);
}
private function cancelGhnOrder($ghnOrderCode) {
        $data = ["order_codes" => [$ghnOrderCode]];

        $ch = curl_init($this->ghnUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Token: ' . $this->ghnToken,
            'ShopId: ' . $this->ghnShopId
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
?>