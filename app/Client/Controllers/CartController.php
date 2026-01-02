<?php
// Load ProductModel để lấy thông tin sản phẩm hiển thị
require_once __DIR__ . '/../../models/ProductModel.php';

class CartController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    // 1. HIỂN THỊ GIỎ HÀNG
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $products = [];
        $totalMoney = 0;

        if (!empty($cart)) {
            $ids = array_keys($cart);
            $products = $this->productModel->getProductsByIds($ids);

            foreach ($products as $p) {
                $totalMoney += $p['price'] * $cart[$p['id']];
            }
        }

        // [MỚI] Xử lý hiển thị Coupon
        $discountAmount = 0;
        $finalTotal = $totalMoney;

        // Nếu trong Session có mã giảm giá -> Tính lại
        if (isset($_SESSION['coupon'])) {
            $coupon = $_SESSION['coupon'];
            
            // Kiểm tra lại lần nữa xem mã còn hợp lệ với tổng tiền mới không
            // (Phòng trường hợp khách xóa bớt hàng làm tổng tiền giảm xuống dưới mức tối thiểu)
            require_once __DIR__ . '/../../models/CouponModel.php';
            $couponModel = new CouponModel();
            $check = $couponModel->checkCoupon($coupon['code'], $totalMoney);
            
            if ($check['valid']) {
                // Tính lại tiền giảm (cập nhật nếu tổng tiền thay đổi)
                if ($coupon['type'] == 'fixed') {
                    $discountAmount = $coupon['value'];
                } else {
                    $discountAmount = ($totalMoney * $coupon['value']) / 100;
                }
                
                // Cập nhật lại session số tiền giảm mới nhất
                $_SESSION['coupon']['discount_amount'] = $discountAmount;
            } else {
                // Nếu không còn hợp lệ (do xóa bớt hàng) -> Tự động hủy mã
                unset($_SESSION['coupon']);
                $discountAmount = 0;
                $_SESSION['error'] = "Mã giảm giá đã bị hủy do đơn hàng không đủ điều kiện!";
            }
        }

        $finalTotal = $totalMoney - $discountAmount;
        if ($finalTotal < 0) $finalTotal = 0;

        // Load Header
    require_once __DIR__ . '/../Views/layouts/header.php';
    
    // Load nội dung giỏ hàng (File bạn vừa sửa ở Bước 1)
    require_once __DIR__ . '/../Views/cart/index.php';
    
    // Load Footer
    require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. THÊM VÀO GIỎ (Xử lý khi bấm nút MUA NGAY)
    // THÊM SẢN PHẨM VÀO GIỎ HÀNG
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($productId > 0 && $quantity > 0) {
                
                // 1. Kiểm tra sản phẩm có tồn tại và còn hàng không
                // (Dùng hàm getById của ProductModel mà bên Product đã cung cấp)
                $product = $this->productModel->getById($productId);

                if (!$product) {
                    echo "<script>alert('Sản phẩm không tồn tại!'); window.history.back();</script>";
                    exit;
                }

                if ($product['quantity'] < $quantity) {
                    echo "<script>alert('Sản phẩm này chỉ còn {$product['quantity']} cái!'); window.history.back();</script>";
                    exit;
                }

                // 2. Khởi tạo giỏ hàng nếu chưa có
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // 3. Logic thêm vào giỏ
                if (isset($_SESSION['cart'][$productId])) {
                    // Nếu đã có -> Cộng dồn số lượng
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    // Nếu chưa có -> Thêm mới
                    $_SESSION['cart'][$productId] = $quantity;
                }

                // 4. Kiểm tra xem khách bấm nút nào?
                if (isset($_POST['buy_now'])) {
                    // A. Nếu bấm "MUA NGAY" -> Chuyển thẳng đến trang Thanh toán
                    header("Location: index.php?controller=checkout");
                } else {
                    // B. Nếu bấm "THÊM GIỎ" -> Chuyển về trang Giỏ hàng (hoặc ở lại trang cũ tùy bạn)
                    // Ở đây tôi cho về trang Giỏ hàng để khách nhìn thấy kết quả
                    header("Location: index.php?controller=cart");
                }
                exit;
            }
        }
        
        // Nếu truy cập trực tiếp link add mà không post gì cả -> Về trang chủ
        header("Location: index.php");
        exit;
    }

    // 3. CẬP NHẬT SỐ LƯỢNG (Khi sửa ô input trong giỏ hàng)
    public function update() {
        if (isset($_POST['qty']) && is_array($_POST['qty'])) {
            foreach ($_POST['qty'] as $id => $qty) {
                $id = (int)$id;
                $qty = (int)$qty;
                
                if ($qty <= 0) {
                    // Nếu nhập số <= 0 thì xóa luôn
                    unset($_SESSION['cart'][$id]);
                } else {
                    $_SESSION['cart'][$id] = $qty;
                }
            }
        }
        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    // 4. XÓA SẢN PHẨM KHỎI GIỎ
    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            unset($_SESSION['cart'][$id]);
        }
        header("Location: index.php?controller=cart&action=index");
        exit;
    }
    // [MỚI] 1. Xử lý áp dụng mã giảm giá
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            
            if (empty($code)) {
                $_SESSION['error'] = "Vui lòng nhập mã giảm giá!";
                header("Location: index.php?controller=cart");
                exit;
            }

            // Tính tổng tiền hiện tại của giỏ hàng
            $currentTotal = $this->calculateCartTotal();

            // Gọi Model kiểm tra mã
            require_once __DIR__ . '/../../models/CouponModel.php';
            $couponModel = new CouponModel();
            $check = $couponModel->checkCoupon($code, $currentTotal);

            if ($check['valid'] == false) {
                // Mã sai hoặc không đủ điều kiện
                $_SESSION['error'] = $check['msg'];
                // Xóa mã cũ nếu có để tránh hiểu lầm
                unset($_SESSION['coupon']); 
            } else {
                // Mã đúng -> Tính toán số tiền được giảm
                $couponData = $check['data'];
                $discountAmount = 0;

                if ($couponData['type'] == 'fixed') {
                    // Giảm tiền mặt (VD: 50k)
                    $discountAmount = $couponData['value'];
                } else {
                    // Giảm phần trăm (VD: 10%)
                    $discountAmount = ($currentTotal * $couponData['value']) / 100;
                }

                // Đảm bảo tiền giảm không lớn hơn tổng đơn (không để âm tiền)
                if ($discountAmount > $currentTotal) {
                    $discountAmount = $currentTotal;
                }

                // Lưu thông tin vào Session để dùng cho trang Thanh toán
                $_SESSION['coupon'] = [
                    'code' => $couponData['code'],
                    'type' => $couponData['type'],
                    'value' => $couponData['value'],
                    'discount_amount' => $discountAmount
                ];

                $_SESSION['success'] = "Đã áp dụng mã '{$code}' thành công!";
            }

            header("Location: index.php?controller=cart");
            exit;
        }
    }

    // [MỚI] 2. Hủy mã giảm giá
    public function removeCoupon() {
        if (isset($_SESSION['coupon'])) {
            unset($_SESSION['coupon']);
            $_SESSION['success'] = "Đã hủy mã giảm giá!";
        }
        header("Location: index.php?controller=cart");
        exit;
    }

    // [MỚI] 3. Hàm tính tổng tiền giỏ hàng (Helper)
    private function calculateCartTotal() {
        if (empty($_SESSION['cart'])) return 0;

        $cartIds = array_keys($_SESSION['cart']);
        $products = $this->productModel->getProductsByIds($cartIds);
        
        $total = 0;
        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $total += $p['price'] * $qty;
        }
        return $total;
    }
    // [AJAX] Thêm vào giỏ hàng không load lại trang
    public function addAjax() {
        header('Content-Type: application/json'); // Báo trình duyệt đây là JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($productId > 0 && $quantity > 0) {
                
                // 1. Kiểm tra sản phẩm
                $product = $this->productModel->getById($productId);

                if (!$product) {
                    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại!']);
                    exit;
                }

                if ($product['quantity'] < $quantity) {
                    echo json_encode(['status' => 'error', 'message' => "Chỉ còn {$product['quantity']} sản phẩm!"]);
                    exit;
                }

                // 2. Thêm vào Session
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }

                // 3. Tính tổng số lượng mới để cập nhật lên Header
                $newTotalQty = array_sum($_SESSION['cart']);

                // 4. Trả về thành công
                echo json_encode([
                    'status' => 'success', 
                    'totalQty' => $newTotalQty,
                    'message' => 'Đã thêm vào giỏ hàng thành công!'
                ]);
                exit;
            }
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ']);
        exit;
    }
}
?>