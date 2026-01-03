<?php
require_once __DIR__ . '/../../models/CouponModel.php';

class CouponController {
    private $couponModel;

    public function __construct() {
        $this->couponModel = new CouponModel();

        // --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
        // 1. Kiểm tra đã đăng nhập chưa? (isset $_SESSION['user'])
        // 2. Kiểm tra có phải Admin không? (role_id == 1)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            // Nếu không thỏa mãn, đá về trang đăng nhập ngay lập tức
            header("Location: index.php?module=client&controller=auth&action=login");
            exit;
        }
    }

    // 1. Danh sách (CÓ LỌC)
    public function index() {
        // Lấy tham số bộ lọc
        $keyword = $_GET['keyword'] ?? '';
        $status  = $_GET['status'] ?? '';
        $type    = $_GET['type'] ?? '';

        // Gọi Model
        $coupons = $this->couponModel->getAllCoupons($keyword, $status, $type);
        
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
            header("Location: index.php?module=admin&controller=coupon");
            exit;
        }

        $isEdit = true;
        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once __DIR__ . '/../Views/coupon/form.php';
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    // 4. Lưu dữ liệu (Chung cho Create và Update)
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? 0;
            
            $data = [
                'code' => strtoupper(trim($_POST['code'])),
                'description' => trim($_POST['description']),
                'type' => $_POST['type'],
                'value' => (int)$_POST['value'],
                'max_discount_amount' => ($_POST['type'] == 'percent') ? (int)$_POST['max_discount_amount'] : 0,
                'min_order_amount' => (int)$_POST['min_order_amount'],
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

            header("Location: index.php?module=admin&controller=coupon");
            exit;
        }
    }

    // 5. Xóa
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $this->couponModel->deleteCoupon($id);
        $_SESSION['success'] = "Đã xóa mã giảm giá!";
        header("Location: index.php?module=admin&controller=coupon");
        exit;
    }
}
?>