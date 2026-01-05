<?php
require_once __DIR__ . '/../../models/BrandModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php'; 

class BrandController {
    private $brandModel;
    private $cateModel;
    private $uploadDir = 'uploads/brands/';
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // 2. Kiểm tra quyền Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
        $this->brandModel = new BrandModel();
        $this->cateModel = new CategoryModel();
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // File: app/admin/controllers/BrandController.php

    public function index() {
        // 1. Lấy tham số
        $keyword = isset($_GET['q']) ? $_GET['q'] : '';
        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 5; 

        // 2. Gọi Model lấy danh sách (đã phân trang)
        $listBrands = $this->brandModel->getAll($keyword, $page, $limit);
        
        // 3. Tính toán phân trang
        $totalRecords = $this->brandModel->countAll($keyword);
        $totalPages   = ceil($totalRecords / $limit);

        // 4. Lấy thống kê (để hiển thị 3 ô card trên cùng)
        // Lưu ý: Đếm lại tổng gốc (không theo keyword) cho ô "Tổng thương hiệu"
        $totalStat = $this->brandModel->countAll(); 
        $hasLogo   = $this->brandModel->countHasLogo();
        $noLogo    = $totalStat - $hasLogo;

        require __DIR__ . '/../views/brand/index.php';
    }

    public function create() {
        $currentData = ['id' => '', 'name' => '', 'logo_url' => ''];
        $selectedCats = []; 
        $allCats = $this->cateModel->getAll(); 
        require __DIR__ . '/../views/brand/form.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $data = $this->brandModel->getById($id);
        
        if ($data) {
            $currentData = $data;
            $selectedCats = $this->brandModel->getCategoryIds($id);
            $allCats = $this->cateModel->getAll();
            require __DIR__ . '/../views/brand/form.php';
        } else {
            // [FIX URL] Về trang danh sách
            header("Location: " . $this->baseUrl . "admin/brand");
            exit;
        }
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $name = trim($_POST['name']);
            $postedCats = isset($_POST['categories']) ? $_POST['categories'] : [];

            $error = null;
            if (empty($name)) {
                $error = "❌ Tên thương hiệu không được để trống!";
            } elseif ($this->brandModel->checkNameExists($name, $id)) {
                $error = "❌ Tên thương hiệu '$name' đã tồn tại!";
            }

            // Logic Upload
            $logoPath = "";
            if ($id) {
                $oldBrand = $this->brandModel->getById($id);
                $logoPath = $oldBrand['logo_url'];
            }

            if (isset($_FILES['logo']) && $_FILES['logo']['name'] != '') {
                $fileName = time() . '_' . basename($_FILES['logo']['name']);
                $targetFile = $this->uploadDir . $fileName;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                    if ($logoPath) {
                        $rootPath = dirname(__DIR__, 2);
                        $delPath = $rootPath . DIRECTORY_SEPARATOR . ltrim($logoPath, '/'); 
                        if (file_exists($delPath)) unlink($delPath);
                    }
                    $logoPath = $targetFile;
                }
            }

            if ($error) {
                $currentData = ['id' => $id, 'name' => $name, 'logo_url' => $logoPath];
                $selectedCats = $postedCats; 
                $allCats = $this->cateModel->getAll();
                $msg = $error;
                require __DIR__ . '/../views/brand/form.php';
                exit;
            }

            if ($id) {
                $this->brandModel->update($id, $name, $logoPath);
                $this->brandModel->updateCategories($id, $postedCats); 
                $msg = "updated";
            } else {
                $newId = $this->brandModel->create($name, $logoPath);
                if ($newId) {
                    $this->brandModel->updateCategories($newId, $postedCats); 
                }
                $msg = "created";
            }

            // [FIX URL] Redirect về danh sách kèm thông báo
            header("Location: " . $this->baseUrl . "admin/brand?msg=$msg");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $count = $this->brandModel->countProducts($id);
            if ($count > 0) {
                $msg = urlencode("❌ Không thể xóa! Thương hiệu này đang gắn với $count sản phẩm.");
                // [FIX URL]
                header("Location: " . $this->baseUrl . "admin/brand?msg=$msg");
                exit;
            }
            $this->brandModel->delete($id);
            $msg = "deleted";
            // [FIX URL]
            header("Location: " . $this->baseUrl . "admin/brand?msg=$msg");
        }
        exit;
    }

    public function deleteImage() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $this->brandModel->removeLogo($id);
            
            // [FIX URL] Redirect về trang sửa
            header("Location: " . $this->baseUrl . "admin/brand/edit?id=$id&msg=updated");
        }
        exit;
    }
}
?>