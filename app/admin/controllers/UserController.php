<?php
require_once __DIR__ . '/../../models/UserModel.php';

class UserController {
    private $userModel;
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        $this->userModel = new UserModel();

        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // KIỂM TRA QUYỀN ADMIN
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
    }

    // 1. DANH SÁCH USER
    public function index() {
        $keyword = $_GET['keyword'] ?? '';
        $role    = $_GET['role'] ?? '';
        $status  = $_GET['status'] ?? '';

        $users = $this->userModel->getAllUsers($keyword, $role, $status);
        
        require_once __DIR__ . '/../Views/users/index.php';
    }

    // 2. SỬA USER (Phân quyền, Kích hoạt)
    public function edit() {
        $id = $_GET['id'] ?? '';
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            die("User không tồn tại!");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role_id = $_POST['role_id'];
            $is_verified = $_POST['is_verified'];

            // Không cho phép tự hạ quyền chính mình
            if ($id == $_SESSION['user']['id'] && $role_id != 1) {
                echo "<script>alert('Bạn không thể tự hạ quyền Admin của chính mình!'); window.history.back();</script>";
                exit;
            }

            $this->userModel->updateUserByAdmin($id, $role_id, $is_verified);
            // [FIX URL] Về trang danh sách
            header("Location: " . $this->baseUrl . "admin/user");
            exit;
        }

        require_once __DIR__ . '/../Views/users/edit.php';
    }

    // 3. XÓA USER
    public function delete() {
        $id = $_GET['id'] ?? '';

        // Không cho phép tự xóa chính mình
        if ($id == $_SESSION['user']['id']) {
            echo "<script>alert('Không thể xóa tài khoản đang đăng nhập!'); window.history.back();</script>";
            exit;
        }

        $this->userModel->deleteUser($id);
        // [FIX URL] Về trang danh sách
        header("Location: " . $this->baseUrl . "admin/user");
        exit;
    }
}
?>