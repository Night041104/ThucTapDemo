<?php
// Load Model & Helper
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../Helpers/GhnHelper.php';

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
            // [FIX URL] Redirect về trang đăng nhập client chuẩn xác
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

        // Gọi Model
        $orders = $this->orderModel->getAllOrders($keyword, $status, $payment);

        // Truyền biến ra View
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/order/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. Xem chi tiết đơn
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = $this->orderModel->getOrderDetail($id);

        if (!$data) {
            // Có thể redirect về trang danh sách hoặc báo lỗi
            echo "<script>alert('Đơn hàng không tồn tại!'); window.location.href='" . $this->baseUrl . "admin/order';</script>";
            exit;
        }

        $order = $data['info'];
        $items = $data['items'];

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/order/detail.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 3. Xử lý cập nhật trạng thái (CÓ TÍCH HỢP GHN)
    public function update_status() {
        // Luôn trả về JSON vì client dùng AJAX
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? 0;
            $status  = $_POST['status'] ?? 0;

            if (!$orderId || !$status) {
                echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
                exit;
            }

            // --- [BẮT ĐẦU LOGIC GHN] ---
            // Nếu chuyển sang trạng thái "Đang giao hàng" (ID = 3)
            if ($status == 3) {
                // 1. Lấy thông tin chi tiết đơn hàng
                $data = $this->orderModel->getOrderDetail($orderId);
                $orderInfo = $data['info'];
                $orderItems = $data['items'];

                // 2. Kiểm tra nếu chưa có mã vận đơn thì mới tạo
                if (empty($orderInfo['tracking_code'])) {
                    $ghn = new GhnHelper();
                    
                    // Gọi API tạo đơn vận chuyển
                    $result = $ghn->createShippingOrder($orderInfo, $orderItems);

                    if (isset($result['code']) && $result['code'] == 200) {
                        // A. Thành công: Lấy mã vận đơn từ GHN
                        $trackingCode = $result['data']['order_code'];
                        
                        // B. Lưu mã vận đơn vào Database
                        $this->orderModel->updateTrackingCode($orderId, $trackingCode);
                        
                    } else {
                        // C. Thất bại: Trả về lỗi ngay lập tức
                        $msg = $result['message'] ?? $result['code_message_value'] ?? 'Lỗi không xác định từ GHN';
                        
                        // Dịch một số lỗi phổ biến cho dễ hiểu
                        if (strpos($msg, 'DistrictID') !== false) $msg = "Lỗi địa chỉ: Quận/Huyện không hợp lệ (Vui lòng kiểm tra lại địa chỉ khách hàng).";
                        if (strpos($msg, 'WardCode') !== false) $msg = "Lỗi địa chỉ: Phường/Xã không hợp lệ.";
                        
                        echo json_encode(['status' => 'error', 'message' => "Không thể tạo vận đơn GHN: " . $msg]);
                        exit; // Dừng code, KHÔNG cập nhật trạng thái đơn hàng
                    }
                }
            }
            // --- [KẾT THÚC LOGIC GHN] ---

            // Gọi Model cập nhật trạng thái đơn hàng
            $result = $this->orderModel->updateStatus($orderId, $status);

            if ($result === true) { 
                echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công!']);
            } else {
                // Trường hợp lỗi logic kho hàng (hàm updateStatus trả về chuỗi lỗi)
                $errorMsg = is_string($result) ? $result : 'Lỗi hệ thống khi cập nhật Database!';
                echo json_encode(['status' => 'error', 'message' => $errorMsg]);
            }
            exit;
        }
    }
}
?>