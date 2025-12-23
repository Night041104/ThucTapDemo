<?php
// Load Model
require_once __DIR__ . '/../../models/OrderModel.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    // 1. Danh sách đơn hàng
    public function index() {
        // 1. Lấy các tham số lọc từ URL
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $status  = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : ''; // Chặn null
        $payment = isset($_GET['payment']) ? $_GET['payment'] : '';

        // 2. Gọi Model (Truyền đủ 3 tham số)
        $orders = $this->orderModel->getAllOrders($keyword, $status, $payment);

        // 3. Truyền biến ra View
        require_once __DIR__ . '/../Views/order/index.php';
    }
    // 2. Xem chi tiết đơn
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = $this->orderModel->getOrderDetail($id);

        if (!$data) {
            die("Đơn hàng không tồn tại!");
        }

        $order = $data['info'];
        $items = $data['items'];

        require __DIR__ . '/../Views/order/detail.php';
    }

    // 3. Xử lý cập nhật trạng thái
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['order_id'];
            $status = $_POST['status'];

            // Gọi hàm updateStatus
            $result = $this->orderModel->updateStatus($id, $status);

            if ($result === true || is_object($result)) {
                // Thành công
                $msg = 'updated';
            } else {
                // Thất bại (Trả về chuỗi lỗi)
                $msg = urlencode("❌ Lỗi: " . $result);
            }

            // Quay lại trang chi tiết
            header("Location: index.php?module=admin&controller=order&action=detail&id=$id&msg=$msg");
            exit;
        }
    }
}
?>