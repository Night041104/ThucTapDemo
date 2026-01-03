<?php
// Load các Model
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/UserModel.php';

class DashboardController {
    private $productModel;
    private $orderModel;
    private $userModel;

    public function __construct() {
        // Kiểm tra quyền Admin
        // Nếu chưa đăng nhập hoặc không phải admin -> Đá về trang đăng nhập client
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Dùng ../dang-nhap để thoát khỏi prefix "admin/"
            header("Location: ../dang-nhap");
            exit;
        }

        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        // 1. LẤY DỮ LIỆU TỪ DB
        $products = $this->productModel->getAll(); 
        $orders   = $this->orderModel->getAllOrders(); // Đảm bảo OrderModel có hàm này
        $users    = $this->userModel->getAllUsers();       

        // 2. TÍNH TOÁN THỐNG KÊ
        $stats = [
            'total_revenue' => 0,
            'total_orders'  => count($orders),
            'total_products'=> count($products),
            'total_users'   => count($users)
        ];

        $orderStatusCounts = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0]; 
        
        if (!empty($orders)) {
            foreach ($orders as $order) {
                // Cộng doanh thu: Đơn hoàn thành (4) hoặc Đã thanh toán (2) & VNPAY
                if ($order['status'] == 4 || ($order['status'] == 2 && $order['payment_method'] == 'VNPAY')) {
                    $stats['total_revenue'] += $order['total_money'];
                }
                
                if(isset($orderStatusCounts[$order['status']])) {
                    $orderStatusCounts[$order['status']]++;
                }
            }
            // Lấy 5 đơn mới nhất
            $recentOrders = array_slice($orders, 0, 5); 
        } else {
            $recentOrders = [];
        }

        // 3. TÌM SẢN PHẨM SẮP HẾT HÀNG (Low Stock <= 5)
        $lowStockProducts = [];
        if (!empty($products)) {
            foreach($products as $p) {
                if($p['quantity'] <= 5) {
                    $lowStockProducts[] = $p;
                }
            }
            $lowStockProducts = array_slice($lowStockProducts, 0, 5);
        }

        // 4. BIỂU ĐỒ (Giữ nguyên giả lập)
        $chartData = [
            'labels' => ['T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            'data'   => [12000000, 19000000, 15000000, 25000000, 30000000, $stats['total_revenue']] 
        ];

        // Load View
        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}
?>