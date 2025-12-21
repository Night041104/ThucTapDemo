<?php
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/ProductModel.php';
// require_once __DIR__ . '/../../Helpers/MailHelper.php'; // Đã gọi trong hàm helper bên dưới

class CheckoutController {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    // 1. HIỂN THỊ FORM THANH TOÁN
   // 1. HIỂN THỊ FORM THANH TOÁN (Đã sửa: Tính thêm mã giảm giá)
    // 1. HIỂN THỊ FORM THANH TOÁN (ĐÃ FIX LỖI ARRAY KEY)
    public function index() {
        // Kiểm tra giỏ hàng
        if (empty($_SESSION['cart'])) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }

        // Lấy thông tin sản phẩm
        $cartIds = array_keys($_SESSION['cart']);
        $products = $this->productModel->getProductsByIds($cartIds);
        
        $totalMoney = 0;
        
        // [ĐOẠN SỬA LỖI Ở ĐÂY]
        // Không dùng $key => $p vì $key chỉ là số thứ tự 0,1,2...
        foreach ($products as $p) {
            $realId = $p['id']; // Lấy ID thật từ dữ liệu sản phẩm
            
            if (isset($_SESSION['cart'][$realId])) {
                $qty = $_SESSION['cart'][$realId];
                $totalMoney += $p['price'] * $qty;
            }
        }

        // Xử lý hiển thị giảm giá nếu có Coupon
        $discountMoney = 0;
        if (isset($_SESSION['coupon'])) {
            $discountMoney = $_SESSION['coupon']['discount_amount'];
        }

        // Tính tổng cuối cùng
        $finalTotal = $totalMoney - $discountMoney;
        if ($finalTotal < 0) $finalTotal = 0;

        // Xử lý thông tin User
        $user = [];
        if (isset($_SESSION['user'])) {
            $u = $_SESSION['user'];
            $fullname = trim(($u['lname'] ?? '') . ' ' . ($u['fname'] ?? ''));
            $user = [
                'fullname' => $fullname,
                'email'    => $u['email'] ?? '',
                'phone'    => '', 
                'address'  => '' 
            ];
        }

