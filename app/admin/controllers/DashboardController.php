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
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            header("Location: index.php?module=client&controller=auth&action=login");
            exit;
        }

        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        // 1. LẤY DỮ LIỆU TỪ DB
        // [FIX] Sử dụng hàm getAll() đúng với ProductModel hiện tại
        $products = $this->productModel->getAll(); 
        
        // Lưu ý: Kiểm tra lại OrderModel của bạn, đảm bảo có hàm getAllOrders()
        // Nếu tên hàm bên đó là getAll() thì bạn sửa dòng dưới thành getAll() nhé.
        $orders   = $this->orderModel->getAllOrders();     
        $users    = $this->userModel->getAllUsers();       

        // 2. TÍNH TOÁN THỐNG KÊ
        $stats = [
            'total_revenue' => 0,
            'total_orders'  => count($orders),
            'total_products'=> count($products),
            'total_users'   => count($users)
        ];

        // Mảng đếm trạng thái đơn hàng: 1-Mới, 2-Duyệt, 3-Giao, 4-Xong, 5-Hủy
        $orderStatusCounts = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0]; 
        
        if (!empty($orders)) {
            foreach ($orders as $order) {
                // Cộng doanh thu: Chỉ tính đơn Hoàn thành (4) hoặc Đã thanh toán VNPAY (2)
                if ($order['status'] == 4 || ($order['status'] == 2 && $order['payment_method'] == 'VNPAY')) {
                    $stats['total_revenue'] += $order['total_money'];
                }
                
                // Đếm số lượng theo trạng thái
                if(isset($orderStatusCounts[$order['status']])) {
                    $orderStatusCounts[$order['status']]++;
                }
            }
            
            // Lấy 5 đơn mới nhất (Đảo ngược mảng để lấy đơn cuối cùng nếu model chưa sort)
            // Giả sử orders lấy ra đã sort DESC created_at thì dùng array_slice luôn
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
            // Chỉ lấy 5 sản phẩm đầu tiên để hiển thị cho gọn
            $lowStockProducts = array_slice($lowStockProducts, 0, 5);
        }

        // 4. DỮ LIỆU BIỂU ĐỒ (Giả lập cho Demo)
        // Thực tế bạn cần viết câu SQL GROUP BY MONTH(created_at) để lấy dữ liệu này chính xác
        $chartData = [
            'labels' => ['T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            'data'   => [12000000, 19000000, 15000000, 25000000, 30000000, $stats['total_revenue']] 
        ];

        // Load View
        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}