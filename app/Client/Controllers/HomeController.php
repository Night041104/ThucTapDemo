<?php
require_once __DIR__ . '/../../Models/ProductModel.php';
// Load thêm CategoryModel nếu bạn muốn lấy danh mục động từ DB
// require_once __DIR__ . '/../../Models/CategoryModel.php';

class HomeController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function index() {
        // 1. Lấy sản phẩm nổi bật (Giả sử lấy 4 sản phẩm mới nhất)
        // Bạn có thể viết thêm hàm getHotProducts() trong Model nếu muốn
        $hotProducts = $this->productModel->getAll(0, ''); 
        $hotProducts = array_slice($hotProducts, 0, 5); // Lấy 5 sp đầu

        // 2. Lấy Điện thoại (Giả sử cate_id = 1)
        $phoneProducts = $this->productModel->getProductsByCateForClient(1);
        $phoneProducts = array_slice($phoneProducts, 0, 8); // Lấy 8 cái

        // 3. Lấy Laptop (Giả sử cate_id = 2)
        $laptopProducts = $this->productModel->getProductsByCateForClient(2);
        $laptopProducts = array_slice($laptopProducts, 0, 8);

        // Load View (Lắp ráp Header + Home Body + Footer)
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>