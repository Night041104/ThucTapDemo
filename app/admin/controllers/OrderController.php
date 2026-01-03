<?php
// Load Model & Helper
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../Helpers/GhnHelper.php'; // [QUAN TRỌNG] Load Helper GHN

class OrderController {
    private $orderModel;

    public function __construct() {
        // Kiểm tra quyền Admin
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
        $status  = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : '';
        $payment = isset($_GET['payment']) ? $_GET['payment'] : '';

        // 2. Gọi Model
        $orders = $this->orderModel->getAllOrders($keyword, $status, $payment);

        // 3. Truyền biến ra View
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/order/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. Xem chi tiết đơn
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = $this->orderModel->getOrderDetail($id);

        if (!$data) {
            echo "Đơn hàng không tồn tại!"; // Nên thay bằng trang 404 đẹp hơn nếu có
            exit;
        }

        $order = $data['info'];
        $items = $data['items'];

        require_once __DIR__ . '/../Views/layouts/header.php'; // Đảm bảo có header
        require __DIR__ . '/../Views/order/detail.php';
        require_once __DIR__ . '/../Views/layouts/footer.php'; // Đảm bảo có footer
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
                        
                        // (Tùy chọn) Có thể lưu thông báo vào session để hiện sau khi reload
                        // $_SESSION['success'] = "Tạo đơn GHN thành công: " . $trackingCode;
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

            if ($result === true) { // Chú ý so sánh === true vì updateStatus có thể trả về string lỗi
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