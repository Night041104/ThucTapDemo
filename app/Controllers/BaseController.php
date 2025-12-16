<?php
class BaseController {
    protected function view($path, $data = []) {
        extract($data);
        $fullPath = "app/Views/" . $path . ".php";
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            echo "Lỗi: Không tìm thấy View tại đường dẫn: $fullPath";
        }
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
}
?>