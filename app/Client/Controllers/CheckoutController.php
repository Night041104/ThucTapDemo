<?php
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/ProductModel.php';

class CheckoutController {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    // 1. HIỂN THỊ FORM THANH TOÁN
    public function index() {
        // Kiểm tra giỏ hàng
        if (empty($_SESSION['cart'])) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }

        // Lấy thông tin sản phẩm trong giỏ để hiển thị
        $cartIds = array_keys($_SESSION['cart']);
        $products = $this->productModel->getProductsByIds($cartIds);
        
        $totalMoney = 0;
        foreach ($products as $id => $p) {
            $qty = $_SESSION['cart'][$id];
            $totalMoney += $p['price'] * $qty;
        }

        // [MỚI] Xử lý thông tin User (nếu đã đăng nhập)
        $user = [];
        if (isset($_SESSION['user'])) {
            $u = $_SESSION['user'];
            // Ghép fname và lname thành fullname (theo bảng users mới)
            $fullname = trim(($u['lname'] ?? '') . ' ' . ($u['fname'] ?? ''));
            
            $user = [
                'fullname' => $fullname,
                'email'    => $u['email'] ?? '',
                'phone'    => '', // Bảng users chưa có phone, để trống cho khách tự nhập
                'address'  => ''  // Bảng users chưa có address
            ];
        }

        require_once __DIR__ . '/../Views/checkout/index.php';
    }

    // 2. XỬ LÝ ĐẶT HÀNG
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // A. Lấy dữ liệu từ Form (Ưu tiên những gì người dùng nhập)
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');     // [QUAN TRỌNG] Lấy email từ ô input
            $phone    = trim($_POST['phone'] ?? '');
            $address  = trim($_POST['address'] ?? '');
            $note     = trim($_POST['note'] ?? '');

            // B. Validate
            if (empty($fullname) || empty($email) || empty($phone) || empty($address)) {
                die("❌ Vui lòng điền đầy đủ Họ tên, Email, SĐT và Địa chỉ!");
            }

            // C. Chuẩn bị dữ liệu
            $cartItems = $_SESSION['cart'];
            // Lấy ID user (UUID) nếu đang đăng nhập để liên kết tài khoản
            $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null; 

            $customerData = [
                'fullname' => $fullname,
                'email'    => $email,    // Truyền email này sang Model để lưu vào đơn
                'phone'    => $phone,
                'address'  => $address,
                'note'     => $note
            ];

            // D. Gọi Model tạo đơn
            $orderCode = $this->orderModel->createOrder($userId, $customerData, $cartItems);

            if ($orderCode) {
                // --- THÀNH CÔNG ---
        
        // 1. [MỚI] Gửi Email xác nhận (Chạy ngầm, lỗi cũng không sao)
        // Chúng ta cần lấy lại thông tin chi tiết các món đã mua để hiển thị trong mail
        // (Lấy từ Model hoặc tính toán lại từ session đều được. Ở đây ta lấy từ session cho nhanh)
        
        require_once __DIR__ . '/../../Helpers/MailHelper.php'; // Load Helper
        
        // Chuẩn bị dữ liệu items để gửi mail
        $mailItems = [];
        $ids = array_keys($cartItems);
        $products = $this->productModel->getProductsByIds($ids);
        $totalForMail = 0;
        
        foreach($products as $p) {
            $qty = $cartItems[$p['id']];
            $mailItems[] = [
                'product_name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $qty
            ];
            $totalForMail += ($p['price'] * $qty);
        }

        // Gọi hàm gửi mail
        MailHelper::sendOrderConfirmation($email, $fullname, $orderCode, $totalForMail, $mailItems);

        // 2. Xóa giỏ hàng
                unset($_SESSION['cart']);

                // 2. (TODO) Gửi email xác nhận tại đây (Sẽ tích hợp sau)
                // MailHelper::sendOrderConfim($email, $orderCode, ...);

                // 3. Chuyển hướng sang trang Cảm ơn
                header("Location: index.php?controller=checkout&action=success&code=$orderCode");
                exit;
            } else {
                // --- THẤT BẠI (Do hết hàng hoặc lỗi khác) ---
                echo "<script>alert('❌ Đặt hàng thất bại! Có thể sản phẩm vừa hết hàng.'); window.location.href='index.php?controller=cart';</script>";
            }
        }
    }

    // 3. TRANG THÀNH CÔNG
    public function success() {
        $code = $_GET['code'] ?? '';
        if (!$code) {
            header("Location: index.php");
            exit;
        }
        require_once __DIR__ . '/../Views/checkout/success.php';
    }
}
?>