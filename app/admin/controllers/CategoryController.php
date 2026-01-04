<?php
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/AttributeModel.php'; 

class CategoryController {
    private $cateModel;
    private $attrModel;
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // 2. Kiểm tra quyền Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL]
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
        $this->cateModel = new CategoryModel();
        $this->attrModel = new AttributeModel();
    }

    public function index() {
        $listCates = $this->cateModel->getAll();
        require __DIR__ . '/../views/category/index.php';
    }

    // CREATE: Gọi form
    public function create() {
        $currentData = ['id' => '', 'name' => '', 'slug' => '', 'template' => []];
        $attrs = $this->attrModel->getAll();
        require __DIR__ . '/../views/category/form.php';
    }

    // EDIT: Gọi form và đổ dữ liệu
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $cate = $this->cateModel->getById($id);
        
        if ($cate) {
            $currentData = [
                'id' => $cate['id'], 
                'name' => $cate['name'], 
                'slug' => $cate['slug'],
                'template' => json_decode($cate['spec_template'], true) ?? []
            ];
            $attrs = $this->attrModel->getAll();
            require __DIR__ . '/../views/category/form.php';
        } else {
            // [FIX URL] Về danh sách
            header("Location: " . $this->baseUrl . "admin/category");
            exit;
        }
    }

    // SAVE: Xử lý Thêm hoặc Sửa
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $name = trim($_POST['name']);
            $slug = trim($_POST['slug'] ?? '');
            
            // --- 1. VALIDATION CƠ BẢN ---
            $error = null;
            if (empty($name)) {
                $error = "❌ Tên danh mục không được để trống!";
            } 
            // [THÊM MỚI] Kiểm tra xem tên có phải là số không
            elseif (is_numeric($name)) {
                $error = "❌ Tên danh mục không được là số!";
            }
            // Kết thúc thêm mới
            elseif ($this->cateModel->checkNameExists($name, $id)) {
                $error = "❌ Tên danh mục '$name' đã tồn tại!";
            }

            // --- 2. VALIDATION TEMPLATE (Check trùng lặp) ---
            $template = []; 
            if (!$error && isset($_POST['groups']) && is_array($_POST['groups'])) {
                $seenGroups = []; 

                foreach ($_POST['groups'] as $gIndex => $groupName) {
                    $groupName = trim($groupName);
                    if ($groupName === '') continue;

                    $groupNameLower = mb_strtolower($groupName, 'UTF-8');
                    if (in_array($groupNameLower, $seenGroups)) {
                        $error = "❌ Tên nhóm thông số '$groupName' bị lặp lại!"; break;
                    }
                    $seenGroups[] = $groupNameLower;

                    $items = [];
                    $seenItems = []; 

                    if (isset($_POST['items'][$gIndex]['name'])) {
                        foreach ($_POST['items'][$gIndex]['name'] as $iIndex => $itemName) {
                            $itemName = trim($itemName);
                            if ($itemName === '') continue;

                            $itemNameLower = mb_strtolower($itemName, 'UTF-8');
                            if (in_array($itemNameLower, $seenItems)) {
                                $error = "❌ Trong nhóm '$groupName', thông số '$itemName' bị nhập 2 lần!"; break 2;
                            }
                            $seenItems[] = $itemNameLower;

                            $type = $_POST['items'][$gIndex]['type'][$iIndex];
                            $attrId = $_POST['items'][$gIndex]['attr_id'][$iIndex] ?? null;
                            
                            $itemData = ['name' => $itemName, 'type' => $type];
                            if ($type == 'attribute' && $attrId) $itemData['attribute_id'] = (int)$attrId;
                            
                            $items[] = $itemData;
                        }
                    }
                    $template[] = ['group_name' => $groupName, 'items' => $items];
                }
            }

            // --- 3. XỬ LÝ KẾT QUẢ ---
            if ($error) {
                // [STICKY FORM]
                $attrs = $this->attrModel->getAll();
                $currentData = [
                    'id' => $id,
                    'name' => $name,
                    'slug' => $slug,
                    'template' => $template 
                ];
                $msg = $error; 
                require __DIR__ . '/../views/category/form.php';
                exit;
            }

            // Lưu DB
            $jsonTemplate = json_encode($template, JSON_UNESCAPED_UNICODE);

            if ($id) {
                $this->cateModel->update($id, $name, $slug, $jsonTemplate);
                $msg = "updated";
            } else {
                $this->cateModel->create($name, $jsonTemplate);
                $msg = "created";
            }

            // [FIX URL] Về danh sách
            header("Location: " . $this->baseUrl . "admin/category?msg=$msg");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // [AN TOÀN]
            $count = $this->cateModel->countProducts($id);
            if ($count > 0) {
                // [FIX URL]
                header("Location: " . $this->baseUrl . "admin/category?msg=" . urlencode("❌ Không thể xóa! Danh mục này đang chứa $count sản phẩm."));
                exit;
            }

            $this->cateModel->delete($id);
            // [FIX URL]
            header("Location: " . $this->baseUrl . "admin/category?msg=deleted");
        } else {
            // [FIX URL]
            header("Location: " . $this->baseUrl . "admin/category");
        }
        exit;
    }
}
?>