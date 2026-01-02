<?php
class AboutController {
    public function index() {
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/about.php'; 
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
    public function warranty(){
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/warranty.php'; 
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
    public function payment(){
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/payment.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
    public function guide() {
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/home/shopping_guide.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }
}
?>