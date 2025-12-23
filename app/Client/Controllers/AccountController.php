<?php
require_once __DIR__ . '/../../models/UserModel.php';

class AccountController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // 1. HIỂN THỊ TRANG THÔNG TIN
    public function profile() {
        // Bắt buộc phải đăng nhập mới được vào
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // Lấy thông tin mới nhất từ DB (để hiển thị đúng sau khi sửa)
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->getUserById($userId);

        require_once __DIR__ . '/../Views/account/profile.php';
    }

    // 2. XỬ LÝ CẬP NHẬT (POST)
    public function update() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            
            // Lấy dữ liệu từ Form
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $avatarName = '';

            // XỬ LÝ UPLOAD ẢNH (Avatar)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['avatar']['name'];
                $fileTmp = $_FILES['avatar']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, $allowed)) {
                    // Tạo tên file mới để tránh trùng: avatar_USERID_TIMESTAMP.jpg
                    $newFileName = "avatar_" . $userId . "_" . time() . "." . $fileExt;
                    
                    // Đường dẫn lưu file (Tạo thư mục public/uploads/avatars nếu chưa có)
                    $uploadPath = __DIR__ . '/../../../../public/uploads/avatars/' . $newFileName;
                    
                    if (move_uploaded_file($fileTmp, $uploadPath)) {
                        $avatarName = 'public/uploads/avatars/' . $newFileName;
                        
                        // Cập nhật lại Session Avatar ngay lập tức
                        $_SESSION['user']['avatar'] = $avatarName; 
                    }
                }
            }

            // Gọi Model cập nhật
            $data = [
                'fname' => $fname,
                'lname' => $lname,
                'phone' => $phone,
                'street_address' => $address,
                'avatar' => $avatarName
            ];

            if ($this->userModel->updateProfile($userId, $data)) {
                // Cập nhật lại Session tên để Header hiển thị đúng
                $_SESSION['user']['fname'] = $fname;
                $_SESSION['user']['lname'] = $lname;
                
                $_SESSION['success'] = "Cập nhật thông tin thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại.";
            }

            header("Location: index.php?controller=account&action=profile");
            exit;
        }
    }
    // 3. [MỚI] ĐỔI MẬT KHẨU
    public function changePassword() {
        // Bắt buộc đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            
            $currentPass = $_POST['current_password'];
            $newPass     = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];

            // 1. Kiểm tra mật khẩu cũ
            if (!$this->userModel->verifyCurrentPassword($userId, $currentPass)) {
                $_SESSION['error'] = "Mật khẩu hiện tại không đúng!";
                header("Location: index.php?controller=account&action=changePassword");
                exit;
            }

            // 2. Kiểm tra mật khẩu mới
            if (strlen($newPass) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự!";
                header("Location: index.php?controller=account&action=changePassword");
                exit;
            }

            if ($newPass !== $confirmPass) {
                $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
                header("Location: index.php?controller=account&action=changePassword");
                exit;
            }

            // 3. Cập nhật (Tái sử dụng hàm resetPassword đã có ở UserModel)
            if ($this->userModel->resetPassword($userId, $newPass)) {
                $_SESSION['success'] = "Đổi mật khẩu thành công!";
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, vui lòng thử lại.";
            }

            header("Location: index.php?controller=account&action=changePassword");
            exit;
        }

        // Load View
        $user = $_SESSION['user']; // Lấy thông tin user để hiển thị Sidebar
        require_once __DIR__ . '/../Views/account/change_password.php';
    }
}
?>