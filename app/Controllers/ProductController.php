<?php
class ProductController extends BaseController {
    private $productModel;
    private $attributeModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->attributeModel = new AttributeModel();
        $this->categoryModel = new CategoryModel(); // Cần require model này ở index.php
    }

    // --- PHẦN SINH BIẾN THỂ (Giữ nguyên logic cũ) ---
    public function showGenerator() { 
        $parents = $this->productModel->getParents();
        $attributes = $this->attributeModel->getAllWithOptions();
        $this->view('product/generate_variants', ['parents' => $parents, 'attributes' => $attributes]);
    }
    
    public function generateVariants() { /* ... Code giống phiên bản trước ... */ }

    // --- PHẦN TẠO SẢN PHẨM GỐC (MỚI) ---
    public function create() {
    // 1. Lấy danh sách danh mục để đổ vào Dropdown
    $categories = $this->categoryModel->getAll();
    
    // 2. Lấy ID danh mục nếu người dùng đã chọn (để load Template)
    $cateId = $_GET['cate_id'] ?? 0;
    
    $template = [];
    if ($cateId) {
        $cate = $this->categoryModel->getById($cateId);
        // Giải mã JSON template thành mảng để View hiển thị ra các ô input
        $template = json_decode($cate['spec_template'], true) ?? [];
    }

    // 3. Lấy cấu hình thuộc tính (để biết cái nào là custom name, cái nào fixed)
    $attrConfigs = $this->attributeModel->getAllWithOptions();

    // 4. Gọi View
    $this->view('product/create', [
        'categories' => $categories,
        'selectedCateId' => $cateId,
        'template' => $template,
        'attrConfigs' => $attrConfigs
    ]);
}

    public function store() {
        if (!isset($_POST['btn_save_product'])) return;

        try {
            // 1. Xử lý JSON Specs
            $specsForJson = [];
            $eavData = []; 
            if (isset($_POST['spec_group'])) {
                foreach ($_POST['spec_group'] as $gKey => $groupName) {
                    $groupItems = [];
                    if (isset($_POST['spec_item'][$gKey]['name'])) {
                        foreach ($_POST['spec_item'][$gKey]['name'] as $iKey => $itemName) {
                            $itemName = trim($itemName);
                            if ($itemName === '') continue;
                            
                            $inputType = $_POST['spec_item'][$gKey]['type'][$iKey];
                            $jsonValue = ""; 
                            $filterId = null;

                            $valText = $_POST['spec_item'][$gKey]['value_text'][$iKey] ?? '';
                            $valId   = $_POST['spec_item'][$gKey]['value_id'][$iKey] ?? '';
                            $valCust = $_POST['spec_item'][$gKey]['value_custom'][$iKey] ?? '';

                            if ($inputType == 'text') {
                                $jsonValue = $valText;
                            } elseif ($inputType == 'attribute') {
                                $filterId = $valId;
                                $jsonValue = !empty($valCust) ? $valCust : ""; // Nếu không custom thì để trống hoặc lấy text option từ DB
                                // Logic lấy text option từ DB để lưu vào JSON (như code cũ)
                                // ...
                            }

                            if ($jsonValue !== "" || $filterId) {
                                $groupItems[] = ['name' => $itemName, 'value' => $jsonValue, 'attribute_id' => $_POST['spec_item'][$gKey]['attr_id'][$iKey] ?? null];
                                if ($filterId) {
                                    $eavData[] = [
                                        'attr_id' => $_POST['spec_item'][$gKey]['attr_id'][$iKey], 
                                        'option_id' => $filterId,
                                        'value_custom' => $jsonValue
                                    ]; 
                                }
                            }
                        }
                    }
                    if (!empty($groupItems)) $specsForJson[] = ['group_name' => $groupName, 'items' => $groupItems];
                }
            }

            // 2. Insert Product
            $prodData = [
                'parent_id' => null,
                'name' => $_POST['name'],
                'sku' => $_POST['sku'],
                'slug' => $_POST['sku'],
                'category_id' => $_GET['cate_id'],
                'brand_id' => 1, // Hardcode tạm
                'price' => null,
                'quantity' => 0,
                'specs_json' => json_encode($specsForJson, JSON_UNESCAPED_UNICODE)
            ];
            $newId = $this->productModel->createProduct($prodData);

            // 3. Insert EAV & Image
            foreach ($eavData as $eav) {
                $this->productModel->insertEAV($newId, $eav['attr_id'], $eav['option_id'], $eav['value_custom']);
            }
            
            // Upload ảnh
            if (!empty($_FILES['product_images']['name'][0])) {
                $uploadDir = 'public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                foreach ($_FILES['product_images']['name'] as $i => $name) {
                    if ($_FILES['product_images']['error'][$i] === 0) {
                        $target = $uploadDir . time() . '_' . $name;
                        move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $target);
                        $this->productModel->insertImage($newId, $target);
                    }
                }
            }

            echo "<script>alert('Tạo sản phẩm thành công!'); window.location.href='index.php?act=create_product';</script>";

        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    // --- PHẦN CHI TIẾT SẢN PHẨM (MATRIX) ---
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $product = $this->productModel->getProductById($id);
        if (!$product) die("Không tìm thấy sản phẩm");

        $parentId = $product['parent_id'] ? $product['parent_id'] : $product['id'];
        
        // Logic lấy Specs và Images thừa kế
        $specs = json_decode($product['specs_json'], true);
        if (empty($specs) && $product['parent_id']) {
            $parent = $this->productModel->getProductById($product['parent_id']);
            $specs = json_decode($parent['specs_json'], true);
        }

        $images = $this->productModel->getImages($id);
        if (empty($images) && $product['parent_id']) {
            $images = $this->productModel->getImages($product['parent_id']);
        }
        if (empty($images)) $images = ['https://via.placeholder.com/500'];

        // Logic Matrix
        $siblings = $this->productModel->getSiblings($parentId);
        $matrix = [];
        $variantsInfo = [];
        $currentAttrs = [];

        foreach ($siblings as $row) {
            $pid = $row['p_id'];
            $aid = $row['attribute_id'];
            $oid = $row['option_id'];
            $val = $row['value_custom'] ? $row['value_custom'] : 'Option'; // Cần join lấy tên gốc nếu muốn

            $matrix[$pid][$aid] = $oid;
            $variantsInfo[$aid]['name'] = $row['attr_name'];
            $variantsInfo[$aid]['options'][$oid] = $val;

            if ($pid == $id) $currentAttrs[$aid] = $oid;
        }

        $this->view('product/detail', [
            'product' => $product,
            'specs' => $specs,
            'images' => $images,
            'variantsInfo' => $variantsInfo,
            'currentAttrs' => $currentAttrs,
            'matrix' => $matrix
        ]);
    }
}
?>