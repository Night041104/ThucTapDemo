<?php
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/ReviewModel.php';

class ProductController {
    private $prodModel;
    private $cateModel;
    private $reviewModel;

    public function __construct() {
        $this->prodModel = new ProductModel();
        $this->cateModel = new CategoryModel();
        $this->reviewModel = new ReviewModel(); // Khởi tạo nó ở đây

    }

    // URL: index.php?module=client&controller=product&action=detail&id=123
    // URL: index.php?module=client&controller=product&action=detail&id=123
    public function detail() {
        $id = 0;

        // 1. Ưu tiên lấy ID từ Slug
        if (isset($_GET['slug'])) {
            $id = $this->prodModel->getIdBySlug($_GET['slug']);
        } else {
            $id = $_GET['id'] ?? 0;
        }

        // Logic Review
        $this->reviewModel = new ReviewModel();
        $userReview = null;
        if(isset($_SESSION['user'])) {
            $userReview = $this->reviewModel->getUserReview($_SESSION['user']['id'], $id);
        } 
                
        // 2. Lấy thông tin sản phẩm
        $product = $this->prodModel->getById($id);
        if (!$product) die("Sản phẩm không tồn tại!");

        // 3. Lấy Gallery & Breadcrumb
        $gallery = $this->prodModel->getGallery($id);
        $category = $this->cateModel->getById($product['category_id']);

        // 4. XỬ LÝ BIẾN THỂ (LOGIC MỚI CÓ SLUG)
        $masterId = ($product['parent_id'] == 0 || $product['parent_id'] == NULL) ? $product['id'] : $product['parent_id'];
        
        // Lấy dữ liệu map từ DB (đã bao gồm slug nhờ sửa Model)
        $rawMap = $this->prodModel->getFamilyVariantMap($masterId);
        
        $productsMap = [];
        foreach ($rawMap as $row) {
            $productsMap[$row['product_id']][$row['attribute_id']] = $row['attribute_value'];
        }

        $variantGroups = [];
        $currentProductAttrs = $productsMap[$id] ?? []; 

        foreach ($rawMap as $row) {
            $attrName = $row['attribute_name'];
            $val = $row['attribute_value'];
            $attrId = $row['attribute_id'];
            
            if (!isset($variantGroups[$attrName][$val])) {
                $targetId = $row['product_id']; 
                $targetSlug = $row['slug']; // [MỚI] Lấy slug

                // Logic tìm sản phẩm đích (giữ nguyên)
                foreach ($productsMap as $pid => $pAttrs) {
                    if (isset($pAttrs[$attrId]) && $pAttrs[$attrId] == $val) {
                        $targetId = $pid; 
                        
                        // Tìm lại slug cho targetId này nếu nó thay đổi
                        // (Đoạn này tối ưu: loop rawMap để lấy slug của pid)
                        foreach($rawMap as $r2) { if($r2['product_id'] == $pid) { $targetSlug = $r2['slug']; break; } }

                        $matchAll = true;
                        foreach ($currentProductAttrs as $curAttrId => $curVal) {
                            if ($curAttrId != $attrId) { 
                                if (!isset($pAttrs[$curAttrId]) || $pAttrs[$curAttrId] != $curVal) {
                                    $matchAll = false;
                                    break;
                                }
                            }
                        }
                        if ($matchAll) {
                            $targetId = $pid;
                            // Update slug cho targetId chuẩn
                            foreach($rawMap as $r2) { if($r2['product_id'] == $pid) { $targetSlug = $r2['slug']; break; } }
                            break; 
                        }
                    }
                }

                $isActive = (isset($currentProductAttrs[$attrId]) && $currentProductAttrs[$attrId] == $val);

                $variantGroups[$attrName][$val] = [
                    'product_id' => $targetId,
                    'slug'       => $targetSlug, // [MỚI] Truyền slug sang View
                    'active'     => $isActive,
                    'thumbnail'  => $row['thumbnail'] ?? ''
                ];
            }
        }

        // 5. Decode Specs & Reviews
        $specs = json_decode($product['specs_json'], true) ?? [];
        $reviews = $this->reviewModel->getReviewsByProduct($id);
        $reviewStats = $this->reviewModel->getReviewStats($id);

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/product/detail.php';
        require __DIR__ . '/../views/layouts/footer.php';
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
    require __DIR__ . '/../views/layouts/header.php';
    require __DIR__ . '/../views/product/search_results.php';
    }

    


}
?>