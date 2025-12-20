<?php
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/AttributeModel.php';
require_once __DIR__ . '/../../models/BrandModel.php';

class ProductController {
    private $prodModel;
    private $cateModel;
    private $attrModel;
    private $brandModel;
    private $uploadDir = 'uploads/products/';

    public function __construct() {
        $this->prodModel = new ProductModel();
        $this->cateModel = new CategoryModel();
        $this->attrModel = new AttributeModel();
        $this->brandModel = new BrandModel(); 
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // =======================================================
    // 1. INDEX
    // =======================================================
    public function index() {
        $filterMasterId = isset($_GET['master_id']) ? $_GET['master_id'] : 0;
        $keyword = isset($_GET['q']) ? $_GET['q'] : '';

        $products = $this->prodModel->getAll($filterMasterId, $keyword);
        $masters  = $this->prodModel->getMasters();

        require __DIR__ . '/../views/product/index.php';
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->prodModel->deleteWithInheritance($_GET['id']);
        }
        header("Location: index.php?module=admin&controller=product&action=index&msg=deleted");
        exit;
    }

    // =======================================================
    // 2. CREATE
    // =======================================================
    public function create() {
        $categories = $this->cateModel->getAll();
        $selectedCateId = $_GET['cate_id'] ?? 0;
        
        // [THAY ĐỔI] Chỉ lấy brands thuộc danh mục đã chọn
        $brands = [];
        if ($selectedCateId) {
            $brands = $this->brandModel->getByCategoryId($selectedCateId);
        } else {
            // Nếu chưa chọn danh mục thì ko hiện brand nào (hoặc hiện hết tùy logic của bạn)
            // Tốt nhất là để rỗng để ép người dùng chọn danh mục trước
            $brands = []; 
        }
        
        $selectedCateId = $_GET['cate_id'] ?? 0;
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
        require __DIR__ . '/../views/product/form.php';
    }

