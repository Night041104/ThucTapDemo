<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; display: flex; gap: 20px; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .left-col { flex: 1.5; }
        .right-col { flex: 1; }
        
        h2 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        
        /* Form Styles */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; font-size: 14px; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .form-control:focus { border-color: #007bff; outline: none; }
        textarea.form-control { height: 80px; resize: vertical; }
        
        /* Order Summary Styles */
        .order-summary table { width: 100%; font-size: 14px; border-collapse: collapse; }
        .order-summary td { padding: 10px 0; border-bottom: 1px dashed #eee; vertical-align: middle; }
        .total-row { font-size: 20px; font-weight: bold; color: #cb1c22; text-align: right; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; }
        
        /* Button Styles */
        .btn-confirm { width: 100%; padding: 15px; background: #cb1c22; color: white; border: none; font-weight: bold; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 20px; text-transform: uppercase; transition: background 0.3s; }
        .btn-confirm:hover { background: #b0181d; }
        
        /* Payment Method Styles */
        .payment-methods { margin: 20px 0; background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #e9ecef; }
        .payment-methods label { display: flex; align-items: center; margin-bottom: 10px; cursor: pointer; padding: 8px; border-radius: 4px; transition: background 0.2s; }
        .payment-methods label:hover { background: #e2e6ea; }
        .payment-methods input { margin-right: 10px; transform: scale(1.2); }
    </style>
</head>
<body>

<form action="index.php?controller=checkout&action=submit" method="POST">
    <div class="container">
        
        <div class="box left-col">
            <h2>🚚 THÔNG TIN GIAO HÀNG</h2>
            
            <div class="form-group">
                <label>Họ và tên người nhận (*)</label>
                <input type="text" name="fullname" class="form-control" required placeholder="VD: Nguyễn Văn A"
                       value="<?= isset($user['fullname']) ? htmlspecialchars($user['fullname']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Email nhận thông báo đơn hàng (*)</label>
                <input type="email" name="email" class="form-control" required placeholder="VD: email@example.com"
                       value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>">
                <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                    Hóa đơn và thông báo trạng thái đơn hàng sẽ được gửi vào email này.
                </small>
            </div>

            <div class="form-group">
                <label>Số điện thoại (*)</label>
                <input type="text" name="phone" class="form-control" required placeholder="VD: 0988xxxxxx"
                       value="<?= isset($user['phone']) ? htmlspecialchars($user['phone']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ nhận hàng (*)</label>
                <input type="text" name="address" class="form-control" required placeholder="VD: Số 123, Đường ABC, Quận X..."
                       value="<?= isset($user['address']) ? htmlspecialchars($user['address']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Ghi chú đơn hàng (Tùy chọn)</label>
                <textarea name="note" class="form-control" placeholder="Ví dụ: Giao hàng giờ hành chính, gọi trước khi giao..."></textarea>
            </div>
        </div>

        <div class="box right-col">
            <h2>📦 ĐƠN HÀNG CỦA BẠN</h2>
            
            <div class="order-summary">
                <table>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($p['name']) ?></strong> <br>
                                <small style="color: #777;">Số lượng: <?= $_SESSION['cart'][$p['id']] ?></small>
                            </td>
                            <td align="right" style="white-space: nowrap;">
                                <?= number_format($p['price'] * $_SESSION['cart'][$p['id']], 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr style="border-top: 2px solid #eee;">
                        <td style="padding-top: 15px; color: #555;">Tạm tính:</td>
                        <td style="padding-top: 15px; text-align: right; font-weight: bold;">
                            <?= number_format($totalMoney, 0, ',', '.') ?>₫
                        </td>
                    </tr>

                    <?php if(isset($discountMoney) && $discountMoney > 0): ?>
                    <tr>
                        <td style="color: #28a745;">
                            Giảm giá (<?= isset($_SESSION['coupon']['code']) ? htmlspecialchars($_SESSION['coupon']['code']) : 'Coupon' ?>):
                        </td>
                        <td style="text-align: right; color: #28a745; font-weight: bold;">
                            -<?= number_format($discountMoney, 0, ',', '.') ?>₫
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <div class="total-row">
                    <small style="font-size: 14px; color: #333; font-weight: normal;">Tổng thanh toán:</small><br>
                    <span><?= number_format($finalTotal, 0, ',', '.') ?>₫</span>
                </div>
            </div>

            <div class="payment-methods">
                <h3 style="margin-top: 0; font-size: 16px; margin-bottom: 15px;">Phương thức thanh toán</h3>
                
                <label>
                    <input type="radio" name="payment_method" value="COD" checked> 
                    <span>💵 Thanh toán khi nhận hàng (COD)</span>
                </label>
                
                <label>
                    <input type="radio" name="payment_method" value="VNPAY"> 
                    <span>💳 Thanh toán Online qua VNPAY</span>
                </label>
            </div>

            <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
            
            <p style="text-align: center; margin-top: 15px;">
                <a href="index.php?controller=cart" style="text-decoration: none; color: #666; font-size: 14px;">← Quay lại giỏ hàng</a>
            </p>
        </div>

    </div>
</form>

</body>
</html>