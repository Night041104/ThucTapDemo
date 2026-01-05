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

        // 1. Tính tổng tiền hàng
        if (!empty($cart)) {
            $ids = array_keys($cart);
            $products = $this->productModel->getProductsByIds($ids);

            foreach ($products as $p) {
                // Kiểm tra key tồn tại để tránh lỗi warning
                if (isset($cart[$p['id']])) {
                    $totalMoney += $p['price'] * $cart[$p['id']];
                }
            }
        }

        // [MỚI] Xử lý hiển thị Coupon
        $discountAmount = 0;

        // Nếu trong Session có mã giảm giá -> Tính lại
        if (isset($_SESSION['coupon'])) {
            $couponCode = $_SESSION['coupon']['code'];
            
            require_once __DIR__ . '/../../models/CouponModel.php';
            $couponModel = new CouponModel();
            
            // Lấy User ID (nếu có) để check giới hạn
            $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

            // Gọi hàm checkCoupon để Model tự tính toán lại (bao gồm cả max_discount)
            $check = $couponModel->checkCoupon($couponCode, $totalMoney, $userId);
            
            if ($check['valid']) {
                // --- ĐOẠN SỬA QUAN TRỌNG ---
                // Không tự tính toán nữa, lấy kết quả chuẩn từ Model
                $discountAmount = $check['discount_amount'];
                
                // Cập nhật lại session để đồng bộ
                $_SESSION['coupon']['discount_amount'] = $discountAmount;
            } else {
                // Nếu không còn hợp lệ (do xóa bớt hàng) -> Tự động hủy mã
                unset($_SESSION['coupon']);
                $discountAmount = 0;
                $_SESSION['error'] = "Mã giảm giá đã bị hủy do đơn hàng không đủ điều kiện!";
            }
        }

        // Tính tổng cuối
        $finalTotal = $totalMoney - $discountAmount;
        if ($finalTotal < 0) $finalTotal = 0;

        // --- [THÊM ĐOẠN NÀY] Lấy danh sách Coupon ---
    require_once __DIR__ . '/../../models/CouponModel.php';
    $couponModel = new CouponModel();
    
    // Lấy ID người dùng hiện tại (nếu chưa đăng nhập thì là null)
$currentUserId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

