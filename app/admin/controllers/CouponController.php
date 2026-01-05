<?php
require_once __DIR__ . '/../../models/CouponModel.php';

class CouponController {
    private $couponModel;
    private $baseUrl; // Biến lưu đường dẫn gốc

    public function __construct() {
        $this->couponModel = new CouponModel();

        // 1. Tính toán Base URL
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $protocol . $domainName . $path;

        // --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // [FIX URL] Về trang đăng nhập
            header("Location: " . $this->baseUrl . "dang-nhap");
            exit;
        }
    }

    // 1. Danh sách (CÓ LỌC)
    // File: app/admin/controllers/CouponController.php

    // 1. Danh sách (CÓ LỌC & PHÂN TRANG)
    public function index() {
        // Lấy tham số lọc
        $keyword = $_GET['keyword'] ?? '';
        $status  = $_GET['status'] ?? '';
        $type    = $_GET['type'] ?? '';

        // [MỚI] Lấy tham số phân trang
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 1; // 10 mã mỗi trang

        // Gọi Model (Truyền thêm page, limit)
        $coupons = $this->couponModel->getAllCoupons($keyword, $status, $type, $page, $limit);
        
        // [MỚI] Tính toán phân trang
        $totalRecords = $this->couponModel->countAll($keyword, $status, $type);
        $totalPages   = ceil($totalRecords / $limit);
        
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/coupon/index.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 2. Form Thêm mới
    public function create() {
        $isEdit = false;
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/coupon/form.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 3. Form Sửa
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $coupon = $this->couponModel->getCouponById($id);

        if (!$coupon) {
            $_SESSION['error'] = "Không tìm thấy mã giảm giá!";
            // [FIX URL] Về danh sách
            header("Location: " . $this->baseUrl . "admin/coupon");
            exit;
        }

        $isEdit = true;
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/coupon/form.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 4. Lưu dữ liệu
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? 0;
            
            // Hàm helper để xóa dấu phẩy trong chuỗi tiền tệ (Giữ nguyên logic của bạn)
            $cleanNumber = function($str) {
                return (int)str_replace(',', '', $str);
            };

            $data = [
                'code' => strtoupper(trim($_POST['code'])),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'],
                
                'value' => $cleanNumber($_POST['value']),
                'max_discount_amount' => ($_POST['type'] == 'percent') ? $cleanNumber($_POST['max_discount_amount']) : 0,
                'min_order_amount' => $cleanNumber($_POST['min_order_amount']),
                
                'quantity' => (int)$_POST['quantity'],
                'usage_limit_per_user' => (int)$_POST['usage_limit_per_user'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => isset($_POST['status']) ? 1 : 0
            ];

            if ($id > 0) {
                $this->couponModel->updateCoupon($id, $data);
                $_SESSION['success'] = "Cập nhật mã thành công!";
            } else {
                $this->couponModel->createCoupon($data);
                $_SESSION['success'] = "Thêm mã mới thành công!";
            }

            // [FIX URL] Về danh sách
            header("Location: " . $this->baseUrl . "admin/coupon");
            exit;
        }
    }

    // 5. Xóa
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $this->couponModel->deleteCoupon($id);
        $_SESSION['success'] = "Đã xóa mã giảm giá!";
        // [FIX URL] Về danh sách
        header("Location: " . $this->baseUrl . "admin/coupon");
        exit;
    }
}
?>