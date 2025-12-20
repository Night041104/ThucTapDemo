<?php
require_once __DIR__ . '/../../models/BrandModel.php';
// [MỚI] Cần Model Category để lấy danh sách hiển thị
require_once __DIR__ . '/../../models/CategoryModel.php'; 

class BrandController {
    private $brandModel;
    private $cateModel;
    private $uploadDir = 'uploads/brands/';

    public function __construct() {
        $this->brandModel = new BrandModel();
        $this->cateModel = new CategoryModel();
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function index() {
        $listBrands = $this->brandModel->getAll();
        require __DIR__ . '/../views/brand/index.php';
    }

    public function create() {
        $currentData = ['id' => '', 'name' => '', 'logo_url' => ''];
        $selectedCats = []; // Mảng chứa ID danh mục đã chọn
        $allCats = $this->cateModel->getAll(); // Lấy tất cả danh mục để hiện checkbox
        require __DIR__ . '/../views/brand/form.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $data = $this->brandModel->getById($id);
        
        if ($data) {
            $currentData = $data;
            // [MỚI] Lấy các danh mục đã chọn
            $selectedCats = $this->brandModel->getCategoryIds($id);
            $allCats = $this->cateModel->getAll();
            require __DIR__ . '/../views/brand/form.php';
        } else {
            header("Location: index.php?module=admin&controller=brand&action=index");
            exit;
        }
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $name = trim($_POST['name']);
            // Lấy mảng danh mục từ form
            $postedCats = isset($_POST['categories']) ? $_POST['categories'] : [];

            $error = null;
            if (empty($name)) {
                $error = "❌ Tên thương hiệu không được để trống!";
            } elseif ($this->brandModel->checkNameExists($name, $id)) {
                $error = "❌ Tên thương hiệu '$name' đã tồn tại!";
            }

            // Upload Logo
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
                        // Unlink logic
                        $rootPath = dirname(__DIR__, 2);
                        $delPath = $rootPath . DIRECTORY_SEPARATOR . ltrim($logoPath, '/'); // Fix path
                        if (file_exists($delPath)) unlink($delPath);
                    }
                    $logoPath = $targetFile;
                }
            }

            if ($error) {
                $currentData = ['id' => $id, 'name' => $name, 'logo_url' => $logoPath];
                $selectedCats = $postedCats; // Giữ lại lựa chọn khi lỗi
                $allCats = $this->cateModel->getAll();
                $msg = $error;
                require __DIR__ . '/../views/brand/form.php';
                exit;
            }

            if ($id) {
                $this->brandModel->update($id, $name, $logoPath);
                $this->brandModel->updateCategories($id, $postedCats); // [MỚI]
                $msg = "updated";
            } else {
                $newId = $this->brandModel->create($name, $logoPath);
                if ($newId) {
                    $this->brandModel->updateCategories($newId, $postedCats); // [MỚI]
                }
                $msg = "created";
            }

            header("Location: index.php?module=admin&controller=brand&action=index&msg=$msg");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $count = $this->brandModel->countProducts($id);
            if ($count > 0) {
                $msg = urlencode("❌ Không thể xóa! Thương hiệu này đang gắn với $count sản phẩm.");
                header("Location: index.php?module=admin&controller=brand&action=index&msg=$msg");
                exit;
            }
            $this->brandModel->delete($id);
            $msg = "deleted";
            header("Location: index.php?module=admin&controller=brand&action=index&msg=$msg");
        }
        exit;
    }

    public function deleteImage() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Gọi hàm xử lý trọn gói bên Model
            $this->brandModel->removeLogo($id);
            
            // Redirect về trang sửa
            header("Location: index.php?module=admin&controller=brand&action=edit&id=$id&msg=updated");
        }
        exit;
    }
}
?>