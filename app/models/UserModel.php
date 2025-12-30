<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {

    // 1. [SỬA] ĐĂNG KÝ TÀI KHOẢN (Trả về Token thay vì true/false)
    public function register($data) {
        $uuid = $this->generateUUID(); 
        $fname = $this->escape($data['fname']);
        $lname = $this->escape($data['lname']);
        $email = $this->escape($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Tạo mã xác thực ngẫu nhiên (32 bytes -> 64 ký tự hex)
        $token = bin2hex(random_bytes(32));

        // Lưu ý: is_verified = 0 (Chưa kích hoạt)
        // Lưu verification_token vào DB
        $sql = "INSERT INTO users (id, fname, lname, email, password, role_id, is_verified, verification_token) 
                VALUES ('$uuid', '$fname', '$lname', '$email', '$password', 0, 0, '$token')";
        
        if ($this->_query($sql)) {
            return $token; // Trả về Token để Controller gửi mail
        }
        return false;
    }

    // 2. [MỚI] KÍCH HOẠT TÀI KHOẢN BẰNG TOKEN
    public function verifyAccount($token) {
        $token = $this->escape($token);
        
        // Tìm user có token này và chưa kích hoạt
        $sql = "SELECT id FROM users WHERE verification_token = '$token' AND is_verified = 0 LIMIT 1";
        $result = $this->_query($sql);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Kích hoạt: Set is_verified = 1 và xóa token đi (để không dùng lại được)
            $uid = $user['id'];
            $update = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = '$uid'";
            return $this->_query($update);
        }
        return false;
    }

    // 3. [SỬA] CHECK LOGIN (Thêm điều kiện phải kích hoạt rồi mới cho vào)
    public function checkLogin($email, $password) {
        $email = $this->escape($email);
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $this->_query($sql);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                // [QUAN TRỌNG] Kiểm tra đã kích hoạt chưa
                if ($user['is_verified'] == 0) {
                    return 'unverified'; // Trả về trạng thái chưa kích hoạt
                }
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }

    // ... (Giữ nguyên hàm isEmailExists và generateUUID cũ) ...
    public function isEmailExists($email) {
        $email = $this->escape($email);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = $this->_query($sql);
        return mysqli_num_rows($result) > 0;
    }

    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    // 4. [MỚI] LẤY THÔNG TIN USER THEO ID
    public function getUserById($id) {
        $id = $this->escape($id);
        $sql = "SELECT * FROM users WHERE id = '$id'";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // 5. [MỚI] CẬP NHẬT THÔNG TIN CÁ NHÂN
   public function updateProfile($id, $data) {
        $id = $this->escape($id);
        $fname = $this->escape($data['fname']);
        $lname = $this->escape($data['lname']);
        $phone = $this->escape($data['phone']);
        
        // [KIỂM TRA KỸ 4 DÒNG NÀY]
        $street = $this->escape($data['street_address']);
        $city = $this->escape($data['city']);
        $district = $this->escape($data['district']);
        $ward = $this->escape($data['ward']);
        
        $avatarSql = "";
        if (!empty($data['avatar'])) {
            $avt = $this->escape($data['avatar']);
            $avatarSql = ", avatar = '$avt'";
        }

        $sql = "UPDATE users SET 
                fname = '$fname', 
                lname = '$lname', 
                phone = '$phone', 
                street_address = '$street', 
                city = '$city', 
                district = '$district', 
                ward = '$ward'
                $avatarSql
                WHERE id = '$id'";
        
        return $this->_query($sql);
    }
    // 6. [MỚI] TẠO TOKEN RESET MẬT KHẨU
    public function createResetToken($email) {
        $email = $this->escape($email);
        
        // Kiểm tra email có tồn tại không
        $check = $this->_query("SELECT id, fname, lname FROM users WHERE email = '$email'");
        $user = mysqli_fetch_assoc($check);
        
        if (!$user) return false;

        // Tạo token ngẫu nhiên
        $token = bin2hex(random_bytes(32));
        
        // Token hết hạn sau 1 giờ (NOW + 1 hour)
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Lưu vào DB
        $sql = "UPDATE users SET reset_token = '$token', reset_token_expires = '$expiry' WHERE email = '$email'";
        $this->_query($sql);

        return [
            'token' => $token,
            'fullname' => $user['lname'] . ' ' . $user['fname']
        ];
    }

    // 7. [MỚI] KIỂM TRA TOKEN CÓ HỢP LỆ KHÔNG
    public function verifyResetToken($token) {
        $token = $this->escape($token);
        $now = date('Y-m-d H:i:s');

        // Tìm user có token này và chưa hết hạn
        $sql = "SELECT id FROM users WHERE reset_token = '$token' AND reset_token_expires > '$now'";
        $result = $this->_query($sql);
        
        return mysqli_fetch_assoc($result); // Trả về user nếu đúng, null nếu sai/hết hạn
    }

    // 8. [MỚI] ĐẶT LẠI MẬT KHẨU MỚI
    public function resetPassword($userId, $newPassword) {
        $userId = $this->escape($userId);
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Cập nhật pass mới và xóa token đi
        $sql = "UPDATE users SET password = '$hash', reset_token = NULL, reset_token_expires = NULL WHERE id = '$userId'";
        return $this->_query($sql);
    }
    // 9. [MỚI] KIỂM TRA MẬT KHẨU HIỆN TẠI (Dùng cho chức năng Đổi mật khẩu)
    public function verifyCurrentPassword($userId, $password) {
        $userId = $this->escape($userId);
        $sql = "SELECT password FROM users WHERE id = '$userId'";
        $result = $this->_query($sql);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }
    // 10. [MỚI] XỬ LÝ ĐĂNG NHẬP GOOGLE
    public function processGoogleUser($googleInfo) {
        $googleId = $this->escape($googleInfo['id']);
        $email    = $this->escape($googleInfo['email']);
        $fname    = $this->escape($googleInfo['given_name']);
        $lname    = $this->escape($googleInfo['family_name']);
        $avatar   = $this->escape($googleInfo['picture']);

        // TRƯỜNG HỢP 1: Đã từng đăng nhập bằng Google rồi
        $sql = "SELECT * FROM users WHERE google_id = '$googleId'";
        $result = $this->_query($sql);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            return $user; // Cho đăng nhập luôn
        }

        // TRƯỜNG HỢP 2: Chưa có Google ID, nhưng Email đã tồn tại (User cũ liên kết thêm)
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->_query($sql);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Cập nhật thêm google_id và avatar cho user cũ này
            $this->_query("UPDATE users SET google_id = '$googleId', avatar = '$avatar', is_verified = 1 WHERE email = '$email'");
            
            // Lấy lại thông tin mới nhất
            return mysqli_fetch_assoc($this->_query("SELECT * FROM users WHERE email = '$email'"));
        }

        // TRƯỜNG HỢP 3: User hoàn toàn mới -> Tự động Đăng ký
        $uuid = $this->generateUUID();
        // Mật khẩu ngẫu nhiên (vì họ dùng Google nên ko cần pass, nhưng DB yêu cầu)
        $randomPass = password_hash(uniqid(), PASSWORD_DEFAULT); 
        
        $sqlInsert = "INSERT INTO users (id, fname, lname, email, password, role_id, is_verified, google_id, avatar) 
                      VALUES ('$uuid', '$fname', '$lname', '$email', '$randomPass', 0, 1, '$googleId', '$avatar')";
        
        if ($this->_query($sqlInsert)) {
            return mysqli_fetch_assoc($this->_query("SELECT * FROM users WHERE id = '$uuid'"));
        }

        return false;
    }
    // --- KHU VỰC ADMIN ---

    // 11. LẤY TẤT CẢ USER (Có thể phân trang nếu muốn)
    // 11. [ĐÃ NÂNG CẤP] LẤY TẤT CẢ USER (CÓ LỌC)
    public function getAllUsers($keyword = '', $role = '', $status = '') {
        $sql = "SELECT * FROM users WHERE 1=1";

        // 1. Tìm theo từ khóa (Tên hoặc Email)
        if (!empty($keyword)) {
            $keyword = $this->escape($keyword);
            $sql .= " AND (lname LIKE '%$keyword%' OR fname LIKE '%$keyword%' OR email LIKE '%$keyword%')";
        }

        // 2. Lọc theo Vai trò (0 hoặc 1)
        if ($role !== '') {
            $role = (int)$role;
            $sql .= " AND role_id = $role";
        }

        // 3. Lọc theo Trạng thái (0 hoặc 1)
        if ($status !== '') {
            $status = (int)$status;
            $sql .= " AND is_verified = $status";
        }

        $sql .= " ORDER BY created_at DESC";

        $result = $this->_query($sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    // 12. ADMIN CẬP NHẬT USER (Sửa quyền, trạng thái)
    public function updateUserByAdmin($id, $role_id, $is_verified) {
        $id = $this->escape($id);
        $role_id = (int)$role_id;
        $is_verified = (int)$is_verified;

        $sql = "UPDATE users SET role_id = $role_id, is_verified = $is_verified WHERE id = '$id'";
        return $this->_query($sql);
    }

    // 13. XÓA USER
    public function deleteUser($id) {
        $id = $this->escape($id);
        $sql = "DELETE FROM users WHERE id = '$id'";
        return $this->_query($sql);
    }
}
?>