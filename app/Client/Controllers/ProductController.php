<?php
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

class ProductController {
    private $prodModel;
    private $cateModel;

    public function __construct() {
        $this->prodModel = new ProductModel();
        $this->cateModel = new CategoryModel();
    }

    // URL: index.php?module=client&controller=product&action=detail&id=123
    // URL: index.php?module=client&controller=product&action=detail&id=123
    public function detail() {
        $id = $_GET['id'] ?? 0;
        
        // 1. Lấy thông tin sản phẩm hiện tại
        $product = $this->prodModel->getById($id);
        if (!$product) die("Sản phẩm không tồn tại!");

        // 2. Lấy Gallery & Breadcrumb
        $gallery = $this->prodModel->getGallery($id);
        $category = $this->cateModel->getById($product['category_id']);

        // 3. XỬ LÝ BIẾN THỂ (LOGIC MỚI)
        $masterId = ($product['parent_id'] == 0 || $product['parent_id'] == NULL) ? $product['id'] : $product['parent_id'];
        
        // Lấy dữ liệu map từ DB
        $rawMap = $this->prodModel->getFamilyVariantMap($masterId);
        
        // A. Cấu trúc lại dữ liệu: [Product_ID => [Attr_ID => Value]]
        // Để biết mỗi sản phẩm có những thuộc tính gì
        $productsMap = [];
        foreach ($rawMap as $row) {
            $productsMap[$row['product_id']][$row['attribute_id']] = $row['attribute_value'];
        }

        // B. Gom nhóm thuộc tính để hiển thị (VD: Màu sắc -> [Đỏ, Xanh...])
        // Cấu trúc: [Attr_Name => [Value => [Link_Product_ID, Active_State]]]
        $variantGroups = [];
        $currentProductAttrs = $productsMap[$id] ?? []; // Thuộc tính của SP đang xem

        foreach ($rawMap as $row) {
            $attrName = $row['attribute_name'];
            $val = $row['attribute_value'];
            $attrId = $row['attribute_id'];
            
            // Nếu giá trị này chưa có trong nhóm thì khởi tạo
            if (!isset($variantGroups[$attrName][$val])) {
                
                // --- LOGIC TÌM SẢN PHẨM ĐÍCH (SMART LINK) ---
                // Mặc định: Tìm chính bản thân sản phẩm của dòng dữ liệu này
                $targetId = $row['product_id']; 

                // Nâng cao: Cố gắng tìm sản phẩm khớp với các thuộc tính CÒN LẠI của sản phẩm đang xem
                // Ví dụ: Đang xem (Đỏ, 128GB). Đang tạo nút "Xanh".
                // -> Cố tìm thằng (Xanh, 128GB). Nếu ko có mới lấy thằng (Xanh, 256GB).
                
                foreach ($productsMap as $pid => $pAttrs) {
                    // Điều kiện 1: Sản phẩm đó phải có giá trị thuộc tính này (VD: Phải là màu Xanh)
                    if (isset($pAttrs[$attrId]) && $pAttrs[$attrId] == $val) {
                        $targetId = $pid; // Tạm chấp nhận ứng viên này
                        
                        // Điều kiện 2 (Hoàn hảo): Khớp hết các thuộc tính khác
                        $matchAll = true;
                        foreach ($currentProductAttrs as $curAttrId => $curVal) {
                            if ($curAttrId != $attrId) { // Bỏ qua thuộc tính đang xét
                                if (!isset($pAttrs[$curAttrId]) || $pAttrs[$curAttrId] != $curVal) {
                                    $matchAll = false;
                                    break;
                                    
                                }
                            }
                        }
                        
                        if ($matchAll) {
                            $targetId = $pid; // Tìm thấy ứng viên hoàn hảo! Chốt luôn.
                            break; 
                        }
                    }
                }
                // --- KẾT THÚC LOGIC TÌM ---

                // ... (Các logic tìm targetId ở trên giữ nguyên) ...

                // Xác định xem nút này có phải là nút đang active (của sản phẩm hiện tại) ko
                $isActive = (isset($currentProductAttrs[$attrId]) && $currentProductAttrs[$attrId] == $val);

                // [CẬP NHẬT] Thêm thumbnail và price vào mảng dữ liệu
                $variantGroups[$attrName][$val] = [
                    'product_id' => $targetId,
                    'active'     => $isActive,
                    'thumbnail'  => $row['thumbnail'] ?? '' // Lấy ảnh từ Model (bạn đã thêm cột này)
                ];
            }
        }

        // 4. Decode Specs hiển thị
        $specs = json_decode($product['specs_json'], true) ?? [];

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/product/detail.php';
    }
    public function search() {
    // Lấy 'keyword' thay vì 'q' cho khớp với header.php
    $keyword = $_GET['keyword'] ?? ''; 
    $selectedCate = (int)($_GET['cate_id'] ?? 0);

    // 1. Lấy toàn bộ kết quả tìm kiếm từ Model
    $allResults = $this->prodModel->searchProducts($keyword);
    // 2. Logic tạo Tab danh mục dựa trên kết quả tìm thấy
    $categoryTabs = [];
    foreach ($allResults as $p) {
        $cId = $p['category_id'];
        if (!isset($categoryTabs[$cId])) {
            $categoryTabs[$cId] = [
                'id' => $cId,
                'name' => $p['cate_name'], // Tên danh mục lấy từ SQL JOIN
                'count' => 0
            ];
        }
        $categoryTabs[$cId]['count']++;
    }

    // 3. Nếu đang chọn 1 Tab cụ thể thì lọc lại danh sách hiển thị
    $displayProducts = $allResults;
    if ($selectedCate > 0) {
        $displayProducts = array_filter($allResults, function($item) use ($selectedCate) {
            return $item['category_id'] == $selectedCate;
        });
    }

    // 4. Load View
    require __DIR__ . '/../views/header.php';
    require __DIR__ . '/../views/product/search_results.php';
}

}
?>