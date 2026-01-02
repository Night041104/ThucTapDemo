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
            header("Location: index.php?controller=auth&action=login");
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
            header("Location: index.php?controller=auth&action=login");
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
        // Load View cùng Header/Footer
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/order_detail.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
        
    }

    // 3. KHÁCH HÀNG TỰ HỦY ĐƠN (Chỉ khi đơn mới tạo)
    public function cancel() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId  = $_SESSION['user']['id'];

        // Kiểm tra quyền sở hữu
        if (!$this->orderModel->isOrderOwner($orderId, $userId)) {
            $_SESSION['error'] = "Bạn không có quyền thao tác đơn hàng này!";
            header("Location: index.php?controller=order&action=history");
            exit;
        }

        // Kiểm tra trạng thái: Chỉ hủy được khi status = 1 (Chờ xác nhận)
        // (Bạn có thể cho phép hủy cả status 2 tùy chính sách)
        $data = $this->orderModel->getOrderDetail($orderId);
        if ($data['info']['status'] == 1) {
            // Cập nhật trạng thái thành 5 (Đã hủy)
            $this->orderModel->updateStatus($orderId, 5);
            $_SESSION['success'] = "Đã hủy đơn hàng thành công!";
        } else {
            $_SESSION['error'] = "Đơn hàng đã được xử lý, không thể hủy!";
        }

        header("Location: index.php?controller=order&action=detail&id=$orderId");
        exit;
    }
}
?>