    public function store() {
        if (isset($_POST['btn_save_product'])) {
            $nameRaw = trim($_POST['name']);
            //Validate  dữ liệu đầu vào
            $error =  null;
            if(empty($nameRaw)) {
                $error = "❌ Tên sản phẩm không được để trống!";
            } elseif (empty($_FILES['thumbnail']['name'])) {
                $error = "❌ Thiếu ảnh Thumbnail";
            } elseif  (empty($_FILES['gallery']['name'][0])){
                $error = "❌ Bắt buộc phải có ít nhất một ảnh Gallery";
            }
            if($error){
                header("Location: index.php?module=admin&controller=product&action=create&cate_id=".$_POST['cate_id']."&msg=".urlencode($error));
                exit;
            }
            // 1. Slug & SKU
            $baseSlug = $this->prodModel->createSlug($nameRaw);
            $finalSlug = $baseSlug;
            if ($this->prodModel->checkSlugExists($finalSlug)) {
                $finalSlug .= "-" . strtolower(substr(md5(uniqid()), 0, 5));
            }
            $sku = 'SP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // 2. Upload Thumbnail (Dùng hàm processUpload cho gọn)
            $thumbnailPath = "";
            if (!empty($_FILES['thumbnail']['name'])) {
                $thumbnailPath = $this->processUpload($_FILES['thumbnail'], $finalSlug, 'thumb');
            }

            // 3. Xử lý Specs (Validation nằm trong này)
            $specsData = $this->helperProcessSpecs($_POST);
            
            // --> KIỂM TRA LỖI TRÙNG <--
            if (isset($specsData['error'])) {
                if ($thumbnailPath && file_exists($thumbnailPath)) unlink($thumbnailPath); // Xóa ảnh rác
                header("Location: index.php?module=admin&controller=product&action=create&msg=" . urlencode("❌ " . $specsData['error']));
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
                // Save EAV
                foreach ($specsData['eav'] as $eav) {
                    $this->prodModel->addAttributeValue($newId, $eav['attr_id'], $eav['opt_id'], $eav['val']);
                }
                // Upload Gallery
                $this->helperUploadGallery($newId, $finalSlug);

                header("Location: index.php?module=admin&controller=product&action=index&msg=created");
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
        $gallery    = $this->prodModel->getGallery($id);
        
        $cate = $this->cateModel->getById($rowProd['category_id']);
        $catTemplate = ($cate && $cate['spec_template']) ? json_decode($cate['spec_template'], true) : [];

        $attrs = $this->attrModel->getAll();
        $attrConfigs = [];
        foreach($attrs as $a) $attrConfigs[$a['id']] = $a['is_customizable'];
        $allAttributeOptions = $this->attrModel->getAllOptionsGrouped();
        $currentSpecs = json_decode($rowProd['specs_json'], true) ?? [];

        require __DIR__ . '/../views/product/form.php';
    }

    public function update() {
        if (isset($_POST['btn_update'])) {
            $id = $_GET['id'];
            $oldProd = $this->prodModel->getById($id);

            $nameRaw = trim($_POST['name']);
            // //Validate  dữ liệu đầu vào
            // $error =  null;
            // if(empty($nameRaw)) {
            //     $error = "❌ Tên sản phẩm không được để trống!";
            // } elseif (empty($_FILES['thumbnail']['name'])) {
            //     $error = "❌ Thiếu ảnh Thumbnail";
            // } elseif  (empty($_FILES['gallery']['name'][0])){
            //     $error = "❌ Bắt buộc phải có ít nhất một ảnh Gallery";
            // }
            // if($error){
            //     header("Location: index.php?module=admin&controller=product&action=create&cate_id=".$_POST['cate_id']."&msg=".urlencode($error));
            //     exit;
            // }
            $finalSlug = $oldProd['slug'];
            
            if ($nameRaw !== $oldProd['name']) {
                $baseSlug = $this->prodModel->createSlug($nameRaw);
                $finalSlug = $baseSlug;
                if ($this->prodModel->checkSlugExists($finalSlug, $id)) {
                    $finalSlug .= "-" . rand(1000,9999);
                }
            }

            // Upload Thumbnail (Xóa cũ nếu có mới)
            $thumbnailPath = $oldProd['thumbnail'];
            if (!empty($_FILES['thumbnail']['name'])) {
                if (!empty($oldProd['thumbnail']) && file_exists($oldProd['thumbnail'])) {
                    unlink($oldProd['thumbnail']);
                }
                $thumbnailPath = $this->processUpload($_FILES['thumbnail'], $finalSlug, 'thumb');
            }

            // Specs Processing
            $specsData = $this->helperProcessSpecs($_POST);

            // --> KIỂM TRA LỖI TRÙNG <--
            if (isset($specsData['error'])) {
                // Nếu lỡ up ảnh mới thì xóa đi
                if (!empty($_FILES['thumbnail']['name']) && file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
                header("Location: index.php?module=admin&controller=product&action=edit&id=$id&msg=" . urlencode("❌ " . $specsData['error']));
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

            $this->prodModel->clearAttributes($id);
            foreach ($specsData['eav'] as $eav) {
                $this->prodModel->addAttributeValue($id, $eav['attr_id'], $eav['opt_id'], $eav['val']);
            }

            $this->helperUploadGallery($id, $finalSlug);
            $this->prodModel->syncFamilyData($id, $oldProd['parent_id'], $data);

            header("Location: index.php?module=admin&controller=product&action=edit&id=$id&msg=updated");
            exit;
        }
    }

    public function deleteImage() {
        if (isset($_GET['del_img'])) {
            $this->prodModel->deleteImage($_GET['del_img']);
            $id = $_GET['id'];
            header("Location: index.php?module=admin&controller=product&action=edit&id=$id");
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
                
                $newName = $source['name'];
                $newSku = $source['sku'] . "-" . rand(100,999);
                $newSlug = $this->prodModel->createSlug($newName) . "-" . rand(1000,9999);

                $data = [
                    'parent_id' => $masterId, 'name' => $newName, 'sku' => $newSku, 'slug' => $newSlug,
                    'category_id' => $source['category_id'], 'brand_id' => $source['brand_id'],
                    'thumbnail' => $source['thumbnail'], 'specs_json' => $source['specs_json'],
                    'price' => (int)$source['price'], 'market_price' => (int)$source['market_price'],
                    'quantity' => 0, 'status' => 0
                ];

                $newId = $this->prodModel->create($data);
                
                if ($newId) {
                    // Copy EAV
                    $conn = Database::getInstance()->conn;
                    $rsEav = mysqli_query($conn, "SELECT * FROM product_attribute_values WHERE product_id = $sourceId");
                    while($eav = mysqli_fetch_assoc($rsEav)) {
                        $this->prodModel->addAttributeValue($newId, $eav['attribute_id'], $eav['option_id'], $eav['value_custom']);
                    }
                    
                    // Copy Gallery Link
                    $rsGal = $this->prodModel->getGallery($sourceId);
                    foreach($rsGal as $img) {
                        $this->prodModel->addImage($newId, $img['image_url']);
                    }

                    header("Location: index.php?module=admin&controller=product&action=edit&id=$newId&msg=cloned");
                    exit;
                }
            }
        }
    }

    // =======================================================
    // HELPERS
    // =======================================================

    // [MỚI] Hàm xử lý upload tập trung (để tránh lặp code)
    private function processUpload($file, $slug, $suffix = '') {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFileName = $slug . ($suffix ? "-$suffix" : "") . "-" . time() . rand(10,99) . "." . $extension;
        $targetPath = $this->uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath;
        }
        return "";
    }

    // Helper upload Gallery (Sử dụng processUpload)
    private function helperUploadGallery($prodId, $slug) {
        if (isset($_FILES['gallery']['name'])) {
            $count = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['gallery']['name'][$i] != '') {
                    // Giả lập mảng file đơn
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

    // [SỬA LỖI] Helper xử lý Specs: Check trùng cả Tên và ID
    private function helperProcessSpecs($postData) {
        $specsForJson = []; 
        $eavData = [];
        
        $seenAttributes = []; // Check trùng ID Attribute
        $seenNames = [];      // Check trùng Tên hiển thị (Text)

        if (isset($postData['spec_group'])) {
            foreach ($postData['spec_group'] as $gKey => $groupName) {
                $groupItems = [];
                if (isset($postData['spec_item'][$gKey]['name'])) {
                    foreach ($postData['spec_item'][$gKey]['name'] as $iKey => $itemName) {
                        $itemName = trim($itemName);
                        if ($itemName === '') continue;
                        
                        // 1. Kiểm tra trùng TÊN (Cho cả Text và Attribute)
                        $nameLower = mb_strtolower($itemName, 'UTF-8');
                        if (in_array($nameLower, $seenNames)) {
                            return ['error' => "Lỗi: Tên thông số '$itemName' bị nhập trùng!"];
                        }
                        $seenNames[] = $nameLower;

                        $type = $postData['spec_item'][$gKey]['type'][$iKey];
                        $valId = $postData['spec_item'][$gKey]['value_id'][$iKey] ?? '';
                        $valCust = $postData['spec_item'][$gKey]['value_custom'][$iKey] ?? '';
                        $valText = $postData['spec_item'][$gKey]['value_text'][$iKey] ?? '';
                        $attrId = $postData['spec_item'][$gKey]['attr_id'][$iKey] ?? 0;

                        // 2. Kiểm tra trùng ID (Cho Attribute)
                        if ($type == 'attribute' && $attrId) {
                            if (in_array($attrId, $seenAttributes)) {
                                return ['error' => "Lỗi: Bạn đang chọn trùng thuộc tính (ID: $attrId)."];
                            }
                            $seenAttributes[] = $attrId;
                        }

                        $jsonValue = "";
                        if ($type == 'text') $jsonValue = $valText;
                        elseif ($type == 'attribute') {
                            if ($valCust !== '') $jsonValue = $valCust;
                            elseif ($valId) {
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
                            if ($type == 'attribute' && $valId) {
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
}
?>