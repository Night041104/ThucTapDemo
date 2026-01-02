<?php
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../Helpers/MailHelper.php'; // Nhớ require MailHelper

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // 1. XỬ LÝ ĐĂNG KÝ (Đã sửa)
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];

            if ($password !== $repassword) {
                $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            if ($this->userModel->isEmailExists($email)) {
                $_SESSION['error'] = "Email này đã được sử dụng!";
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            $data = [
                'fname' => $fname, 
                'lname' => $lname, 
                'email' => $email, 
                'password' => $password
            ];

            // Gọi Model đăng ký -> Nhận về Token
            $token = $this->userModel->register($data);

            if ($token) {
                // Gửi mail xác thực
                $fullName = $lname . ' ' . $fname;
                MailHelper::sendVerificationEmail($email, $fullName, $token);

                $_SESSION['success'] = "Đăng ký thành công! Vui lòng kiểm tra Email để kích hoạt tài khoản.";
                header("Location: index.php?controller=auth&action=login");
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, vui lòng thử lại.";
                header("Location: index.php?controller=auth&action=register");
            }
            exit;
        }
    }

    // 2. [MỚI] HÀM XÁC THỰC TÀI KHOẢN (Chạy khi bấm link trong mail)
    public function verify() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if (empty($token)) {
            $_SESSION['error'] = "Liên kết không hợp lệ!";
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // Gọi Model kích hoạt
        if ($this->userModel->verifyAccount($token)) {
            $_SESSION['success'] = "🎉 Kích hoạt tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } else {
            $_SESSION['error'] = "Liên kết xác thực bị lỗi hoặc đã hết hạn!";
        }
        
        header("Location: index.php?controller=auth&action=login");
        exit;
    }

    // 3. XỬ LÝ ĐĂNG NHẬP (Sửa lại logic check unverified)
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $result = $this->userModel->checkLogin($email, $password);

            if ($result === 'unverified') {
                $_SESSION['error'] = "Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email!";
                header("Location: index.php?controller=auth&action=login");
                exit;
            }

            if ($result) {
                $_SESSION['user'] = $result;
                if ($result['role_id'] == 1) {
                     // header("Location: index.php?module=admin");
                }
                header("Location: index.php");
            } else {
                $_SESSION['error'] = "Email hoặc mật khẩu không đúng!";
                header("Location: index.php?controller=auth&action=login");
            }
            exit;
        }
    }

    // (Giữ nguyên các hàm login, register view, logout...)
    public function login() {
        if (isset($_SESSION['user'])) { header("Location: index.php"); exit; }
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
        header("Location: index.php");
        exit;
    }
    // 6. HIỂN THỊ FORM NHẬP EMAIL (QUÊN MẬT KHẨU)
    public function forgotPassword() {
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/forgot_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 7. XỬ LÝ GỬI MAIL RESET
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            
            // Gọi Model tạo token
            $result = $this->userModel->createResetToken($email);

            if ($result) {
                // Gửi mail
                MailHelper::sendResetPasswordEmail($email, $result['fullname'], $result['token']);
                
                $_SESSION['success'] = "Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn. Vui lòng kiểm tra!";
            } else {
                $_SESSION['error'] = "Email này chưa được đăng ký trong hệ thống!";
            }
            
            header("Location: index.php?controller=auth&action=forgotPassword");
            exit;
        }
    }

    // 8. HIỂN THỊ FORM NHẬP MẬT KHẨU MỚI
    public function resetPassword() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        
        // Kiểm tra token có hợp lệ không
        $user = $this->userModel->verifyResetToken($token);

        if (!$user) {
            $_SESSION['error'] = "Đường dẫn không hợp lệ hoặc đã hết hạn!";
            header("Location: index.php?controller=auth&action=forgotPassword");
            exit;
        }

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/auth/reset_password.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 9. XỬ LÝ LƯU MẬT KHẨU MỚI
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

            // Kiểm tra lại token lần cuối cho chắc
            $user = $this->userModel->verifyResetToken($token);
            if (!$user) {
                $_SESSION['error'] = "Phiên làm việc hết hạn, vui lòng thử lại!";
                header("Location: index.php?controller=auth&action=forgotPassword");
                exit;
            }

            // Đổi pass
            $this->userModel->resetPassword($user['id'], $pass);

            $_SESSION['success'] = "🎉 Đổi mật khẩu thành công! Hãy đăng nhập ngay.";
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }
    // --- CẤU HÌNH GOOGLE (Điền thông tin bạn lấy ở Bước 1 vào đây) ---
    private $googleClientID = '814424808372-vtroocch4q3g6viseb7jolvvs5btu11k.apps.googleusercontent.com';
    private $googleClientSecret = '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';
    // Link Callback phải KHỚP 100% với link đã khai báo trên Google Console
    private $googleRedirectUri = 'http://localhost/THUCTAPDEMO/index.php?controller=auth&action=googleCallback';

    // 10. CHUYỂN HƯỚNG SANG GOOGLE
    public function loginGoogle() {
        // Tạo URL đăng nhập
        $params = [
            'response_type' => 'code',
            'client_id' => $this->googleClientID,
            'redirect_uri' => $this->googleRedirectUri,
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        $url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
        
        // Chuyển hướng
        header("Location: $url");
        exit;
    }

    // 11. XỬ LÝ KHI GOOGLE TRẢ VỀ (CALLBACK)
    public function googleCallback() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            // A. Dùng Code để lấy Access Token
            $tokenUrl = 'https://oauth2.googleapis.com/token';
            $postData = [
                'code' => $code,
                'client_id' => $this->googleClientID,
                'client_secret' => $this->googleClientSecret,
                'redirect_uri' => $this->googleRedirectUri,
                'grant_type' => 'authorization_code'
            ];

            // Gọi cURL POST
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Nếu bạn chạy localhost bị lỗi SSL thì bỏ comment dòng dưới (nhưng ko khuyến khích trên host thật)
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            $response = curl_exec($ch);
            curl_close($ch);

            $tokenData = json_decode($response, true);

            if (!isset($tokenData['access_token'])) {
                $_SESSION['error'] = "Lỗi kết nối với Google!";
                header("Location: index.php?controller=auth&action=login");
                exit;
            }

            // B. Dùng Access Token để lấy Thông tin User
            $accessToken = $tokenData['access_token'];
            $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $googleInfo = json_decode($response, true);

            if (isset($googleInfo['email'])) {
                // C. Gọi Model xử lý (Đăng nhập/Đăng ký)
                $user = $this->userModel->processGoogleUser($googleInfo);

                if ($user) {
                    $_SESSION['user'] = $user;
                    $_SESSION['success'] = "Đăng nhập bằng Google thành công!";
                    header("Location: index.php");
                } else {
                    $_SESSION['error'] = "Lỗi xử lý dữ liệu người dùng!";
                    header("Location: index.php?controller=auth&action=login");
                }
            } else {
                $_SESSION['error'] = "Không lấy được email từ Google!";
                header("Location: index.php?controller=auth&action=login");
            }
            exit;
        } else {
            // Nếu không có code (Người dùng bấm hủy)
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }
}
?>