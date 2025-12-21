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
        // Lấy giỏ hàng từ Session (nếu chưa có thì là mảng rỗng)
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $products = [];
        $totalMoney = 0;

        if (!empty($cart)) {
            // Lấy danh sách ID sản phẩm: [105, 107, ...]
            $ids = array_keys($cart);
            
            // Gọi hàm getProductsByIds bạn vừa thêm vào ProductModel
            $productsRaw = $this->productModel->getProductsByIds($ids);

            // Duyệt qua để tính toán thành tiền
            foreach ($productsRaw as $p) {
                $id = $p['id'];
                // Lấy số lượng khách đang chọn mua
                $qty = $cart[$id]; 
                
                // Tính thành tiền (Giá x Số lượng)
                $p['buy_qty'] = $qty;
                $p['subtotal'] = $p['price'] * $qty;
                
                $totalMoney += $p['subtotal'];
                $products[] = $p;
            }
        }

        // Gọi View hiển thị
        require_once __DIR__ . '/../views/cart/index.php';
    }

    // 2. THÊM VÀO GIỎ (Xử lý khi bấm nút MUA NGAY)
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($id > 0 && $qty > 0) {
                // Nếu sản phẩm đã có trong giỏ -> Cộng dồn số lượng
                if (isset($_SESSION['cart'][$id])) {
                    $_SESSION['cart'][$id] += $qty;
                } else {
                    // Nếu chưa có -> Thêm mới
                    $_SESSION['cart'][$id] = $qty;
                }
            }
        }
        // Thêm xong thì chuyển hướng về trang giỏ hàng
        header("Location: index.php?controller=cart&action=index");
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
}
?>