        require_once __DIR__ . '/../Views/checkout/index.php';
    }

    // 2. XỬ LÝ ĐẶT HÀNG (SUBMIT)
    // XỬ LÝ KHI BẤM NÚT "XÁC NHẬN ĐẶT HÀNG"
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Lấy dữ liệu từ Form
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            $address  = trim($_POST['address'] ?? '');
            $note     = trim($_POST['note'] ?? '');
            $paymentMethod = $_POST['payment_method'] ?? 'COD'; 

            // 2. Validate cơ bản
            if (empty($fullname) || empty($email) || empty($phone) || empty($address)) {
                die("❌ Vui lòng điền đầy đủ Họ tên, Email, SĐT và Địa chỉ!");
            }

            // 3. Chuẩn bị dữ liệu
            $cartItems = $_SESSION['cart'];
            $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

            $customerData = [
                'fullname' => $fullname,
                'email'    => $email,
                'phone'    => $phone,
                'address'  => $address,
                'note'     => $note,
                'payment_method' => $paymentMethod
            ];

            // [MỚI] Lấy thông tin Coupon từ Session (Nếu có)
            $couponCode = null;
            $discountMoney = 0;
            
            if (isset($_SESSION['coupon'])) {
                $couponCode = $_SESSION['coupon']['code'];
                $discountMoney = $_SESSION['coupon']['discount_amount'];
            }

            // 4. Gọi Model tạo đơn hàng
            // Truyền thêm $couponCode và $discountMoney
            $orderCode = $this->orderModel->createOrder($userId, $customerData, $cartItems, $couponCode, $discountMoney);

            if ($orderCode) {
                // --- ĐẶT HÀNG THÀNH CÔNG ---
                
                // Xóa giỏ hàng và Coupon ngay lập tức
                unset($_SESSION['cart']);
                unset($_SESSION['coupon']); // <--- Quan trọng: Xóa mã để không bị lưu cho đơn sau

                // --- PHÂN LUỒNG THANH TOÁN ---
                if ($paymentMethod == 'VNPAY') {
                    // ==> NẾU CHỌN VNPAY:
                    // 1. Tính tổng tiền hàng gốc
                    $cartTotal = $this->calculateTotal($cartItems);
                    
                    // 2. Trừ đi tiền giảm giá (Để gửi sang VNPAY số tiền thực phải trả)
                    $finalPayment = $cartTotal - $discountMoney;
                    if ($finalPayment < 0) $finalPayment = 0; // Không để âm tiền

                    // 3. Chuyển hướng
                    $this->redirectToVnPay($orderCode, $finalPayment);
                    
                } else {
                    // ==> NẾU CHỌN COD: Gửi mail và Kết thúc
                    $this->sendMailAndFinish($email, $fullname, $orderCode, $cartItems);
                }

            } else {
                // --- THẤT BẠI ---
                echo "<script>alert('❌ Đặt hàng thất bại! Có thể sản phẩm vừa hết hàng hoặc hệ thống bận.'); window.location.href='index.php?controller=cart';</script>";
            }
        }
    }

    // [HELPER RIÊNG] Xử lý gửi mail và chuyển trang hoàn tất (FULL CODE)
    private function sendMailAndFinish($email, $name, $code, $cartItems) {
        // 1. Load file Helper gửi mail
        require_once __DIR__ . '/../../Helpers/MailHelper.php';

        // 2. Lấy thông tin chi tiết sản phẩm từ Database
        $ids = array_keys($cartItems);
        $products = $this->productModel->getProductsByIds($ids);

        // 3. Chuẩn bị dữ liệu để gửi vào MailHelper
        $mailItems = [];
        $totalMoney = 0;

        foreach ($products as $p) {
            $qty = $cartItems[$p['id']];
            
            // Tính tổng tiền lại
            $totalMoney += $p['price'] * $qty;

            // Tạo mảng item chuẩn cấu trúc
            $mailItems[] = [
                'product_name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $qty
            ];
        }

        // 4. Gọi hàm gửi mail
        MailHelper::sendOrderConfirmation($email, $name, $code, $totalMoney, $mailItems);

        // 5. Chuyển hướng
        header("Location: index.php?controller=checkout&action=success&code=$code");
        exit;
    }

    // [HELPER] Tính tổng tiền
    private function calculateTotal($cartItems) {
        $ids = array_keys($cartItems);
        $products = $this->productModel->getProductsByIds($ids);
        $total = 0;
        foreach ($products as $p) {
            $total += $p['price'] * $cartItems[$p['id']];
        }
        return $total;
    }

    // [VNPAY] Chuyển hướng sang cổng thanh toán
   // [CHUẨN] Hàm chuyển hướng sang VNPAY (Đã bỏ chế độ Debug)
    private function redirectToVnPay($orderCode, $totalMoney) {
        require __DIR__ . '/../../../config/vnpay_config.php';

        $vnp_TxnRef = $orderCode;
        $vnp_OrderInfo = "Thanh toan don hang " . $orderCode;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = (int)$totalMoney * 100;
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_CreateDate = date('YmdHis');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng ngay lập tức
        header('Location: ' . $vnp_Url);
        exit;
    }

    // [VNPAY] Xử lý kết quả trả về
    public function vnpay_return() {
        // ... (Phần load config và check hash giữ nguyên) ...
        require_once __DIR__ . '/../../../config/vnpay_config.php';
        
        // (Logic xử lý dữ liệu đầu vào giữ nguyên) ...
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // --- THANH TOÁN THÀNH CÔNG ---
                $orderCode = $inputData['vnp_TxnRef'];
                
                // 1. Cập nhật trạng thái "Đã thanh toán" (Status = 2)
                $this->orderModel->updateStatusByCode($orderCode, 2); 
                
                // 2. [MỚI] GỬI MAIL XÁC NHẬN
                // Vì giỏ hàng đã xóa, phải lấy thông tin từ DB lên để gửi
                $orderData = $this->orderModel->getOrderByCode($orderCode);
                
                if ($orderData) {
                    require_once __DIR__ . '/../../Helpers/MailHelper.php';
                    
                    $info = $orderData['info'];
                    $items = $orderData['items'];
                    
                    // Chuẩn bị dữ liệu items cho MailHelper
                    $mailItems = [];
                    foreach ($items as $item) {
                        $mailItems[] = [
                            'product_name' => $item['product_name'],
                            'price'        => $item['price'],
                            'quantity'     => $item['quantity']
                        ];
                    }

                    // Gọi hàm gửi mail
                    MailHelper::sendOrderConfirmation(
                        $info['email'], 
                        $info['fullname'], 
                        $orderCode, 
                        $info['total_money'], 
                        $mailItems
                    );
                }

                // 3. Chuyển hướng
                header("Location: index.php?controller=checkout&action=success&code=$orderCode&payment=vnpay");
            } else {
                echo "Giao dịch không thành công. Mã lỗi: " . $_GET['vnp_ResponseCode'];
                echo "<br><a href='index.php'>Về trang chủ</a>";
            }
        } else {
            echo "Chữ ký không hợp lệ!";
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