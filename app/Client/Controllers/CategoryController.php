<?php
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

    // --- [THUẬT TOÁN MỚI] CHIA ĐÚNG 5 MỨC GIÁ TRÒN ĐẸP ---
    private function getDynamicPriceRanges($cateId) {
        // 1. Lấy Min/Max thực tế từ DB
        $prices = $this->prodModel->getMinMaxPrice($cateId);
        $minReal = $prices['min'];
        $maxReal = $prices['max'];

        // Nếu không có sản phẩm hoặc đồng giá -> Trả về rỗng
        if ($minReal === 0 && $maxReal === 0) return [];
        if ($minReal === $maxReal) return []; 

        // 2. Tính bước nhảy thô (Raw Step) để chia thành 5 phần
        $diff = $maxReal - $minReal;
        $rawStep = $diff / 5;

        // 3. Làm tròn bước nhảy về con số đẹp (Magic Number)
        $step = $this->roundToNiceStep($rawStep);

        // 4. Xác định điểm bắt đầu (Base)
        // Làm tròn Min xuống để mốc bắt đầu đẹp hơn (Ví dụ Min=13.5tr -> Base=13tr)
        if ($minReal >= 1000000) {
            $base = floor($minReal / 1000000) * 1000000;
        } else {
            $base = floor($minReal / 100000) * 100000;
        }

        $ranges = [];
        $prev = $base; // Điểm mốc trước đó

        // 5. Tạo 5 khoảng giá
        // Loop 4 lần để tạo 4 khoảng đầu, khoảng cuối cùng sẽ là "Trên..."
        for ($i = 1; $i <= 4; $i++) {
            // Mốc tiếp theo = Mốc trước + Bước nhảy
            $current = $base + ($step * $i);
            
            // Xử lý Label hiển thị
            if ($i == 1) {
                // Khoảng 1: Dưới X (0 - X)
                // Lưu ý: Key phải là "0-X" để Model xử lý đúng
                $ranges["0-$current"] = "Dưới " . $this->formatPriceShort($current);
            } else {
                // Các khoảng giữa: Từ A - B
                $ranges["$prev-$current"] = "Từ " . $this->formatPriceShort($prev) . " - " . $this->formatPriceShort($current);
            }
            
            $prev = $current; // Cập nhật mốc trước cho vòng lặp sau
        }

        // Khoảng 5: Trên X (X - max)
        $ranges["$prev-max"] = "Trên " . $this->formatPriceShort($prev);

        return $ranges;
    }

    // Helper: Làm tròn bước nhảy về số đẹp gần nhất
    private function roundToNiceStep($rawStep) {
        // Danh sách các bước nhảy "đẹp" ưu tiên
        $niceSteps = [
            50000, 100000, 200000, 500000,          // < 1 triệu
            1000000, 2000000, 3000000, 5000000,     // < 10 triệu
            10000000, 20000000, 50000000            // > 10 triệu
        ];

        // Tìm số đẹp gần nhất (lớn hơn hoặc bằng rawStep)
        foreach ($niceSteps as $step) {
            if ($step >= $rawStep) return $step;
        }
        
        // Nếu lớn hơn cả danh sách trên, làm tròn thô
        return ceil($rawStep / 1000000) * 1000000;
    }

    // Helper: Format hiển thị (1.5 triệu, 500k)
    private function formatPriceShort($price) {
        if ($price >= 1000000) {
            $val = round($price / 1000000, 1); // VD: 1.5
            // Xóa .0 nếu có (1.0 -> 1)
            if ($val == (int)$val) $val = (int)$val;
            return str_replace('.', ',', $val) . " triệu";
        } else {
            return ($price / 1000) . "k";
        }
    }

    public function index() {
        $cateId = $_GET['id'] ?? 0;
        
        $category = $this->cateModel->getById($cateId);
        if (!$category) die("<h3 style='text-align:center; margin-top:50px;'>Danh mục không tồn tại!</h3>");

        $products = $this->prodModel->getProductsByCateForClient($cateId);
        $filterBrands = $this->brandModel->getByCategoryId($cateId);
        $filterAttrs  = $this->attrModel->getFiltersByCateForClient($cateId);
        
        // Tính khoảng giá động (Mới)
        $priceRanges = $this->getDynamicPriceRanges($cateId);

        require __DIR__ . '/../views/header.php'; 
        require __DIR__ . '/../views/category/category.php';
    }

    public function filter() {
        $cateId = $_GET['id'] ?? 0;
        
        // FIX BUG: Lọc bỏ giá trị rỗng để tránh lỗi SQL IN (0)
        $brandsRaw = isset($_GET['brands']) ? explode(',', $_GET['brands']) : [];
        $brands = array_filter($brandsRaw, function($value) { return $value !== ''; });
        
        $price  = $_GET['price'] ?? '';
        
        $attrs = $_GET['attrs'] ?? [];
        foreach($attrs as $key => $val) {
            $attrs[$key] = explode(',', $val);
        }

        $products = $this->prodModel->getProductsByFilter($cateId, $brands, $price, $attrs);

        require __DIR__ . '/../views/category/product_list.php';
    }
}
?>