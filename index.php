<?php
session_start();
require_once 'config/db.php'; 

// FILE: index.php (Router)

// LẤY URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '/';

// DANH SÁCH ROUTE TĨNH (Link đẹp)
$staticRoutes = [
    'trang-chu'          => ['home', 'index'],
    'dang-nhap'          => ['auth', 'login'],
    'dang-ky'            => ['auth', 'register'],
    'dang-xuat'          => ['auth', 'logout'],
    'tai-khoan'          => ['account', 'profile'],
    'doi-mat-khau'       => ['account', 'changePassword'],
    'gio-hang'           => ['cart', 'index'],
    // 'thanh-toan'         => ['cart', 'checkout'],
    'lich-su-don'        => ['order', 'history'],
    // 'chi-tiet-don'        => ['order', 'detail'],
    'tim-kiem'           => ['product', 'search'], 
    'quen-mat-khau'      => ['auth', 'forgotPassword'],
    // [SỬA/THÊM] Trỏ 'thanh-toan' về CheckoutController -> index
    'thanh-toan'         => ['checkout', 'index'],
    
    // [THÊM MỚI] Route cho trang thành công
    'dat-hang-thanh-cong'=> ['checkout', 'success'],
];

// MẶC ĐỊNH
$module = 'client';
$controllerName = 'home';
$actionName = 'index';

// --- LOGIC ĐỊNH TUYẾN ---

// 1. KIỂM TRA ROUTE TĨNH
if (array_key_exists($url, $staticRoutes)) {
    $module = 'client';
    $controllerName = $staticRoutes[$url][0];
    $actionName = $staticRoutes[$url][1];
} 
// 2. KIỂM TRA SLUG DANH MỤC (/danh-muc/dien-thoai)
elseif (preg_match('#^danh-muc/(.+)$#', $url, $matches)) {
    $controllerName = 'category';
    $actionName = 'index';
    $_GET['slug'] = $matches[1];
}
// 3. KIỂM TRA SLUG SẢN PHẨM (/san-pham/iphone-15.html)
elseif (preg_match('#^san-pham/(.+?)(\.html)?$#', $url, $matches)) {
    $controllerName = 'product';
    $actionName = 'detail';
    $_GET['slug'] = $matches[1];
}
elseif (preg_match('#^chi-tiet-don/([0-9]+)$#', $url, $matches)) {
    $controllerName = 'order';
    $actionName = 'detail';
    $_GET['id'] = $matches[1]; // Lấy số ID từ URL đưa vào biến $_GET['id']
}
// 4. KHU VỰC ADMIN & CÁC LINK KHÁC
elseif (strpos($url, 'admin') === 0) {
    $parts = explode('/', $url);
    $module = 'admin';
    
    // [FIX LỖI 404 QUAN TRỌNG TẠI ĐÂY]
    // Lấy phần controller, nếu rỗng thì về dashboard
    $rawController = $parts[1] ?? 'dashboard';
    
    // Loại bỏ đuôi .php nếu có (Ví dụ: index.php -> index)
    $rawController = str_replace('.php', '', $rawController);
    
    // Nếu tên là 'index' hoặc rỗng -> Về Dashboard
    if (empty($rawController) || $rawController === 'index') {
        $controllerName = 'dashboard';
    } else {
        $controllerName = $rawController;
    }

    $actionName = $parts[2] ?? 'index';
}
// 5. FALLBACK CHO LINK CŨ (?controller=...)
elseif (isset($_GET['controller'])) {
    $module = $_GET['module'] ?? 'client';
    $controllerName = $_GET['controller'];
    $actionName = $_GET['action'] ?? 'index';
}

// --- KHỞI TẠO CONTROLLER ---
$className = ucfirst($controllerName) . 'Controller';

// Đường dẫn file
if ($module === 'admin') {
    $path = __DIR__ . "/app/admin/controllers/{$className}.php";
} else {
    $path = __DIR__ . "/app/Client/Controllers/{$className}.php";
}

if (file_exists($path)) {
    require_once $path;
    if (class_exists($className)) {
        $object = new $className();
        if (method_exists($object, $actionName)) {
            $object->$actionName();
        } else {
            // Fallback về trang chủ hoặc báo lỗi nhẹ
            die("Lỗi 404: Không tìm thấy Action '{$actionName}'");
        }
    } else {
        die("Lỗi 500: Class '{$className}' không tồn tại");
    }
} else {
    // Nếu lỗi, thử chuyển về trang chủ thay vì chết trang
    if ($module == 'client') {
        header("Location: index.php"); 
        exit;
    }
    die("Lỗi 404: File Controller không tồn tại: " . htmlspecialchars($path));
}
?>