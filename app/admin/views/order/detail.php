<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f6f8; }
        .container { display: flex; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); flex: 1; }
        
        h2 { margin-top: 0; color: #1565c0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        p { margin: 10px 0; line-height: 1.5; color: #333; }
        strong { color: #555; }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; color: #333; }
        td { color: #444; }

        /* Buttons */
        .btn-update { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-update:hover { background: #218838; }
        .back-link { text-decoration: none; color: #666; font-weight: bold; display: inline-block; margin-bottom: 15px; font-size: 14px; }
        .back-link:hover { color: #000; }

        /* Badge Status */
        .badge { padding: 6px 12px; border-radius: 20px; color: white; font-weight: bold; font-size: 12px; display: inline-block; margin-bottom: 10px;}
        .st-1 { background: #ffc107; color: #333; } /* Chờ xác nhận */
        .st-2 { background: #17a2b8; } /* Đã xác nhận/Thanh toán */
        .st-3 { background: #007bff; } /* Đang giao */
        .st-4 { background: #28a745; } /* Hoàn thành */
        .st-5 { background: #dc3545; } /* Hủy */

        /* Alert Message */
        .alert-success { background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <a href="index.php?module=admin&controller=order&action=index" class="back-link">← Quay lại danh sách đơn hàng</a>
    
    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'updated'): ?>
            <div class="alert-success">✅ Cập nhật trạng thái đơn hàng thành công!</div>
        <?php else: ?>
            <div class="alert-error"><?= htmlspecialchars(urldecode($_GET['msg'])) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="container">
        
        <div class="box">
            <h2>ℹ️ Thông tin đơn hàng: <?= $order['order_code'] ?></h2>
            
            <div>
                <?php 
                    $st = $order['status'];
                    $stLabel = '';
                    $stClass = 'st-' . $st;
                    
                    switch($st) {
                        case 1: $stLabel = 'Chờ xác nhận'; break;
                        case 2: $stLabel = ($order['payment_method'] == 'VNPAY') ? 'Đã thanh toán' : 'Đã xác nhận'; break;
                        case 3: $stLabel = 'Đang giao hàng'; break;
                        case 4: $stLabel = 'Hoàn thành'; break;
                        case 5: $stLabel = 'Đã hủy'; break;
                        default: $stLabel = 'Không rõ';
                    }
                ?>
                <span class="badge <?= $stClass ?>">Trạng thái: <?= $stLabel ?></span>
            </div>

            <p><strong>Ngày đặt:</strong> <?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?></p>
            
            <p>
                <strong>Phương thức thanh toán: </strong> 
                <?php if($order['payment_method'] == 'VNPAY'): ?>
                    <span style="color: #6610f2; font-weight:bold; background: #e0d4fc; padding: 2px 8px; border-radius: 4px;">
                        💳 Thanh toán Online (VNPAY)
                    </span>
                <?php else: ?>
                    <span style="color: #333; font-weight:bold; background: #eee; padding: 2px 8px; border-radius: 4px;">
                        💵 Thanh toán khi nhận hàng (COD)
                    </span>
                <?php endif; ?>
            </p>

            <hr style="border: 0; border-top: 1px dashed #ddd; margin: 15px 0;">

            <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
            <p><strong>Ghi chú:</strong> <em style="color:#666"><?= htmlspecialchars($order['note'] ?: 'Không có') ?></em></p>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
            
            <h3 style="color:#333; font-size: 16px;">Cập nhật trạng thái xử lý</h3>
            <form action="index.php?module=admin&controller=order&action=update_status" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                
                <div style="display: flex; gap: 10px;">
                    <select name="status" style="padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="1" <?= $order['status']==1 ? 'selected':'' ?>>1. Chờ xác nhận</option>
                        <option value="2" <?= $order['status']==2 ? 'selected':'' ?>>2. Đã xác nhận / Đã thanh toán</option>
                        <option value="3" <?= $order['status']==3 ? 'selected':'' ?>>3. Đang giao hàng</option>
                        <option value="4" <?= $order['status']==4 ? 'selected':'' ?>>4. Hoàn thành (Đã giao)</option>
                        <option value="5" <?= $order['status']==5 ? 'selected':'' ?>>5. ❌ Hủy đơn hàng</option>
                    </select>

                    <button type="submit" class="btn-update">Cập nhật</button>
                </div>
            </form>
            
            <?php if($order['status'] != 5): ?>
                <p style="color: #d9534f; font-size: 13px; margin-top: 10px; font-style: italic;">
                    * Lưu ý: Khi chuyển sang "Hủy đơn hàng", hệ thống sẽ tự động cộng lại số lượng sản phẩm vào kho.
                </p>
            <?php else: ?>
                 <p style="color: #28a745; font-size: 13px; margin-top: 10px; font-style: italic;">
                    * Đơn hàng đang ở trạng thái Hủy. Nếu bạn chuyển về trạng thái khác, hệ thống sẽ trừ lại kho (nếu đủ hàng).
                </p>
            <?php endif; ?>
        </div>

        <div class="box">
            <h2>🛒 Sản phẩm trong đơn</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                                <small style="color:#888">ID: <?= $item['product_id'] ?></small>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                            <td style="text-align: center; font-weight: bold;">x<?= $item['quantity'] ?></td>
                            <td style="text-align: right;">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr style="background: #fff8e1;">
                        <td colspan="3" style="text-align: right; font-weight: bold; padding-top: 20px;">TỔNG CỘNG:</td>
                        <td style="text-align: right; color: #cb1c22; font-size: 20px; font-weight: bold; padding-top: 20px;">
                            <?= number_format($order['total_money'], 0, ',', '.') ?>₫
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>