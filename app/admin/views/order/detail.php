<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f6f8; }
        .container { display: flex; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); flex: 1; }
        h2 { margin-top: 0; color: #1565c0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        p { margin: 8px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        
        .btn-update { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
        .back-link { text-decoration: none; color: #666; font-weight: bold; display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>

    <a href="index.php?module=admin&controller=order&action=index" class="back-link">← Quay lại danh sách</a>
    
    <?php if(isset($_GET['msg']) && $_GET['msg']=='updated'): ?>
        <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:4px;">
            ✅ Cập nhật trạng thái thành công!
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="box">
            <h2>ℹ️ Thông tin đơn hàng: <?= $order['order_code'] ?></h2>
            <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Điện thoại:</strong> <?= $order['phone'] ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></p>
            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>

            <hr>
            
            <h3>Cập nhật trạng thái</h3>
            <form action="index.php?module=admin&controller=order&action=update_status" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                
                <select name="status" style="padding: 8px; width: 200px; font-size: 14px;">
                    <option value="1" <?= $order['status']==1 ? 'selected':'' ?>>Chờ xác nhận</option>
                    <option value="2" <?= $order['status']==2 ? 'selected':'' ?>>Đã xác nhận</option>
                    <option value="3" <?= $order['status']==3 ? 'selected':'' ?>>Đang giao hàng</option>
                    <option value="4" <?= $order['status']==4 ? 'selected':'' ?>>Hoàn thành</option>
                    <option value="5" <?= $order['status']==5 ? 'selected':'' ?>>❌ Hủy đơn hàng</option>
                </select>

                <button type="submit" class="btn-update">Cập nhật</button>
            </form>
            
            <?php if($order['status'] != 5): ?>
                <p style="color: #d9534f; font-size: 13px; margin-top: 10px;">
                    * Lưu ý: Nếu chọn "Hủy đơn hàng", hệ thống sẽ tự động hoàn trả số lượng sản phẩm về kho.
                </p>
            <?php endif; ?>
        </div>

        <div class="box">
            <h2>🛒 Sản phẩm đã mua</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá lúc mua</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($item['product_name']) ?><br>
                                <small style="color:#666">ID: <?= $item['product_id'] ?></small>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                            <td>x<?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr>
                        <td colspan="3" align="right"><strong>TỔNG CỘNG:</strong></td>
                        <td style="color:#cb1c22; font-size:18px; font-weight:bold;">
                            <?= number_format($order['total_money'], 0, ',', '.') ?>₫
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>