<?php
// Nhúng thư viện PHPMailer thủ công
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    
    // =================================================================
    // 1. CẤU HÌNH DÙNG CHUNG
    // =================================================================
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587; 
    const SMTP_USER = 'loanthanh3210w1@gmail.com'; // Email của bạn
    const SMTP_PASS = 'oagl hmkd szii wofv';       // Mật khẩu ứng dụng của bạn

    /**
     * Hàm nội bộ dùng để gửi mail (Core function)
     * Tất cả các hàm khác sẽ gọi hàm này để tránh lặp lại code cấu hình
     */
    private static function send($toEmail, $toName, $subject, $bodyContent) {
        $mail = new PHPMailer(true);

        try {
            // --- CẤU HÌNH SERVER ---
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USER;
            $mail->Password   = self::SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = self::SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // --- NGƯỜI GỬI & NGƯỜI NHẬN ---
            $mail->setFrom(self::SMTP_USER, 'FPT Shop Demo - Thông báo');
            $mail->addAddress($toEmail, $toName);

            // --- NỘI DUNG ---
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $bodyContent;
            $mail->AltBody = strip_tags($bodyContent); // Nội dung văn bản thuần cho trình duyệt cũ

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Có thể bỏ comment dòng dưới để debug lỗi nếu gửi thất bại
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    // =================================================================
    // 2. CÁC HÀM CHỨC NĂNG CỤ THỂ
    // =================================================================

    // A. Gửi mail xác nhận đơn hàng
    public static function sendOrderConfirmation($toEmail, $customerName, $orderCode, $totalMoney, $orderItems) {
        $subject = "Xác nhận đơn hàng #$orderCode - FPT Shop Demo";
        
        // Tạo bảng danh sách sản phẩm
        $listItemsHtml = "";
        foreach ($orderItems as $item) {
            $price = number_format($item['price'], 0, ',', '.');
            $subtotal = number_format($item['price'] * $item['quantity'], 0, ',', '.');
            $listItemsHtml .= "
                <tr>
                    <td style='padding:5px; border-bottom:1px solid #ddd'>{$item['product_name']}</td>
                    <td style='padding:5px; border-bottom:1px solid #ddd; text-align:center'>x{$item['quantity']}</td>
                    <td style='padding:5px; border-bottom:1px solid #ddd; text-align:right'>{$subtotal}₫</td>
                </tr>
            ";
        }
        $totalMoneyFmt = number_format($totalMoney, 0, ',', '.');

        // Tạo nội dung HTML
        $bodyContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
                <h2 style='color: #d32f2f; text-align: center;'>ĐẶT HÀNG THÀNH CÔNG!</h2>
                <p>Xin chào <strong>$customerName</strong>,</p>
                <p>Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đang được xử lý.</p>
                
                <div style='background: #f9f9f9; padding: 15px; margin: 20px 0;'>
                    <p><strong>Mã đơn hàng:</strong> <span style='color:#007bff; font-weight:bold'>$orderCode</span></p>
                    <p><strong>Tổng thanh toán:</strong> <span style='color:#d32f2f; font-weight:bold; font-size:18px'>{$totalMoneyFmt}₫</span></p>
                </div>

                <h3>Chi tiết đơn hàng:</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr style='background: #eee;'>
                        <th style='padding:8px; text-align:left'>Sản phẩm</th>
                        <th style='padding:8px; text-align:center'>SL</th>
                        <th style='padding:8px; text-align:right'>Thành tiền</th>
                    </tr>
                    $listItemsHtml
                </table>
                
                <p style='margin-top: 30px; font-size: 13px; color: #777;'>
                    Đây là email tự động, vui lòng không trả lời email này.<br>
                </p>
            </div>
        ";

        // Gọi hàm send chung
        return self::send($toEmail, $customerName, $subject, $bodyContent);
    }

    // B. Gửi mail kích hoạt tài khoản
    // B. Gửi mail kích hoạt tài khoản
    public static function sendVerificationEmail($toEmail, $userName, $token) {
        $subject = "Kích hoạt tài khoản - FPT Shop Demo";
        
        // --- ĐOẠN CODE TỰ ĐỘNG LẤY ĐƯỜNG DẪN (DYNAMIC URL) ---
        // 1. Lấy giao thức http hay https
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://";
        // 2. Lấy tên miền (localhost hoặc domain thật)
        $host = $_SERVER['HTTP_HOST'];
        // 3. Lấy thư mục gốc chứa file index.php đang chạy
        // Ví dụ máy bạn: /THUCTAPDEMO
        // Ví dụ máy bạn kia: /baitapPHP/THUCTAPDEMO
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $dir = rtrim($dir, '/');
        
        // Ghép lại thành đường dẫn gốc
        $baseUrl = $protocol . $host . $dir;
        // -----------------------------------------------------

        // Link kích hoạt tự động theo máy
        $activeLink = $baseUrl . "/index.php?controller=auth&action=verify&token=" . $token;

        $bodyContent = "
            <h3>Xin chào $userName,</h3>
            <p>Cảm ơn bạn đã đăng ký tài khoản.</p>
            <p>Vui lòng click vào đường link dưới đây để kích hoạt tài khoản:</p>
            <p style='margin: 20px 0;'>
                <a href='$activeLink' style='background:#cd1818; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-weight:bold;'>
                    KÍCH HOẠT TÀI KHOẢN NGAY
                </a>
            </p>
            <p>Hoặc copy link này: <br> $activeLink</p>
        ";

        return self::send($toEmail, $userName, $subject, $bodyContent);
    }

    // C. Gửi mail Quên mật khẩu
    public static function sendResetPasswordEmail($toEmail, $fullname, $token) {
        $subject = "Yêu cầu đặt lại mật khẩu - FPT Shop Demo";
        
        // --- COPY LẠI ĐOẠN LOGIC TRÊN HOẶC VIẾT HÀM RIÊNG ---
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $dir = rtrim($dir, '/');
        $baseUrl = $protocol . $host . $dir;
        // -----------------------------------------------------

        // Link reset tự động
        $link = $baseUrl . "/index.php?controller=auth&action=resetPassword&token=$token";

        $bodyContent = "
            <h3>Xin chào $fullname,</h3>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            <p>Vui lòng bấm vào nút bên dưới để tạo mật khẩu mới:</p>
            <p style='margin: 20px 0;'>
                <a href='$link' style='background-color:#007bff; color:white; padding:12px 20px; text-decoration:none; border-radius:4px; font-weight:bold;'>
                    ĐẶT LẠI MẬT KHẨU
                </a>
            </p>
            <p>Link này sẽ hết hạn sau 1 giờ.</p>
            <p>Nếu không phải bạn yêu cầu, hãy bỏ qua email này.</p>
        ";

        return self::send($toEmail, $fullname, $subject, $bodyContent);
    }
}
?>