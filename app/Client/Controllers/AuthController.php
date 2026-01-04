<?php
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../Helpers/MailHelper.php';

class AuthController {
    private $userModel;

    // --- CẤU HÌNH GOOGLE ---
    private $googleClientID = '814424808372-vtroocch4q3g6viseb7jolvvs5btu11k.apps.googleusercontent.com';
    private $googleClientSecret = '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';
    private $googleRedirectUri; // Không gán giá trị cứng ở đây nữa

    public function __construct() {
        $this->userModel = new UserModel();

        // --- XỬ LÝ URL ĐỘNG (Dynamic URL) ---
        // 1. Kiểm tra http hay https
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://";
        
        // 2. Lấy tên miền (localhost hoặc domain thật)
        $host = $_SERVER['HTTP_HOST'];
        
        // 3. Lấy thư mục chứa file index.php
        // dirname($_SERVER['SCRIPT_NAME']) trả về ví dụ: "/THUCTAPDEMO" hoặc "/baitapPHP/THUCTAPDEMO"
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $dir = rtrim($dir, '/'); // Xóa dấu gạch chéo thừa ở cuối nếu có

        // 4. Tạo đường dẫn Callback đầy đủ
        $this->googleRedirectUri = $protocol . $host . $dir . '/index.php?controller=auth&action=googleCallback';
    }

    // 1. XỬ LÝ ĐĂNG KÝ
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];

            if ($password !== $repassword) {
                $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
                header("Location: dang-ky");
                exit;
            }

            if ($this->userModel->isEmailExists($email)) {
                $_SESSION['error'] = "Email này đã được sử dụng!";
                header("Location: dang-ky");
                exit;
            }

            $data = [
                'fname' => $fname, 
                'lname' => $lname, 
                'email' => $email, 
                'password' => $password
            ];

            $token = $this->userModel->register($data);

            if ($token) {
                $fullName = $lname . ' ' . $fname;
                MailHelper::sendVerificationEmail($email, $fullName, $token);

                $_SESSION['success'] = "Đăng ký thành công! Vui lòng kiểm tra Email để kích hoạt tài khoản.";
                header("Location: dang-nhap");
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, vui lòng thử lại.";
                header("Location: dang-ky");
            }
            exit;
        }
    }

    // 2. XÁC THỰC TÀI KHOẢN
    public function verify() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if (empty($token)) {
            $_SESSION['error'] = "Liên kết không hợp lệ!";
            header("Location: dang-nhap");
            exit;
        }

        if ($this->userModel->verifyAccount($token)) {
            $_SESSION['success'] = "🎉 Kích hoạt tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } else {
            $_SESSION['error'] = "Liên kết xác thực bị lỗi hoặc đã hết hạn!";
        }
        
        header("Location: dang-nhap");
        exit;
    }

    // 3. XỬ LÝ ĐĂNG NHẬP
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $result = $this->userModel->checkLogin($email, $password);

            if ($result === 'unverified') {
                $_SESSION['error'] = "Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email!";
                header("Location: dang-nhap");
                exit;
            }

            if ($result) {
                $_SESSION['user'] = $result;
                header("Location: trang-chu");
            } else {
                $_SESSION['error'] = "Email hoặc mật khẩu không đúng!";
                header("Location: dang-nhap");
            }
            exit;
        }
    }

    public function login() {
        if (isset($_SESSION['user'])) { header("Location: trang-chu"); exit; }
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/login.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
    
    public function register() {
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/register.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
    
    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: dang-nhap");
        exit;
    }

    // 6. QUÊN MẬT KHẨU
    public function forgotPassword() {
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/forgot_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 7. GỬI LINK RESET
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            
            $result = $this->userModel->createResetToken($email);

            if ($result) {
                MailHelper::sendResetPasswordEmail($email, $result['fullname'], $result['token']);
                $_SESSION['success'] = "Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn. Vui lòng kiểm tra!";
            } else {
                $_SESSION['error'] = "Email này chưa được đăng ký trong hệ thống!";
            }
            
            header("Location: quen-mat-khau");
            exit;
        }
    }

    // 8. FORM RESET PASS
    public function resetPassword() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $user = $this->userModel->verifyResetToken($token);

        if (!$user) {
            $_SESSION['error'] = "Đường dẫn không hợp lệ hoặc đã hết hạn!";
            header("Location: quen-mat-khau");
            exit;
        }

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/reset_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 9. XỬ LÝ ĐỔI PASS
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $pass  = $_POST['password'];
            $repass = $_POST['repassword'];

            if ($pass !== $repass) {
                $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
                header("Location: index.php?controller=auth&action=resetPassword&token=$token");
                exit;
            }

            $user = $this->userModel->verifyResetToken($token);
            if (!$user) {
                $_SESSION['error'] = "Phiên làm việc hết hạn, vui lòng thử lại!";
                header("Location: quen-mat-khau");
                exit;
            }

            $this->userModel->resetPassword($user['id'], $pass);

            $_SESSION['success'] = "🎉 Đổi mật khẩu thành công! Hãy đăng nhập ngay.";
            header("Location: dang-nhap");
            exit;
        }
    }

    // --- GOOGLE LOGIN ---

    public function loginGoogle() {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->googleClientID,
            'redirect_uri' => $this->googleRedirectUri, // Sẽ tự động lấy giá trị từ __construct
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        $url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
        header("Location: $url");
        exit;
    }

    public function googleCallback() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $tokenUrl = 'https://oauth2.googleapis.com/token';
            $postData = [
                'code' => $code,
                'client_id' => $this->googleClientID,
                'client_secret' => $this->googleClientSecret,
                'redirect_uri' => $this->googleRedirectUri, // Sẽ tự động lấy giá trị từ __construct
                'grant_type' => 'authorization_code'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $tokenData = json_decode($response, true);

            if (!isset($tokenData['access_token'])) {
                $_SESSION['error'] = "Lỗi kết nối với Google!";
                header("Location: dang-nhap");
                exit;
            }

            $accessToken = $tokenData['access_token'];
            $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $googleInfo = json_decode($response, true);

            if (isset($googleInfo['email'])) {
                $user = $this->userModel->processGoogleUser($googleInfo);
                if ($user) {
                    $_SESSION['user'] = $user;
                    $_SESSION['success'] = "Đăng nhập bằng Google thành công!";
                    header("Location: trang-chu");
                } else {
                    $_SESSION['error'] = "Lỗi xử lý dữ liệu người dùng!";
                    header("Location: dang-nhap");
                }
            } else {
                $_SESSION['error'] = "Không lấy được email từ Google!";
                header("Location: dang-nhap");
            }
            exit;
        } else {
            header("Location: dang-nhap");
            exit;
        }
    }
}
?>