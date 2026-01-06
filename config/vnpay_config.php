<?php
// FILE: config/vnpay_config.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * ---------------------------------------------------------
 * TỰ ĐỘNG LẤY URL GỐC CỦA DỰ ÁN (DYNAMIC BASE URL)
 * ---------------------------------------------------------
 */

// 1. Kiểm tra giao thức (HTTP hoặc HTTPS)
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// 2. Lấy tên host (ví dụ: localhost hoặc domain.com)
$host = $_SERVER['HTTP_HOST'];

// 3. Lấy đường dẫn thư mục gốc nơi chứa file index.php đang chạy
// Ví dụ máy bạn: /THUCTAPDEMO
// Ví dụ máy bạn của bạn: /local/phpBaitap/THUCTAPDEMO
$projectRoot = dirname($_SERVER['SCRIPT_NAME']);

// 4. Chuẩn hóa đường dẫn (Thay thế dấu gạch ngược \ của Windows thành /)
$projectRoot = str_replace('\\', '/', $projectRoot);

// 5. Loại bỏ dấu / ở cuối nếu có (để tránh bị trùng // khi nối chuỗi)
$projectRoot = rtrim($projectRoot, '/');

// 6. Tạo đường dẫn gốc hoàn chỉnh
$base_url = $protocol . $host . $projectRoot;

/*
 * ---------------------------------------------------------
 * CẤU HÌNH VNPAY
 * ---------------------------------------------------------
 */
$vnp_TmnCode = "8EOPMK6Z"; 
$vnp_HashSecret = "Z85MR1ITKD09QS5CZSNI807LNNE0DJC3";
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// [QUAN TRỌNG] Sử dụng $base_url để tạo đường dẫn trả về động
// Kết quả sẽ tự động là: 
// http://localhost/THUCTAPDEMO/index.php... (Máy bạn)
// http://localhost/local/phpBaitap/THUCTAPDEMO/index.php... (Máy bạn của bạn)
$vnp_Returnurl = $base_url . "/index.php?controller=checkout&action=vnpay_return"; 

$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
?>