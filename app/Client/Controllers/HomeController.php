<?php
require_once __DIR__ . '/../../Models/ProductModel.php';
// Lưu ý: Không cần require CategoryModel ở đây vì header.php đã tự require rồi, 
// nhưng để code "sạch" thì nên require nếu controller dùng đến nó.

class HomeController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function index() {
        // 1. Sản phẩm Hot (Flash Sale)
        $hotProducts = $this->productModel->getAll(0, ''); 
        $hotProducts = array_slice($hotProducts, 0, 5); 

        // 2. Điện thoại (ID = 3 trong DB của bạn)
        $phoneProducts = $this->productModel->getProductsByCateForClient(3);
        $phoneProducts = array_slice($phoneProducts, 0, 8); 

        // 3. Laptop (Giả sử ID = 2, nếu chưa có trong DB thì sẽ trả về rỗng, ko lỗi)
        $laptopProducts = $this->productModel->getProductsByCateForClient(2);
        $laptopProducts = array_slice($laptopProducts, 0, 8);
        
        // 4. [MỚI] Phụ kiện / Tai nghe (ID = 4 trong DB của bạn)
        $accessoryProducts = $this->productModel->getProductsByCateForClient(4);
        $accessoryProducts = array_slice($accessoryProducts, 0, 10);

        // Load View
        // Header sẽ tự động load BrandModel và CategoryModel để render Menu
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>