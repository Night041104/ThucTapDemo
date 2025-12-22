<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order['order_code'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; }
        .container { max-width: 1000px; margin: 20px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        
        .header-detail { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .header-detail h2 { margin: 0; color: #333; }
        .order-date { color: #666; font-size: 14px; }
        
        .info-grid { display: flex; gap: 30px; margin-bottom: 30px; }
        .info-col { flex: 1; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .info-col h4 { margin-top: 0; margin-bottom: 10px; color: #cd1818; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-col p { margin: 5px 0; font-size: 14px; color: #444; }

        /* TABLE */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th { background: #eee; padding: 10px; text-align: left; }
        .item-table td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .total-row { display: flex; justify-content: flex-end; }
        .total-box { width: 300px; }
        .row-line { display: flex; justify-content: space-between; padding: 5px 0; }
        .final-price { font-size: 20px; color: #cd1818; font-weight: bold; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 5px; }

        .btn-cancel { background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .btn-cancel:hover { background: #c82333; }

        .btn-back { color: #666; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; }
        .btn-back:hover { color: #cd1818; }
        
        .status-badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>

<div style="max-width: 1000px; margin: 20px auto;">
    <a href="index.php?controller=order&action=history" class="btn-back">
        <i class="fa fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="container">
    <div class="header-detail">
        <div>
            <h2>Chi tiết đơn hàng #<?= $order['order_code'] ?></h2>
            <span class="order-date">Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
        </div>
        <div>
            <?php 
                $st = $order['status'];
                $color = '#666'; $text = 'Không rõ';
                if($st==1) { $color='#f0ad4e'; $text='Chờ xác nhận'; }
                if($st==2) { $color='#0275d8'; $text='Đã xác nhận'; }
                if($st==3) { $color='#5bc0de'; $text='Đang giao hàng'; }
                if($st==4) { $color='#5cb85c'; $text='Hoàn thành'; }
                if($st==5) { $color='#d9534f'; $text='Đã hủy'; }
            ?>
            <span class="status-badge" style="background: <?= $color ?>; color: white;">
                <?= $text ?>
            </span>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            ✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            ❌ <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="info-grid">
        <div class="info-col">
            <h4>Thông tin người nhận</h4>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
        </div>
        <div class="info-col">
            <h4>Thông tin thanh toán</h4>
            <p><strong>Phương thức:</strong> <?= $order['payment_method'] == 'COD' ? 'Thanh toán khi nhận hàng' : 'VNPAY (Ví điện tử)' ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note'] ?: 'Không có') ?></p>
        </div>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th width="15%" style="text-align: center;">Đơn giá</th>
                <th width="10%" style="text-align: center;">SL</th>
                <th width="15%" style="text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php $tempTotal = 0; ?>
            <?php foreach($items as $item): ?>
                <?php $sub = $item['price'] * $item['quantity']; $tempTotal += $sub; ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td align="center"><?= number_format($item['price']) ?>₫</td>
                    <td align="center"><?= $item['quantity'] ?></td>
                    <td align="right"><?= number_format($sub) ?>₫</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-row">
        <div class="total-box">
            <div class="row-line">
                <span>Tạm tính:</span>
                <span><?= number_format($tempTotal) ?>₫</span>
            </div>
            
            <?php if($order['discount_money'] > 0): ?>
            <div class="row-line" style="color: green;">
                <span>Giảm giá (<?= $order['coupon_code'] ?>):</span>
                <span>-<?= number_format($order['discount_money']) ?>₫</span>
            </div>
            <?php endif; ?>

            <div class="row-line final-price">
                <span>Tổng cộng:</span>
                <span><?= number_format($order['total_money']) ?>₫</span>
            </div>

            <?php if($order['status'] == 1): ?>
                <div style="margin-top: 20px; text-align: right;">
                    <a href="index.php?controller=order&action=cancel&id=<?= $order['id'] ?>" 
                       class="btn-cancel"
                       onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này chứ?');">
                       Hủy đơn hàng
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>