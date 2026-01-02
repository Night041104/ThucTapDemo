<?php
// Load Model
require_once __DIR__ . '/../../models/OrderModel.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
        header("Location: index.php?module=client&controller=auth&action=login");
        exit;
    }
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
    // Trong OrderController.php
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $status  = $_POST['status'];

            // Gọi Model cập nhật (Giả sử bạn đã có hàm này)
            $result = $this->orderModel->updateStatus($orderId, $status);

            // [QUAN TRỌNG] Kiểm tra xem request có phải là AJAX không
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống!']);
                }
                exit; // Dừng code tại đây để không load lại view
            }

            // Fallback cho trình duyệt cũ (Load lại trang)
            header("Location: index.php?module=admin&controller=order&action=detail&id=$orderId");
        }
    }
    
}
?>