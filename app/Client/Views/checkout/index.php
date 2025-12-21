<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; display: flex; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .left-col { flex: 1.5; }
        .right-col { flex: 1; }
        h2 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea.form-control { height: 80px; resize: vertical; }
        .order-summary table { width: 100%; font-size: 14px; }
        .order-summary td { padding: 8px 0; border-bottom: 1px dashed #eee; }
        .total-row { font-size: 18px; font-weight: bold; color: #cb1c22; text-align: right; padding-top: 10px; }
        .btn-confirm { width: 100%; padding: 12px; background: #cb1c22; color: white; border: none; font-weight: bold; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 20px; }
        .btn-confirm:hover { background: #b0181d; }
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
                <small style="color: #666; font-size: 12px;">Hóa đơn và thông báo trạng thái sẽ được gửi vào email này.</small>
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
                <label>Ghi chú đơn hàng</label>
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
                                <small>x <?= $_SESSION['cart'][$p['id']] ?></small>
                            </td>
                            <td align="right">
                                <?= number_format($p['price'] * $_SESSION['cart'][$p['id']], 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div class="total-row">
                    Tổng cộng: <?= number_format($totalMoney, 0, ',', '.') ?>₫
                </div>
            </div>

            <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
            
            <p style="text-align: center; margin-top: 15px;">
                <a href="index.php?controller=cart" style="text-decoration: none; color: #666;">← Quay lại giỏ hàng</a>
            </p>
        </div>

    </div>
</form>

</body>
</html>