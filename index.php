<?php
// FILE: index.php (Tại thư mục gốc THUCTAPDEMO)

// 1. Cấu hình mặc định
$controllerName = $_GET['controller'] ?? 'category'; 
$actionName     = $_GET['action']     ?? 'index';

// 2. Chuẩn hóa tên (category -> CategoryController)
$className = ucfirst($controllerName) . 'Controller'; 

// 3. ĐƯỜNG DẪN MỚI (QUAN TRỌNG): Phải đi vào thư mục 'app/controllers'
// Lưu ý: Tên thư mục trong ảnh của bạn là chữ thường 'controllers'
$fileController = __DIR__ . "/app/controllers/$className.php";

// 4. Kiểm tra và chạy
if (file_exists($fileController)) {
    require_once $fileController;
    
    if (class_exists($className)) {
        $object = new $className();
        
        if (method_exists($object, $actionName)) {
            $object->$actionName(); 
        } else {
            die("Lỗi 404: Không tìm thấy hàm '$actionName' trong class '$className'");
        }
    } else {
        die("Lỗi 500: Class '$className' không tồn tại.");
    }
} else {
    // In ra đường dẫn lỗi để dễ debug
    die("Lỗi 404: Không tìm thấy file Controller tại: " . $fileController);
}
?>