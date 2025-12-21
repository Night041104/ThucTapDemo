<?php
require_once __DIR__ . '/../../models/ProductModel.php';

class HomeController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function index() {
        // Lấy toàn bộ sản phẩm để hiển thị demo
        // Hàm getAll này đã có sẵn trong ProductModel của bạn
        $products = $this->productModel->getAll();
        
        // Gọi View
        require_once __DIR__ . '/../Views/home/index.php';
    }
}
?>