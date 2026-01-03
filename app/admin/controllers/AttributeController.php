<?php
require_once __DIR__ . '/../../models/AttributeModel.php';

class AttributeController {
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
            // [FIX URL] Chuyển về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
        $this->attrModel = new AttributeModel();
    }

    // 1. Hiển thị danh sách (Index)
    public function index() {
        $listAttrs = $this->attrModel->getAll();
        require __DIR__ . '/../views/attribute/index.php';
    }

    // 2. Form Tạo mới (Create)
    public function create() {
        // Khởi tạo dữ liệu rỗng
        $currentData = [
            'id' => '', 'code' => '', 'name' => '', 
            'options_str' => '', 'is_customizable' => 0, 'is_variant' => 0
        ];
        require __DIR__ . '/../views/attribute/form.php';
    }

    // 3. Form Sửa (Edit)
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $data = $this->attrModel->getById($id);
        
        if ($data) {
            $currentData = $data; // Model đã gộp options thành chuỗi options_str
            require __DIR__ . '/../views/attribute/form.php';
        } else {
            // [FIX URL] Về danh sách
            header("Location: " . $this->baseUrl . "admin/attribute");
            exit;
        }
    }

    // 4. Xử lý Lưu (Save - dùng chung cho Create & Update)
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $code = trim($_POST['code']);
            $name = trim($_POST['name']);
            $optionsStr = $_POST['options'];
            $isCustom = isset($_POST['is_customizable']) ? 1 : 0;
            $isVariant = isset($_POST['is_variant']) ? 1 : 0;

            // --- Validation ---
            $error = null;
            if (empty($code) || empty($name)) {
                $error = "❌ Mã và Tên thuộc tính không được để trống!";
            } elseif ($this->attrModel->checkCodeExists($code, $id)) {
                $error = "❌ Mã thuộc tính '$code' đã tồn tại!";
            }

            // Chuyển chuỗi Options thành mảng (cắt bởi dấu phẩy)
            $optionsArr = array_map('trim', explode(',', $optionsStr));

            if ($error) {
                // Sticky Form: Nếu lỗi, load lại form với dữ liệu cũ + thông báo lỗi
                $currentData = [
                    'id' => $id, 'code' => $code, 'name' => $name, 
                    'options_str' => $optionsStr, 
                    'is_customizable' => $isCustom, 'is_variant' => $isVariant
                ];
                $msg = $error;
                require __DIR__ . '/../views/attribute/form.php';
                exit;
            }

            // Gọi Model để lưu
            if ($id) {
                $this->attrModel->update($id, $code, $name, $isCustom, $isVariant, $optionsArr);
                $msg = "updated";
            } else {
                $this->attrModel->create($code, $name, $isCustom, $isVariant, $optionsArr);
                $msg = "created";
            }

            // [FIX URL] Chuyển hướng kèm thông báo
            header("Location: " . $this->baseUrl . "admin/attribute?msg=$msg");
            exit;
        }
    }

    // 5. Xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $this->attrModel->delete($_GET['id']);
            $msg = "deleted";
        }
        // [FIX URL]
        header("Location: " . $this->baseUrl . "admin/attribute?msg=deleted");
        exit;
    }
}
?>