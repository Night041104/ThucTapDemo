<?php
session_start();
// Require file kết nối CSDL nếu cần thiết cho toàn bộ project
// require_once 'config/database.php'; 

// FILE: index.php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// --- [PHẦN 1: CẤU HÌNH ROUTING TĨNH] ---
// Lấy đường dẫn từ .htaccess (nếu có rewrite)
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '/';

// Định nghĩa các đường dẫn "đẹp" trỏ về Controller/Action cụ thể
$staticRoutes = [
    // URL hiển thị      => ['Controller', 'Action']
    'trang-chu'          => ['home', 'index'],
    'dang-nhap'          => ['auth', 'login'],
    'dang-ky'            => ['auth', 'register'],
    'dang-xuat'          => ['auth', 'logout'],
    'tai-khoan'          => ['account', 'profile'],
    'doi-mat-khau'       => ['account', 'changePassword'],
    'gio-hang'           => ['cart', 'index'],
    'thanh-toan'         => ['cart', 'checkout'],
    'lich-su-don'        => ['cart', 'history'],
    'tim-kiem'           => ['product', 'search'], 
];

// --- [PHẦN 2: XỬ LÝ LOGIC ĐỂ TÌM CONTROLLER] ---

// Mặc định ban đầu
$module = $_GET['module'] ?? 'client';
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'index';

// A. Kiểm tra xem URL có nằm trong danh sách định nghĩa cứng không?
if (array_key_exists($url, $staticRoutes)) {
    $module = 'client'; // Các trang đẹp này đều thuộc Client
    $controllerName = $staticRoutes[$url][0];
    $actionName = $staticRoutes[$url][1];
} 
// B. Nếu không phải link đẹp, kiểm tra xem có phải link Admin không
elseif (strpos($url, 'admin') === 0) {
    // Xử lý link dạng: domain.com/admin/product/index
    $parts = explode('/', $url);
    $module = 'admin';
    $controllerName = $parts[1] ?? 'dashboard';
    $actionName = $parts[2] ?? 'index';
}
// C. Fallback: Nếu user nhập tay tham số ?controller=... (Logic cũ của bạn)
elseif (isset($_GET['controller'])) {
    $module = $_GET['module'] ?? 'client';
    $controllerName = $_GET['controller'];
    $actionName = $_GET['action'] ?? 'index';
}

// --- [PHẦN 3: CHUẨN HÓA VÀ GỌI FILE (LOGIC CŨ CỦA BẠN)] ---

// Chuẩn hóa tên Class Controller (Ví dụ: product -> ProductController)
$className = ucfirst($controllerName) . 'Controller';

// Xác định đường dẫn file
$path = "";

if ($module === 'admin') {
    // Admin: Tất cả viết thường (app/admin/controllers)
    $path = __DIR__ . "/app/admin/controllers/{$className}.php";
} else {
    // Client: Chữ 'Client' viết hoa, còn 'controllers' viết thường
    $path = __DIR__ . "/app/Client/Controllers/{$className}.php";
}

// Kiểm tra và chạy
if (file_exists($path)) {
    require_once $path;

    if (class_exists($className)) {
        $object = new $className();
        
        if (method_exists($object, $actionName)) {
            // Chạy Action
            $object->$actionName();
        } else {
            die("Lỗi 404: Không tìm thấy hàm '{$actionName}' trong class '{$className}'");
        }
    } else {
        die("Lỗi 500: Không tìm thấy class '{$className}'");
    }
} else {
    // Debug đường dẫn để bạn dễ sửa lỗi nếu sai folder
    die("Lỗi 404: File không tồn tại tại đường dẫn: <br>" . $path);
}
?>