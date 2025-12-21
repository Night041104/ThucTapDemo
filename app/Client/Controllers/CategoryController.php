<?php
// LƯU Ý ĐƯỜNG DẪN:
// __DIR__ đang là: app/Client/Controllers
// Muốn ra models (app/models) -> phải lùi 2 cấp: /../../models

require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/BrandModel.php';
require_once __DIR__ . '/../../models/AttributeModel.php';

class CategoryController {
    private $cateModel;
    private $prodModel;
    private $brandModel;
    private $attrModel;

    public function __construct() {
        $this->cateModel  = new CategoryModel();
        $this->prodModel  = new ProductModel();
        $this->brandModel = new BrandModel();
        $this->attrModel  = new AttributeModel();
    }

    // URL: index.php?module=client&controller=category&action=index&id=1
    public function index() {
        $cateId = $_GET['id'] ?? 0;
        
        // 1. Lấy thông tin danh mục
        $category = $this->cateModel->getById($cateId);
        if (!$category) {
            die("<h3 style='text-align:center; margin-top:50px;'>Danh mục không tồn tại!</h3>");
        }

        // 2. Lấy danh sách sản phẩm (Chỉ lấy SP Cha)
        $products = $this->prodModel->getProductsByCateForClient($cateId);

        // 3. Lấy dữ liệu Sidebar (Bộ lọc)
        $filterBrands = $this->brandModel->getByCategoryId($cateId);
        $filterAttrs  = $this->attrModel->getFiltersByCateForClient($cateId);

        // 4. Load View
        // __DIR__ là app/Client/Controllers
        // View nằm ở app/Client/views -> lùi 1 cấp: /../views
        
        // [QUAN TRỌNG]: Tôi giả định bạn đã di chuyển header.php ra app/Client/views/header.php
        // Nếu bạn vẫn để trong product, hãy sửa thành: /../views/product/header.php
        require __DIR__ . '/../views/header.php'; 
        
        // File category.php nằm trong folder category
        require __DIR__ . '/../views/category/category.php';
    }
}
?>