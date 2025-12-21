<?php
require_once __DIR__ . '/../../models/ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    // Xem chi tiết sản phẩm
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Lấy thông tin sản phẩm
        $product = $this->productModel->getById($id);

        if (!$product) {
            die("Sản phẩm không tồn tại!");
        }

        require_once __DIR__ . '/../Views/product/detail.php';
    }
}
?>