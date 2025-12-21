<?php
// Nhúng thư viện PHPMailer thủ công
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    
    // CẤU HÌNH SMTP GMAIL (Thay đổi thông tin của bạn vào đây)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587; // Hoặc 465 nếu dùng SSL
    const SMTP_USER = 'loanthanh3210w1@gmail.com'; // <--- EMAIL CỦA BẠN
    const SMTP_PASS = 'oagl hmkd szii wofv'; // <--- MẬT KHẨU ỨNG DỤNG (Không phải pass login)

    public static function sendOrderConfirmation($toEmail, $customerName, $orderCode, $totalMoney, $orderItems) {
        $mail = new PHPMailer(true);

        try {
            // 1. Cấu hình Server
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USER;
            $mail->Password   = self::SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Dùng TLS
            $mail->Port       = self::SMTP_PORT;
            $mail->CharSet    = 'UTF-8'; // Hỗ trợ tiếng Việt

            // 2. Người gửi & Người nhận
            $mail->setFrom(self::SMTP_USER, 'FBTSHOP - Thông báo');
            $mail->addAddress($toEmail, $customerName);

            // 3. Tạo nội dung Email (HTML)
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
            
            // Nội dung chính
            $bodyContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
                    <h2 style='color: #d32f2f; text-align: center;'>ĐẶT HÀNG THÀNH CÔNG!</h2>
                    <p>Xin chào <strong>$customerName</strong>,</p>
                    <p>Cảm ơn bạn đã đặt hàng tại FBTSHOP. Đơn hàng của bạn đã được tiếp nhận và đang chờ xử lý.</p>
                    
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
                        Nếu cần hỗ trợ, vui lòng liên hệ hotline: 1900 xxxx.
                    </p>
                </div>
            ";

            $mail->isHTML(true);
            $mail->Subject = "Xác nhận đơn hàng #$orderCode - FBTSHOP";
            $mail->Body    = $bodyContent;
            $mail->AltBody = "Cảm ơn bạn đã đặt hàng. Mã đơn: $orderCode. Tổng tiền: $totalMoneyFmt";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần, nhưng không chặn quy trình mua hàng
            // error_log("Mail Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
?>