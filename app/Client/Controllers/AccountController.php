<?php
require_once __DIR__ . '/../../models/UserModel.php';

class AccountController {
    private $userModel;
    // Đường dẫn gốc tính từ file index.php
    private $uploadDir = 'uploads/avatars/';

    public function __construct() {
        $this->userModel = new UserModel();

        // [TỐI ƯU] Tự động tạo thư mục ngay khi khởi tạo (Viết 1 lần dùng mãi mãi)
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->getUserById($userId);

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/profile.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    public function update() {
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            
            // 1. Lấy thông tin cũ để biết đường dẫn ảnh cũ cần xóa
            $currentUser = $this->userModel->getUserById($userId);
            $oldAvatarPath = $currentUser['avatar'] ?? '';

            // 2. Lấy dữ liệu form
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $phone = trim($_POST['phone']);
            $street = trim($_POST['street_address']); 
            $city     = trim($_POST['city']);
            $district = trim($_POST['district']);
            $ward     = trim($_POST['ward']);
            $districtId = isset($_POST['district_id']) ? (int)$_POST['district_id'] : 0;
            $wardCode   = isset($_POST['ward_code']) ? trim($_POST['ward_code']) : '';

            $avatarName = ''; 

            // 3. XỬ LÝ UPLOAD ẢNH
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $fileName = $_FILES['avatar']['name'];
                $fileTmp  = $_FILES['avatar']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, $allowed)) {
                    $newFileName = "avatar_" . $userId . "_" . time() . "." . $fileExt;
                    $targetFile = $this->uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmp, $targetFile)) {
                        $avatarName = $targetFile; 
                        $_SESSION['user']['avatar'] = $avatarName; 

                        // XÓA ẢNH CŨ
                        if (!empty($oldAvatarPath) && $oldAvatarPath!="uploads/default/default_avt.png") {
                            $pathToDelete = ltrim($oldAvatarPath, '/'); 
                            if (file_exists($pathToDelete) && $pathToDelete != $avatarName) {
                                unlink($pathToDelete);
                            }
                        }

                    } else {
                        $_SESSION['error'] = "Không thể lưu file ảnh. Kiểm tra quyền ghi thư mục!";
                        // [FIX] Link tài khoản
                        header("Location: tai-khoan");
                        exit;
                    }
                } else {
                    $_SESSION['error'] = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!";
                    // [FIX] Link tài khoản
                    header("Location: tai-khoan");
                    exit;
                }
            }

            // 4. GỌI MODEL CẬP NHẬT
            $data = [
                'fname' => $fname,
                'lname' => $lname,
                'phone' => $phone,
                'street_address' => $street,
                'city' => $city,
                'district' => $district,
                'ward' => $ward,
                'district_id' => $districtId,
                'ward_code'   => $wardCode,
                'avatar' => $avatarName
            ];

            if ($this->userModel->updateProfile($userId, $data)) {
                // Cập nhật lại Session
                $_SESSION['user']['fname'] = $fname;
                $_SESSION['user']['lname'] = $lname;
                $_SESSION['user']['phone'] = $phone;
                $_SESSION['user']['street_address'] = $street;
                $_SESSION['user']['city'] = $city;
                $_SESSION['user']['district'] = $district;
                $_SESSION['user']['ward'] = $ward;
                $_SESSION['user']['district_id'] = $districtId;
                $_SESSION['user']['ward_code']   = $wardCode;
                
                $_SESSION['success'] = "Cập nhật thông tin thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu Database.";
            }

            // [FIX] Link tài khoản
            header("Location: tai-khoan");
            exit;
        }
    }

    public function changePassword() {
        if (!isset($_SESSION['user'])) {
            // [FIX] Link đăng nhập
            header("Location: dang-nhap");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            
            $currentPass = $_POST['current_password'];
            $newPass     = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];

            if (!$this->userModel->verifyCurrentPassword($userId, $currentPass)) {
                $_SESSION['error'] = "Mật khẩu hiện tại không đúng!";
                // [FIX] Link đổi mật khẩu
                header("Location: doi-mat-khau");
                exit;
            }

            if (strlen($newPass) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự!";
                // [FIX] Link đổi mật khẩu
                header("Location: doi-mat-khau");
                exit;
            }

            if ($newPass !== $confirmPass) {
                $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
                // [FIX] Link đổi mật khẩu
                header("Location: doi-mat-khau");
                exit;
            }

            if ($this->userModel->resetPassword($userId, $newPass)) {
                $_SESSION['success'] = "Đổi mật khẩu thành công!";
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, vui lòng thử lại.";
            }

            // [FIX] Link đổi mật khẩu
            header("Location: doi-mat-khau");
            exit;
        }

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/change_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>