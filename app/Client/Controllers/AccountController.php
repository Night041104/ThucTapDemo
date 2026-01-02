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

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/profile.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. XỬ LÝ CẬP NHẬT (POST)
    public function update() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            
            // 1. LẤY DỮ LIỆU TỪ FORM (Đảm bảo đúng tên name="")
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $phone = trim($_POST['phone']);
            
            // [QUAN TRỌNG] Phải khớp với name="street_address" bên view
            $street = trim($_POST['street_address']); 
            
            // Lấy 3 trường địa chỉ mới
            $city     = trim($_POST['city']);
            $district = trim($_POST['district']);
            $ward     = trim($_POST['ward']);

           // 1. MẶC ĐỊNH KHÔNG CÓ ẢNH MỚI
            $avatarName = ''; 

            // 2. XỬ LÝ UPLOAD ẢNH
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Thêm webp
                $fileName = $_FILES['avatar']['name'];
                $fileTmp  = $_FILES['avatar']['tmp_name'];
                
                // Lấy đuôi file
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, $allowed)) {
                    // Đặt tên file mới: avatar_ID_TIMESTAMP.jpg
                    $newFileName = "avatar_" . $userId . "_" . time() . "." . $fileExt;
                    
                    // Đường dẫn thư mục (Tuyệt đối)
                    $targetDir = __DIR__ . '/../../../../uploads/avatars/';
                    
                    // [QUAN TRỌNG] Kiểm tra xem thư mục có tồn tại không, nếu không thì tạo
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $targetFile = $targetDir . $newFileName;
                    
                    // Di chuyển file
                    if (move_uploaded_file($fileTmp, $targetFile)) {
                        // Lưu đường dẫn tương đối vào DB (để hiển thị trên web)
                        $avatarName = '/uploads/avatars/' . $newFileName;
                        
                        // Cập nhật luôn vào Session để thấy ngay
                        $_SESSION['user']['avatar'] = $avatarName; 
                    } else {
                        // Debug lỗi nếu không di chuyển được
                        $_SESSION['error'] = "Không thể lưu file ảnh. Kiểm tra quyền ghi thư mục!";
                        header("Location: index.php?controller=account&action=profile");
                        exit;
                    }
                } else {
                    $_SESSION['error'] = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF)!";
                    header("Location: index.php?controller=account&action=profile");
                    exit;
                }
            }

            // 2. GỌI MODEL CẬP NHẬT
            $data = [
                'fname' => $fname,
                'lname' => $lname,
                'phone' => $phone,
                'street_address' => $street, // Lưu số nhà
                'city' => $city,             // Lưu Tỉnh
                'district' => $district,     // Lưu Huyện
                'ward' => $ward,             // Lưu Xã
                'avatar' => $avatarName
            ];

            if ($this->userModel->updateProfile($userId, $data)) {
                // Cập nhật lại Session ngay lập tức để hiển thị ra View
                $_SESSION['user']['fname'] = $fname;
                $_SESSION['user']['lname'] = $lname;
                $_SESSION['user']['phone'] = $phone;
                $_SESSION['user']['street_address'] = $street;
                $_SESSION['user']['city'] = $city;
                $_SESSION['user']['district'] = $district;
                $_SESSION['user']['ward'] = $ward;
                
                $_SESSION['success'] = "Cập nhật thông tin thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu Database.";
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
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/account/change_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>