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
    // [THUẬT TOÁN] Tạo khoảng giá thông minh
    private function getDynamicPriceRanges($cateId) {
        // 1. Lấy giá sàn và trần thực tế
        $prices = $this->prodModel->getMinMaxPrice($cateId);
        $min = $prices['min'];
        $max = $prices['max'];

        // Nếu danh mục chưa có sản phẩm hoặc tất cả đồng giá
        if ($min === 0 && $max === 0) return [];
        if ($min === $max) return []; 

        $ranges = [];
        
        // Luôn có nút "Dưới X" (X là giá thấp nhất + 1 khoảng nhỏ)
        // Tính bước nhảy (Step): Chia khoảng giá thành 4 phần
        $diff = $max - $min;
        $step = ceil($diff / 4); 

        // Làm tròn bước nhảy cho đẹp (VD: 2.340.000 -> 2.500.000)
        // Logic làm tròn:
        if ($step < 1000000) {
            $step = ceil($step / 100000) * 100000; // Làm tròn 100k
        } else {
            $step = ceil($step / 500000) * 500000; // Làm tròn 500k
        }

        // Tạo khoảng 1: Dưới [Min + Step]
        $milestone1 = $min + $step;
        $ranges["0-$milestone1"] = "Dưới " . $this->formatPriceShort($milestone1);

        // Tạo các khoảng giữa
        $current = $milestone1;
        while ($current < $max) {
            $next = $current + $step;
            if ($next >= $max) break; // Nếu bước tiếp theo vượt quá Max thì dừng để gom vào nút cuối
            
            $key = "$current-$next";
            $label = "Từ " . $this->formatPriceShort($current) . " - " . $this->formatPriceShort($next);
            $ranges[$key] = $label;
            
            $current = $next;
        }

        // Tạo khoảng cuối: Trên [Current]
        $ranges["$current-max"] = "Trên " . $this->formatPriceShort($current);

        return $ranges;
    }

    // Helper: Format giá gọn (2.500.000 -> 2.5 triệu, 500.000 -> 500k)
    private function formatPriceShort($price) {
        if ($price >= 1000000) {
            $val = round($price / 1000000, 1); // 1.5
            return str_replace('.', ',', $val) . " triệu";
        } else {
            return ($price / 1000) . "k";
        }
    }

    // --- CẬP NHẬT HÀM INDEX ---
    public function index() {
        $cateId = $_GET['id'] ?? 0;
        
        $category = $this->cateModel->getById($cateId);
        if (!$category) die("<h3 style='text-align:center; margin-top:50px;'>Danh mục không tồn tại!</h3>");

        $products = $this->prodModel->getProductsByCateForClient($cateId);
        $filterBrands = $this->brandModel->getByCategoryId($cateId);
        
        // [UPDATE 1] Lấy full option thuộc tính
        $filterAttrs  = $this->attrModel->getFiltersByCateForClient($cateId);

        // [UPDATE 2] Tính khoảng giá động thông minh
        $priceRanges = $this->getDynamicPriceRanges($cateId);

        require __DIR__ . '/../views/header.php'; 
        require __DIR__ . '/../views/category/category.php';
    }
    public function filter() {
        $cateId = $_GET['id'] ?? 0;
        
        // Nhận dữ liệu từ AJAX
        $brands = isset($_GET['brands']) ? explode(',', $_GET['brands']) : [];
        $price  = $_GET['price'] ?? '';
        
        // Xử lý thuộc tính: Client gửi attrs[RAM]=8GB,16GB
        $attrs = $_GET['attrs'] ?? [];
        foreach($attrs as $key => $val) {
            $attrs[$key] = explode(',', $val);
        }

        // Gọi Model lọc
        $products = $this->prodModel->getProductsByFilter($cateId, $brands, $price, $attrs);

        // Trả về HTML của danh sách sản phẩm (Partial View)
        // Lưu ý: Không load header/footer ở đây
        require __DIR__ . '/../views/category/product_list.php';
    }
}
?>