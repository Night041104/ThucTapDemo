<?php
// Load Model
require_once __DIR__ . '/../../models/OrderModel.php';

class OrderController {
    private $orderModel;
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // 2. Kiểm tra quyền Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
        $this->orderModel = new OrderModel();
    }

    // 1. Danh sách đơn hàng
    public function index() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $status  = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : ''; 
        $payment = isset($_GET['payment']) ? $_GET['payment'] : '';

        $orders = $this->orderModel->getAllOrders($keyword, $status, $payment);

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
            $orderId = $_POST['order_id'];
            $status  = $_POST['status'];

            $result = $this->orderModel->updateStatus($orderId, $status);

            // AJAX Response
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống!']);
                }
                exit; 
            }

            // [FIX URL] Fallback về trang chi tiết: admin/order/detail?id=...
            header("Location: " . $this->baseUrl . "admin/order/detail?id=$orderId");
            exit;
        }
    }
}
?>