<?php
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/AttributeModel.php';
require_once __DIR__ . '/../../models/BrandModel.php';
require_once __DIR__ . '/../../models/ProductLogModel.php';

class ProductController {
    private $prodModel;
    private $cateModel;
    private $attrModel;
    private $brandModel;
    private $logModel;
    private $uploadDir = 'uploads/products/';
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        // 1. Tính toán Base URL để dùng cho redirect
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // 2. Kiểm tra quyền Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // Redirect về trang đăng nhập Client
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }

        $this->prodModel = new ProductModel();
        $this->cateModel = new CategoryModel();
        $this->attrModel = new AttributeModel();
        $this->brandModel = new BrandModel(); 
        $this->logModel  = new ProductLogModel();
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // =======================================================
    // 1. INDEX
    // =======================================================
    // File: app/admin/controllers/ProductController.php

    public function index() {
        // 1. Lấy tham số từ URL
        $filterMasterId = isset($_GET['master_id']) ? $_GET['master_id'] : 0;
        $filterCateId   = isset($_GET['cate_id'])   ? $_GET['cate_id']   : 0; // [MỚI]
        $keyword        = isset($_GET['q'])         ? $_GET['q']         : '';

        // 2. Gọi Model lấy danh sách sản phẩm (Truyền thêm $filterCateId)
        $products = $this->prodModel->getAll($filterMasterId, $keyword, $filterCateId);
        
        // 3. Lấy dữ liệu cho các bộ lọc
        $masters    = $this->prodModel->getMasters();
        $categories = $this->cateModel->getAll(); // [MỚI] Lấy danh sách danh mục
        
        // 4. Các biến phụ khác (Giữ nguyên)
        $variantIds = $this->prodModel->getVariantAttributeIds();

        require __DIR__ . '/../views/product/index.php';
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->prodModel->deleteWithInheritance($_GET['id']);
        }
        // [FIX URL] Về trang danh sách
        header("Location: " . $this->baseUrl . "admin/product?msg=deleted");
        exit;
    }

    // =======================================================
    // 2. CREATE
    // =======================================================
    public function create() {
        $categories = $this->cateModel->getAll();
        $selectedCateId = $_GET['cate_id'] ?? 0;
        
        // Chỉ lấy brands thuộc danh mục đã chọn
        $brands = [];
        if ($selectedCateId) {
            $brands = $this->brandModel->getByCategoryId($selectedCateId);
        }
        
        $template = [];
        if ($selectedCateId) {
            $cate = $this->cateModel->getById($selectedCateId);
            if ($cate && $cate['spec_template']) {
                $template = json_decode($cate['spec_template'], true) ?? [];
            }
        }
        
        $attrs = $this->attrModel->getAll();
        $attrConfigs = [];
        foreach($attrs as $a) $attrConfigs[$a['id']] = $a['is_customizable'];
        $allAttributeOptions = $this->attrModel->getAllOptionsGrouped();
        $variantIds = $this->prodModel->getVariantAttributeIds();
        
        require __DIR__ . '/../views/product/form.php';
    }

    public function store() {
        if (isset($_POST['btn_save_product'])) {
            $nameRaw = trim($_POST['name']);
            // Validate
            $error =  null;
            if(empty($nameRaw)) {
                $error = "❌ Tên sản phẩm không được để trống!";
            } elseif (empty($_FILES['thumbnail']['name'])) {
                $error = "❌ Thiếu ảnh Thumbnail";
            } elseif (empty($_FILES['gallery']['name'][0])){
                $error = "❌ Bắt buộc phải có ít nhất một ảnh Gallery";
            }
            
            if($error){
                // [FIX URL] Redirect lại trang Create kèm lỗi
                header("Location: " . $this->baseUrl . "admin/product/create?cate_id=".$_POST['cate_id']."&msg=".urlencode($error));
                exit;
            }

            // 1. Slug & SKU
            $baseSlug = $this->prodModel->createSlug($nameRaw);
            $finalSlug = $baseSlug;
            if ($this->prodModel->checkSlugExists($finalSlug)) {
                $finalSlug .= "-" . strtolower(substr(md5(uniqid()), 0, 5));
            }
            $sku = 'SP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // 2. Upload Thumbnail
            $thumbnailPath = "";
            if (!empty($_FILES['thumbnail']['name'])) {
                $thumbnailPath = $this->processUpload($_FILES['thumbnail'], $finalSlug, 'thumb');
            }

            // 3. Xử lý Specs
            $specsData = $this->helperProcessSpecs($_POST);
            
            if (isset($specsData['error'])) {
                if ($thumbnailPath && file_exists($thumbnailPath)) unlink($thumbnailPath); 
                header("Location: " . $this->baseUrl . "admin/product/create?msg=" . urlencode("❌ " . $specsData['error']));
                exit;
            }

            // 4. Create Product
            $data = [
                'name' => $nameRaw, 'sku' => $sku, 'slug' => $finalSlug,
                'category_id' => $_POST['cate_id'] ?? 0, 'brand_id' => $_POST['brand_id'],
                'thumbnail' => $thumbnailPath, 'specs_json' => json_encode($specsData['json'], JSON_UNESCAPED_UNICODE),
                'price' => str_replace([',','.'], '', $_POST['price']),
                'market_price' => str_replace([',','.'], '', $_POST['market_price']),
                'quantity' => $_POST['quantity'], 'status' => $_POST['status'],
                'parent_id' => 0
            ];
            
            $newId = $this->prodModel->create($data);

            if ($newId) {
                foreach ($specsData['eav'] as $eav) {
                    $this->prodModel->addAttributeValue($newId, $eav['attr_id'], $eav['opt_id'], $eav['val']);
                }
                $this->helperUploadGallery($newId, $finalSlug);

                // [FIX URL] Về trang danh sách
                header("Location: " . $this->baseUrl . "admin/product?msg=created");
                exit;
            }
        }
    }

    // =======================================================
    // 3. EDIT
    // =======================================================
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $rowProd = $this->prodModel->getById($id);
        if (!$rowProd) die("Không tìm thấy sản phẩm");

        $categories = $this->cateModel->getAll();
        $brands = $this->brandModel->getByCategoryId($rowProd['category_id']);
        $gallery = $this->prodModel->getGallery($id);
        
        $cate = $this->cateModel->getById($rowProd['category_id']);
        $catTemplate = ($cate && $cate['spec_template']) ? json_decode($cate['spec_template'], true) : [];

        $attrs = $this->attrModel->getAll();
        $attrConfigs = [];
        foreach($attrs as $a) $attrConfigs[$a['id']] = $a['is_customizable'];
        $allAttributeOptions = $this->attrModel->getAllOptionsGrouped();
        $currentSpecs = json_decode($rowProd['specs_json'], true) ?? [];

        $eavRaw = $this->prodModel->getAttributeValues($id);
        $selectedOptions = [];
        foreach($eavRaw as $e) {
            $selectedOptions[$e['attribute_id']] = $e['option_id'];
        }
        $variantIds = $this->prodModel->getVariantAttributeIds();
        
        require __DIR__ . '/../views/product/form.php';
    }

    public function update() {
        if (isset($_POST['btn_update'])) {
            $id = $_GET['id'];
            $oldProd = $this->prodModel->getById($id);

            $nameRaw = trim($_POST['name']);
            $finalSlug = $oldProd['slug'];
            
            if ($nameRaw !== $oldProd['name']) {
                $baseSlug = $this->prodModel->createSlug($nameRaw);
                $finalSlug = $baseSlug;
                if ($this->prodModel->checkSlugExists($finalSlug, $id)) {
                    $finalSlug .= "-" . rand(1000,9999);
                }
            }

            $thumbnailPath = $oldProd['thumbnail'];
            if (!empty($_FILES['thumbnail']['name'])) {
                $thumbnailPath = $this->processUpload($_FILES['thumbnail'], $finalSlug, 'thumb');
            }

            $specsData = $this->helperProcessSpecs($_POST);

            if (isset($specsData['error'])) {
                if (!empty($_FILES['thumbnail']['name']) && file_exists($thumbnailPath)) {
                    unlink($thumbnailPath); 
                }
                // [FIX URL] Redirect về trang Edit
                header("Location: " . $this->baseUrl . "admin/product/edit?id=$id&msg=" . urlencode("❌ " . $specsData['error']));
                exit;
            }

            $data = [
                'name' => $nameRaw, 'slug' => $finalSlug,
                'category_id' => $_POST['cate_id'], 'brand_id' => $_POST['brand_id'],
                'thumbnail' => $thumbnailPath, 'specs_json' => json_encode($specsData['json'], JSON_UNESCAPED_UNICODE),
                'price' => str_replace([',','.'], '', $_POST['price']),
                'market_price' => str_replace([',','.'], '', $_POST['market_price']),
                'quantity' => $_POST['quantity'], 'status' => $_POST['status']
            ];
            
            $this->prodModel->update($id, $data);

            if (!empty($_FILES['thumbnail']['name']) && !empty($oldProd['thumbnail'])) {
                $this->prodModel->cleanupFile($oldProd['thumbnail']);
            }

            $this->prodModel->clearAttributes($id);
            foreach ($specsData['eav'] as $eav) {
                $this->prodModel->addAttributeValue($id, $eav['attr_id'], $eav['opt_id'], $eav['val']);
            }

            $this->helperUploadGallery($id, $finalSlug);
            $this->prodModel->syncFamilyData($id, $oldProd['parent_id'], $data, $specsData['eav']);
            $this->logModel->logHistory($id, $oldProd, $data);
            
            // [FIX URL]
            header("Location: " . $this->baseUrl . "admin/product/edit?id=$id&msg=updated");
            exit;
        }
    }

    public function deleteImage() {
        if (isset($_GET['del_img'])) {
            $this->prodModel->deleteImage($_GET['del_img']);
            $id = $_GET['id'];
            // [FIX URL]
            header("Location: " . $this->baseUrl . "admin/product/edit?id=$id");
            exit;
        }
    }

    // =======================================================
    // 4. CLONE
    // =======================================================
    public function clone() {
        if (isset($_GET['id'])) {
            $sourceId = $_GET['id'];
            $source = $this->prodModel->getById($sourceId);

            if ($source) {
                $masterId = ($source['parent_id'] == 0 || $source['parent_id'] == NULL) ? $source['id'] : $source['parent_id'];
                
                $newName = $source['name'] . " (Copy)";
                $newSku = $source['sku'] . "-" . rand(100,999);
                $newSlug = $this->prodModel->createSlug($newName) . "-" . rand(1000,9999);

                $data = [
                    'parent_id' => $masterId, 
                    'name' => $newName, 
                    'sku' => $newSku, 
                    'slug' => $newSlug,
                    'category_id' => $source['category_id'], 
                    'brand_id' => $source['brand_id'],
                    'thumbnail' => $source['thumbnail'], 
                    'specs_json' => $source['specs_json'],
                    'price' => (int)$source['price'], 
                    'market_price' => (int)$source['market_price'],
                    'quantity' => 0, 
                    'status' => 0
                ];

                $newId = $this->prodModel->create($data);
                
                if ($newId) {
                    $eavs = $this->prodModel->getAttributeValues($sourceId);
                    foreach($eavs as $eav) {
                        $this->prodModel->addAttributeValue($newId, $eav['attribute_id'], $eav['option_id'], $eav['value_custom']);
                    }
                    
                    $rsGal = $this->prodModel->getGallery($sourceId);
                    foreach($rsGal as $img) {
                        $this->prodModel->addImage($newId, $img['image_url']);
                    }

                    // [FIX URL]
                    header("Location: " . $this->baseUrl . "admin/product/edit?id=$newId&msg=cloned");
                    exit;
                }
            }
        }
    }

    // =======================================================
    // HELPERS (Giữ nguyên logic cũ)
    // =======================================================

    private function processUpload($file, $slug, $suffix = '') {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFileName = $slug . ($suffix ? "-$suffix" : "") . "-" . time() . rand(10,99) . "." . $extension;
        $targetPath = $this->uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath;
        }
        return "";
    }

    private function helperUploadGallery($prodId, $slug) {
        if (isset($_FILES['gallery']['name'])) {
            $count = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['gallery']['name'][$i] != '') {
                    $fileItem = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i]
                    ];
                    $path = $this->processUpload($fileItem, $slug, "gal-$i");
                    
                    if ($path) {
                        $this->prodModel->addImage($prodId, $path);
                    }
                }
            }
        }
    }

    private function helperProcessSpecs($postData) {
        $specsForJson = []; 
        $eavData = [];
        
        // (Logic xử lý Specs của bạn giữ nguyên, không thay đổi)
        if (isset($postData['spec_group'])) {
            foreach ($postData['spec_group'] as $gKey => $groupName) {
                $groupItems = [];
                if (isset($postData['spec_item'][$gKey]['name'])) {
                    foreach ($postData['spec_item'][$gKey]['name'] as $iKey => $itemName) {
                        $itemName = trim($itemName);
                        if ($itemName === '') continue;
                        
                        $type = $postData['spec_item'][$gKey]['type'][$iKey];
                        $valId = $postData['spec_item'][$gKey]['value_id'][$iKey] ?? '';
                        $valCust = $postData['spec_item'][$gKey]['value_custom'][$iKey] ?? '';
                        $valText = $postData['spec_item'][$gKey]['value_text'][$iKey] ?? '';
                        $attrId = $postData['spec_item'][$gKey]['attr_id'][$iKey] ?? 0;

                        $jsonValue = "";
                        
                        if ($type == 'text') {
                            $jsonValue = $valText;
                        } 
                        elseif ($type == 'attribute') {
                            if ($valCust !== '') {
                                $jsonValue = $valCust;
                            } elseif ($valId) {
                                // Kết nối DB thủ công để lấy giá trị option nếu cần
                                $conn = Database::getInstance()->conn;
                                $rO = mysqli_fetch_assoc(mysqli_query($conn, "SELECT value FROM attribute_options WHERE id=".(int)$valId));
                                if($rO) $jsonValue = $rO['value'];
                            }
                        }

                        if ($jsonValue !== "" || $valId) {
                            $groupItems[] = [
                                'name' => $itemName, 'value' => $jsonValue, 
                                'type' => $type, 'attr_id' => $attrId
                            ];
                            if ($type == 'attribute' && ($valId || $jsonValue)) {
                                $eavData[] = ['attr_id' => $attrId, 'opt_id' => $valId, 'val' => $jsonValue];
                            }
                        }
                    }
                }
                if (!empty($groupItems)) $specsForJson[] = ['group_name' => $groupName, 'items' => $groupItems];
            }
        }
        return ['json' => $specsForJson, 'eav' => $eavData];
    }

    public function history() {
        $masterId = isset($_GET['master_id']) ? (int)$_GET['master_id'] : 0;
        
        $masterProd = $this->prodModel->getById($masterId);
        $logs = $this->logModel->getLogsByFamily($masterId);

        $brands = $this->brandModel->getAll(); 
        $cates  = $this->cateModel->getAll();

        $brandsMap = [];
        if ($brands) foreach($brands as $b) $brandsMap[$b['id']] = $b['name'];

        $catesMap = [];
        if ($cates) foreach($cates as $c) $catesMap[$c['id']] = $c['name'];

        $variantIds = $this->prodModel->getVariantAttributeIds();

        require __DIR__ . '/../views/product/history.php';
    }
}
?>