<?php
require_once __DIR__ . '/../../models/OrderModel.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    // 1. DANH SÁCH ĐƠN HÀNG
    public function history() {
        // Bắt buộc đăng nhập
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        
        // Gọi Model lấy danh sách đơn của User này
        $orders = $this->orderModel->getOrdersByUserId($userId);

        // Load View cùng Header/Footer
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/history.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. CHI TIẾT ĐƠN HÀNG
    public function detail() {
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId  = $_SESSION['user']['id'];

        // Kiểm tra quyền: Chỉ xem được đơn của chính mình
        if (!$this->orderModel->isOrderOwner($orderId, $userId)) {
            die("Bạn không có quyền xem đơn hàng này!");
        }

        // Lấy chi tiết
        $data = $this->orderModel->getOrderDetail($orderId);
        $order = $data['info'];
        $items = $data['items'];
        
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/order_detail.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 3. KHÁCH HÀNG TỰ HỦY ĐƠN (Chỉ khi đơn mới tạo)
    public function cancel() {
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId  = $_SESSION['user']['id'];

        // Kiểm tra quyền sở hữu
        if (!$this->orderModel->isOrderOwner($orderId, $userId)) {
            $_SESSION['error'] = "Bạn không có quyền thao tác đơn hàng này!";
            // [FIX] Link lịch sử đơn
            header("Location: lich-su-don");
            exit;
        }

        // Kiểm tra trạng thái: Chỉ hủy được khi status = 1 (Chờ xác nhận)
        $data = $this->orderModel->getOrderDetail($orderId);
        if ($data['info']['status'] == 1) {
            // Cập nhật trạng thái thành 5 (Đã hủy)
            $this->orderModel->updateStatus($orderId, 5);
            $_SESSION['success'] = "Đã hủy đơn hàng thành công!";
        } else {
            $_SESSION['error'] = "Đơn hàng đã được xử lý, không thể hủy!";
        }

        // [FIX] Link chi tiết đơn hàng (Slug)
        header("Location: chi-tiet-don/$orderId");
        exit;
    }
}
?>