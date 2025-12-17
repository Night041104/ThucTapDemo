<?php
// FILE: index.php (Tại thư mục gốc)

// 1. Lấy tham số từ URL
$module = $_GET['module'] ?? 'client'; // Mặc định là client
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'index';

// 2. Chuẩn hóa tên Class Controller (Ví dụ: product -> ProductController)
// Lưu ý: Tên file Controller bắt buộc phải viết Hoa chữ cái đầu (ProductController.php)
$className = ucfirst($controllerName) . 'Controller';

// 3. XÁC ĐỊNH ĐƯỜNG DẪN (QUAN TRỌNG)
$path = "";

if ($module === 'admin') {
    // Admin: Tất cả viết thường (app/admin/controllers)
    $path = __DIR__ . "/app/admin/controllers/{$className}.php";
} else {
    // Client: Chữ 'Client' viết hoa, còn 'controllers' viết thường
    $path = __DIR__ . "/app/Client/controllers/{$className}.php";
}

// 4. Kiểm tra và chạy
if (file_exists($path)) {
    require_once $path;

    if (class_exists($className)) {
        $object = new $className();
        
        if (method_exists($object, $actionName)) {
            $object->$actionName();
        } else {
            die("Lỗi 404: Không tìm thấy hàm '{$actionName}'");
        }
    } else {
        die("Lỗi 500: Không tìm thấy class '{$className}'");
    }
} else {
    die("Lỗi 404: File không tồn tại tại đường dẫn: <br>" . $path);
}
?>