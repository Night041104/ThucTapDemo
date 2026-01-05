<?php
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/ProductModel.php';
// require_once __DIR__ . '/../../Helpers/MailHelper.php'; // Đã gọi trong hàm helper bên dưới
require_once __DIR__ . '/../../models/CouponModel.php';

class CheckoutController {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    // 1. HIỂN THỊ FORM THANH TOÁN
    // 1. HIỂN THỊ FORM THANH TOÁN
    public function index() {
        // --- [THÊM ĐOẠN NÀY] BẮT BUỘC ĐĂNG NHẬP ---
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiến hành thanh toán!";
            // Chuyển hướng về trang đăng nhập
            header("Location: dang-nhap"); 
            exit;
        }
        // --------------------------------------------

        // Kiểm tra giỏ hàng
        if (empty($_SESSION['cart'])) {
            header("Location: gio-hang");
            exit;
        }

        // Lấy thông tin sản phẩm
        $cartIds = array_keys($_SESSION['cart']);
        $products = $this->productModel->getProductsByIds($cartIds);
        
        $totalMoney = 0;
        
        foreach ($products as $p) {
            $realId = $p['id']; 
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

        // Xử lý thông tin User (Lấy từ Session ra để điền sẵn vào form)
        $user = [];
        // Vì đã check login ở trên, chắc chắn $_SESSION['user'] tồn tại
        $u = $_SESSION['user'];
        $fullname = trim(($u['lname'] ?? '') . ' ' . ($u['fname'] ?? ''));
        
        $user = [
            'fullname'       => $fullname,
            'email'          => $u['email'] ?? '',
            'phone'          => $u['phone'] ?? '',
            'street_address' => $u['street_address'] ?? '',
            'city'           => $u['city'] ?? '',
            'district'       => $u['district'] ?? '',
            'ward'           => $u['ward'] ?? '',
            'district_id'    => $u['district_id'] ?? '', 
            'ward_code'      => $u['ward_code'] ?? ''
        ];

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/checkout/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. XỬ LÝ ĐẶT HÀNG (SUBMIT)
    public function submit() {
        // --- [THÊM ĐOẠN NÀY] BẮT BUỘC ĐĂNG NHẬP ---
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Phiên đăng nhập hết hạn, vui lòng đăng nhập lại!";
            header("Location: dang-nhap");
            exit;
        }
        // --------------------------------------------

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Lấy dữ liệu từ Form
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            
            $street   = trim($_POST['street_address'] ?? '');
            $ward     = trim($_POST['ward'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $city     = trim($_POST['city'] ?? '');

            // Tạo chuỗi địa chỉ hiển thị
            $address = $street;
            if (!empty($ward))     $address .= ", " . $ward;
            if (!empty($district)) $address .= ", " . $district;
            if (!empty($city))     $address .= ", " . $city;
            
            $districtId = isset($_POST['district_id']) ? (int)$_POST['district_id'] : 0;
            $wardCode   = isset($_POST['ward_code']) ? trim($_POST['ward_code']) : '';

            $note     = trim($_POST['note'] ?? '');
            $paymentMethod = $_POST['payment_method'] ?? 'COD'; 

            // 2. Validate cơ bản
            if (empty($fullname) || empty($email) || empty($phone) || empty($street)) {
                die("❌ Vui lòng điền đầy đủ thông tin nhận hàng!");
            }

            // 3. Chuẩn bị dữ liệu khách hàng
            $customerData = [
                'fullname'       => $fullname,
                'email'          => $email,
                'phone'          => $phone,
                'street_address' => $street,
                'ward'           => $ward,
                'district'       => $district,
                'city'           => $city,
                'address'        => $address,
                'district_id'    => $districtId,
                'ward_code'      => $wardCode,
                'note'           => $note,
                'payment_method' => $paymentMethod
            ];

            // 4. Lấy Coupon nếu có
            $couponCode = null;
            $discountMoney = 0;
            if (isset($_SESSION['coupon'])) {
                $couponCode = $_SESSION['coupon']['code'];
                $discountMoney = $_SESSION['coupon']['discount_amount'];
            }

            // --- PHÂN LUỒNG XỬ LÝ THANH TOÁN ---

            // TRƯỜNG HỢP A: THANH TOÁN VNPAY 
            if ($paymentMethod == 'VNPAY') {
                $cartItems = $_SESSION['cart'];
                
                $cartTotal = $this->calculateTotal($cartItems);
                $finalPayment = $cartTotal - $discountMoney;
                if ($finalPayment < 0) $finalPayment = 0;

                $tempOrderRef = "TEMP" . date("ymdHis") . rand(100,999);

                $_SESSION['vnpay_holding'] = [
                    'customer_data' => $customerData,
                    'coupon_code'   => $couponCode,
                    'discount_money'=> $discountMoney,
                    'final_payment' => $finalPayment,
                    'temp_code'     => $tempOrderRef
                ];

                $this->redirectToVnPay($tempOrderRef, $finalPayment);
                exit; 
            }

            // TRƯỜNG HỢP B: THANH TOÁN COD
            else {
                // Lấy ID User từ Session (Chắc chắn có vì đã check ở đầu hàm)
                $userId = $_SESSION['user']['id'];
                $cartItems = $_SESSION['cart'];

                // Gọi Model tạo đơn hàng
                $orderCode = $this->orderModel->createOrder($userId, $customerData, $cartItems, $couponCode, $discountMoney);

                if ($orderCode) {
                    $this->logCouponUsageIfAny($orderCode, $discountMoney);
                    unset($_SESSION['cart']);
                    unset($_SESSION['coupon']);
                    $this->sendMailAndFinish($email, $fullname, $orderCode, $cartItems);
                } else {
                    echo "<script>alert('❌ Đặt hàng thất bại!'); window.location.href='gio-hang';</script>";
                }
            }
        }
    }

    // [VNPAY] Xử lý kết quả trả về (ĐÃ SỬA LOGIC)
    public function vnpay_return() {
        require_once __DIR__ . '/../../../config/vnpay_config.php';
        
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

        // KIỂM TRA CHỮ KÝ HỢP LỆ
        if ($secureHash == $vnp_SecureHash) {
            
            // --- THANH TOÁN THÀNH CÔNG ---
            if ($_GET['vnp_ResponseCode'] == '00') {
                
                // Kiểm tra xem có dữ liệu tạm trong Session không
                if (isset($_SESSION['vnpay_holding'])) {
                    $holding = $_SESSION['vnpay_holding'];

                    // Lấy lại dữ liệu từ Session
                    $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
                    $customerData = $holding['customer_data'];
                    $couponCode = $holding['coupon_code'];
                    $discountMoney = $holding['discount_money'];
                    $cartItems = $_SESSION['cart']; // Giỏ hàng vẫn còn nguyên

                    // BÂY GIỜ MỚI GỌI MODEL ĐỂ LƯU ĐƠN HÀNG VÀO DATABASE
                    // Vì tạo lúc này nên đảm bảo status mặc định là 1 (chờ xử lý) hoặc bạn có thể set luôn là 2 (đã thanh toán)
                    $orderCode = $this->orderModel->createOrder($userId, $customerData, $cartItems, $couponCode, $discountMoney);

                    if ($orderCode) {
                        // Cập nhật trạng thái thành Đã thanh toán (Status = 2)
                        $this->orderModel->updateStatusByCode($orderCode, 2);

                        // Lưu log coupon
                        $this->logCouponUsageIfAny($orderCode, $discountMoney);

                        // GỬI MAIL
                        require_once __DIR__ . '/../../Helpers/MailHelper.php';
                        $this->sendMailHelper($holding['customer_data']['email'], $holding['customer_data']['fullname'], $orderCode, $cartItems);

                        // XÓA SESSION TẠM & GIỎ HÀNG
                        unset($_SESSION['cart']);
                        unset($_SESSION['coupon']);
                        unset($_SESSION['vnpay_holding']);

                        // Chuyển hướng trang thành công
                        header("Location: dat-hang-thanh-cong?code=$orderCode&payment=vnpay");
                        exit;
                    } else {
                        echo "Lỗi tạo đơn hàng vào hệ thống (Có thể hết hàng trong lúc bạn thanh toán). Vui lòng liên hệ Admin để được hoàn tiền.";
                    }
                } else {
                    echo "Lỗi: Không tìm thấy thông tin đơn hàng tạm.";
                }

            } 
            // --- THANH TOÁN THẤT BẠI / HỦY ---
            // --- THANH TOÁN THẤT BẠI / HỦY ---
            else {
                // Chỉ xóa session tạm (Giỏ hàng vẫn giữ nguyên để khách mua lại)
                unset($_SESSION['vnpay_holding']);
                
                // 1. Lấy mã lỗi từ VNPAY để thông báo chi tiết hơn
                $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '99';
                $message = "Giao dịch không thành công do lỗi không xác định.";
                
                // Mapping mã lỗi phổ biến của VNPAY
                switch ($vnp_ResponseCode) {
                    case '24':
                        $message = "Bạn đã hủy giao dịch thanh toán.";
                        break;
                    case '51':
                        $message = "Tài khoản của bạn không đủ số dư.";
                        break;
                    case '11':
                        $message = "Đã hết hạn chờ thanh toán. Vui lòng thử lại.";
                        break;
                    case '75':
                        $message = "Ngân hàng thanh toán đang bảo trì.";
                        break;
                    default:
                        $message = "Giao dịch thất bại (Mã lỗi: $vnp_ResponseCode). Vui lòng thử lại.";
                        break;
                }

                // 2. Hiển thị thông báo đẹp bằng SweetAlert2
                // Chúng ta echo ra một trang HTML nhỏ chỉ để hiện popup rồi redirect
                echo '<!DOCTYPE html>
                <html lang="vi">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Thanh toán thất bại</title>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
                    <style>
                        body { font-family: "Roboto", sans-serif; background: #f4f4f4; }
                    </style>
                </head>
                <body>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Thanh toán thất bại!",
                                text: "' . $message . '",
                                confirmButtonText: "Quay lại trang thanh toán",
                                confirmButtonColor: "#cd1818", // Màu đỏ FPT
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Bấm nút thì quay về trang checkout
                                    window.location.href = "index.php?controller=checkout";
                                }
                            });
                        });
                    </script>
                </body>
                </html>';
                exit; // Dừng code tại đây
            }
        } else {
            echo "Chữ ký không hợp lệ!";
        }
    }

    // [HELPER MỚI] Hàm tách riêng để xử lý log coupon cho gọn
    private function logCouponUsageIfAny($orderCode, $discountMoney) {
        if (isset($_SESSION['coupon']) && isset($_SESSION['user'])) {
            $couponId = $_SESSION['coupon']['id'];
            $userIdCurrent = $_SESSION['user']['id'];
            
            $createdOrder = $this->orderModel->getOrderByCode($orderCode);
            if ($createdOrder) {
                $realOrderId = $createdOrder['info']['id']; 
                $couponModel = new CouponModel();
                $couponModel->logCouponUsage($couponId, $userIdCurrent, $realOrderId, $discountMoney);
            }
        }
    }
    
    // [HELPER MỚI] Tách hàm gửi mail để tái sử dụng
    private function sendMailHelper($email, $fullname, $orderCode, $cartItems) {
        require_once __DIR__ . '/../../models/ProductModel.php'; // Đảm bảo đã load model
        $ids = array_keys($cartItems);
        $products = $this->productModel->getProductsByIds($ids);
        
        $totalMoney = 0;
        $mailItems = [];
        foreach ($products as $p) {
            $qty = $cartItems[$p['id']];
            $totalMoney += $p['price'] * $qty;
            $mailItems[] = [
                'product_name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $qty
            ];
        }
        
        // Tính lại tổng tiền sau giảm giá (nếu cần thiết, hoặc lấy từ tham số truyền vào)
        // Ở đây gửi totalMoney gốc, hoặc bạn có thể truyền totalMoney đã trừ coupon
        MailHelper::sendOrderConfirmation($email, $fullname, $orderCode, $totalMoney, $mailItems);
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
        header("Location: dat-hang-thanh-cong?code=$code");
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

    

    // 3. TRANG THÀNH CÔNG
    public function success() {
        $code = $_GET['code'] ?? '';
        if (!$code) {
            header("Location: trang-chu");
            exit;
        }
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/checkout/success.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>