// Truyền ID vào để Model đếm số lần sử dụng
$listCoupons = $couponModel->getAllActiveCoupons($currentUserId);

        // Load View
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/cart/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. THÊM VÀO GIỎ (Xử lý khi bấm nút MUA NGAY)
    // THÊM SẢN PHẨM VÀO GIỎ HÀNG
   // 2. THÊM VÀO GIỎ (Gộp chung logic: Mua ngay & Ajax Thêm giỏ)
    // 2. THÊM VÀO GIỎ (Gộp chung logic: Mua ngay & Ajax Thêm giỏ)
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Kiểm tra cờ AJAX gửi từ Javascript
            $isAjax = isset($_POST['is_ajax']) || isset($_POST['add_to_cart']); 

            // Chuẩn bị header JSON nếu là AJAX
            if ($isAjax) {
                header('Content-Type: application/json');
            }

            if ($productId > 0 && $quantity > 0) {
                
                // 1. Lấy thông tin sản phẩm mới nhất từ DB
                $product = $this->productModel->getById($productId);

                if (!$product) {
                    $msg = 'Sản phẩm không tồn tại!';
                    if ($isAjax) { echo json_encode(['status'=>'error', 'message'=>$msg]); exit; }
                    else { echo "<script>alert('$msg'); window.history.back();</script>"; exit; }
                }

                // 2. Kiểm tra trạng thái Ngừng kinh doanh
                if ($product['status'] == -1) {
                    $msg = 'Sản phẩm này đã ngừng kinh doanh, không thể mua!';
                    if ($isAjax) { echo json_encode(['status'=>'error', 'message'=>$msg]); exit; }
                    else { echo "<script>alert('$msg'); window.history.back();</script>"; exit; }
                }

                // 3. [LOGIC QUAN TRỌNG] Kiểm tra tổng số lượng (Trong giỏ + Mua thêm) vs Tồn kho
                
                // Lấy số lượng đang có trong giỏ (nếu chưa có thì là 0)
                $currentCartQty = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] : 0;
                
                // Tổng số lượng khách muốn sở hữu
                $totalWanted = $currentCartQty + $quantity;

                if ($totalWanted > $product['quantity']) {
                    // Thông báo chi tiết
                    $msg = "Kho chỉ còn {$product['quantity']} sản phẩm.";
                    if ($currentCartQty > 0) {
                        $msg .= " Bạn đã có $currentCartQty trong giỏ, không thể thêm $quantity nữa.";
                    }
                    
                    if ($isAjax) { 
                        echo json_encode(['status'=>'error', 'message'=>$msg]); 
                        exit; 
                    } else { 
                        echo "<script>alert('$msg'); window.history.back();</script>"; 
                        exit; 
                    }
                }

                // 4. Khởi tạo giỏ hàng nếu chưa có
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // 5. Cập nhật giỏ hàng (Gán bằng tổng số lượng đã tính)
                $_SESSION['cart'][$productId] = $totalWanted;

                // 6. Phản hồi kết quả
                if ($isAjax) {
                    // Tính tổng số lượng hiển thị trên Header
                    $newTotalQty = array_sum($_SESSION['cart']);
                    
                    echo json_encode([
                        'status'      => 'success',
                        'message'     => 'Thêm vào giỏ thành công!',
                        'total_items' => $newTotalQty
                    ]);
                    exit; 
                } else {
                    // Nếu là Form Submit thường (Nút Mua ngay) -> Chuyển hướng
                    if (isset($_POST['buy_now'])) {
                        header("Location: thanh-toan");
                    } else {
                        header("Location: gio-hang");
                    }
                    exit;
                }
            }
        }
        
        // Fallback
        header("Location: trang-chu");
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
        header("Location: gio-hang");
        exit;
    }

    // 4. XÓA SẢN PHẨM KHỎI GIỎ
    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            unset($_SESSION['cart'][$id]);
        }
        header("Location: gio-hang");
        exit;
    }
    // [MỚI] 1. Xử lý áp dụng mã giảm giá
    // Xử lý áp dụng mã giảm giá
    public function applyCoupon() {
        // 1. Kiểm tra request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: gio-hang");
            exit;
        }

        // 2. Kiểm tra giỏ hàng trống
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Giỏ hàng đang trống, không thể áp dụng mã!";
            header("Location: gio-hang");
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        if (empty($code)) {
            $_SESSION['error'] = "Vui lòng nhập mã giảm giá!";
            header("Location: gio-hang");
            exit;
        }

        // 3. Tính tổng tiền hiện tại của giỏ hàng
        // (Phải tính lại từ DB để đảm bảo chính xác, không lấy từ giao diện client gửi lên)
        require_once __DIR__ . '/../../models/ProductModel.php';
        $productModel = new ProductModel();
        
        $cartItems = $_SESSION['cart'];
        $productIds = array_keys($cartItems);
        $products = $productModel->getProductsByIds($productIds);

        $totalOrderAmount = 0;
        foreach ($products as $p) {
            if (isset($cartItems[$p['id']])) {
                $qty = $cartItems[$p['id']];
                $totalOrderAmount += $p['price'] * $qty;
            }
        }

        // 4. Gọi CouponModel để kiểm tra
        require_once __DIR__ . '/../../models/CouponModel.php';
        $couponModel = new CouponModel();

        // Lấy ID người dùng (nếu đã đăng nhập) để check giới hạn lượt dùng
        $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

        // Gọi hàm checkCoupon (Hàm này bạn đã cập nhật ở bước trước)
        $result = $couponModel->checkCoupon($code, $totalOrderAmount, $userId);

        // 5. Xử lý kết quả trả về
        if ($result['valid']) {
            // --- THÀNH CÔNG ---
            // Lưu thông tin mã vào Session
            $_SESSION['coupon'] = [
                'id'              => $result['data']['id'],      // ID để lưu lịch sử sau này
                'code'            => $result['data']['code'],    // Mã hiển thị
                'type'            => $result['data']['type'],    // Loại (fixed/percent)
                'value'           => $result['data']['value'],   // Giá trị gốc
                'discount_amount' => $result['discount_amount']  // Số tiền thực tế được giảm (Model đã tính)
            ];
            
            $_SESSION['success'] = $result['msg']; // "Áp dụng thành công..."
        } else {
            // --- THẤT BẠI ---
            // Xóa mã cũ nếu có (để tránh đang dùng mã sai mà session vẫn lưu mã cũ)
            unset($_SESSION['coupon']);
            $_SESSION['error'] = $result['msg']; // Lý do lỗi (Hết hạn, chưa đủ tiền...)
        }

        // 6. Quay lại trang giỏ hàng
        header("Location: gio-hang");
        exit;
    }

    // Hàm xóa mã giảm giá (User bấm nút X)
    public function removeCoupon() {
        unset($_SESSION['coupon']);
        $_SESSION['success'] = "Đã gỡ bỏ mã giảm giá!";
        header("Location: gio-hang");
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
    // Trong file CartController.php

public function updateAjax() {
        // 1. Chỉ nhận request POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }

        // 2. Lấy dữ liệu từ JS gửi lên
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

        if ($qty < 1) $qty = 1; // Đảm bảo số lượng tối thiểu là 1

        // 3. Cập nhật Session giỏ hàng
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $qty;
        }
        // --- [THÊM ĐOẠN NÀY] Tính tổng số lượng sản phẩm trong giỏ ---
        $newTotalQty = array_sum($_SESSION['cart']);

        // 4. Tính toán lại toàn bộ giỏ hàng
        require_once __DIR__ . '/../../models/ProductModel.php';
        $productModel = new ProductModel();
        
        $cart = $_SESSION['cart'];
        $ids = array_keys($cart);
        // Nếu giỏ hàng trống thì return nhanh
        if (empty($ids)) {
             echo json_encode(['status' => 'success', 'total_money' => 0, 'final_total' => 0]);
             exit;
        }

        $products = $productModel->getProductsByIds($ids);

        $totalMoney = 0;
        $itemSubtotal = 0; // Thành tiền của riêng sản phẩm vừa sửa

        foreach ($products as $p) {
            if (isset($cart[$p['id']])) {
                $currentQty = $cart[$p['id']];
                $lineTotal = $p['price'] * $currentQty;
                $totalMoney += $lineTotal;

                if ($p['id'] == $id) {
                    $itemSubtotal = $lineTotal;
                }
            }
        }

        // --- [PHẦN MỚI QUAN TRỌNG] TẠO LẠI HTML DANH SÁCH MÃ GIẢM GIÁ ---
        // Mục đích: Để Modal cập nhật trạng thái sáng/tối ngay lập tức theo tổng tiền mới
        require_once __DIR__ . '/../../models/CouponModel.php';
        $couponModel = new CouponModel();
        $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

        // Lấy danh sách coupon mới nhất (kèm số lần user đã dùng)
        $listCoupons = $couponModel->getAllActiveCoupons($userId); 
        
        // Dùng Output Buffering để render file view thành chuỗi HTML
        ob_start();
        // Truyền biến vào view: $listCoupons và $totalMoney (để so sánh điều kiện)
        require __DIR__ . '/../Views/cart/coupon_list.php'; 
        $couponHtml = ob_get_clean(); 
        // -----------------------------------------------------------------


        // 5. Tính toán lại Coupon ĐANG ÁP DỤNG (nếu có)
        $discountAmount = 0;
        $couponMsg = '';
        $couponValid = false;

        if (isset($_SESSION['coupon'])) {
            $couponCode = $_SESSION['coupon']['code'];

            // Check lại điều kiện coupon với tổng tiền mới
            $check = $couponModel->checkCoupon($couponCode, $totalMoney, $userId);

            if ($check['valid']) {
                $discountAmount = $check['discount_amount'];
                $_SESSION['coupon']['discount_amount'] = $discountAmount; // Cập nhật Session
                $couponValid = true;
            } else {
                // Coupon không còn hợp lệ (do tổng tiền giảm xuống dưới mức tối thiểu)
                unset($_SESSION['coupon']);
                $discountAmount = 0;
                $couponMsg = 'Mã giảm giá đã bị hủy do đơn hàng không đủ điều kiện!';
            }
        }

        // 6. Tính tổng cuối cùng
        $finalTotal = $totalMoney - $discountAmount;
        if ($finalTotal < 0) $finalTotal = 0;

        // 7. Trả về JSON cho Javascript
        echo json_encode([
            'status' => 'success',
            'item_subtotal'   => number_format($itemSubtotal, 0, ',', '.') . '₫',
            'total_money'     => number_format($totalMoney, 0, ',', '.') . '₫',
            'discount_amount' => number_format($discountAmount, 0, ',', '.') . '₫',
            'final_total'     => number_format($finalTotal, 0, ',', '.') . '₫',
            // --- [THÊM DÒNG NÀY] Gửi tổng số lượng mới về Client ---
            'total_qty'       => $newTotalQty,
            'coupon_valid'    => $couponValid,
            'coupon_msg'      => $couponMsg,
            'coupon_html'     => $couponHtml // <--- HTML mới của Modal
        ]);
        exit;
    }
}
?>