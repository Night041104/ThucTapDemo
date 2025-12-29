<?php
require_once __DIR__ . '/../../models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();

        // KIỂM TRA QUYỀN ADMIN
        // Nếu chưa đăng nhập hoặc role_id != 1 (1 là Admin) thì đá về trang login
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            header("Location: index.php?module=client&controller=auth&action=login");
            exit;
        }
    }

    // 1. DANH SÁCH USER
    public function index() {
        $users = $this->userModel->getAllUsers();
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

            // Không cho phép tự hạ quyền chính mình (Tránh trường hợp Admin tự biến mình thành Member)
            if ($id == $_SESSION['user']['id'] && $role_id != 1) {
                echo "<script>alert('Bạn không thể tự hạ quyền Admin của chính mình!'); window.history.back();</script>";
                exit;
            }

            $this->userModel->updateUserByAdmin($id, $role_id, $is_verified);
            header("Location: index.php?module=admin&controller=user&action=index");
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
        header("Location: index.php?module=admin&controller=user&action=index");
        exit;
    }
}
?>