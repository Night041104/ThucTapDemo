<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $order['order_code'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS CHUNG */
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; color: #333; margin: 0; }
        .page-container { max-width: 1200px; margin: 30px auto 50px; display: flex; gap: 20px; padding: 0 15px; align-items: flex-start; }
        
        /* SIDEBAR */
        .sidebar { width: 280px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); flex-shrink: 0; }
        .user-brief { padding: 25px 20px; text-align: center; background: linear-gradient(135deg, #cd1818 0%, #a51212 100%); color: white; }
        .user-brief img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.3); margin-bottom: 10px; background: white; }
        .user-brief h3 { font-size: 18px; margin: 0; font-weight: 600; }
        .sidebar-menu { padding: 10px 0; }
        .menu-item { display: flex; align-items: center; padding: 12px 25px; color: #555; text-decoration: none; transition: all 0.2s; font-size: 15px; font-weight: 500; }
        .menu-item:hover { background: #f8f9fa; color: #cd1818; }
        .menu-item i { width: 30px; color: #999; font-size: 16px; }
        .menu-item.active { background: #fff5f5; color: #cd1818; border-right: 4px solid #cd1818; font-weight: 700; }
        .menu-item.active i { color: #cd1818; }

        /* MAIN CONTENT */
        .main-content { flex: 1; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        /* ORDER DETAIL STYLES */
        .header-detail { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 25px; }
        .header-detail h2 { margin: 0 0 5px 0; font-size: 20px; color: #333; }
        .order-meta { color: #777; font-size: 14px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; }
        .info-box h4 { margin: 0 0 15px 0; color: #cd1818; font-size: 15px; border-bottom: 1px dashed #ddd; padding-bottom: 10px; text-transform: uppercase; }
        .info-box p { margin: 5px 0; font-size: 14px; color: #444; line-height: 1.5; }
        .info-box strong { font-weight: 600; color: #333; }

        /* TABLE */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th { background: #f4f6f8; padding: 12px; text-align: left; font-size: 14px; color: #555; border-bottom: 2px solid #eee; }
        .item-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        
        .total-area { display: flex; justify-content: flex-end; }
        .total-box { width: 300px; }
        .row-line { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .final-price { font-size: 18px; color: #cd1818; font-weight: 700; border-top: 1px solid #eee; padding-top: 12px; margin-top: 5px; }

        .btn-cancel { background: #fff; color: #dc3545; border: 1px solid #dc3545; padding: 10px 20px; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block; transition: 0.2s; }
        .btn-cancel:hover { background: #dc3545; color: white; }

        .btn-back { display: inline-flex; align-items: center; gap: 5px; color: #666; text-decoration: none; font-size: 14px; margin-bottom: 20px; }
        .btn-back:hover { color: #cd1818; }
        
        /* STATUS */
        .status-badge { padding: 5px 12px; border-radius: 4px; font-weight: 600; font-size: 13px; color: white; }

        @media (max-width: 768px) {
            .page-container { flex-direction: column; }
            .sidebar { width: 100%; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>
<?php $user = $_SESSION['user']; ?>

<div class="page-container">
    <div class="sidebar">
        <div class="user-brief">
            <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'https://i.imgur.com/6k0s8.png' ?>" alt="Avatar" onerror="this.src='https://i.imgur.com/6k0s8.png'">
            <h3><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h3>
        </div>
        <div class="sidebar-menu">
            <a href="index.php?controller=account&action=profile" class="menu-item">
                <i class="fa fa-user-circle"></i> Thông tin tài khoản
            </a>
            <a href="index.php?controller=order&action=history" class="menu-item active">
                <i class="fa fa-shopping-bag"></i> Quản lý đơn hàng
            </a>
            <a href="index.php?controller=account&action=changePassword" class="menu-item">
                <i class="fa fa-lock"></i> Đổi mật khẩu
            </a>
            <a href="index.php?module=client&controller=auth&action=logout" class="menu-item">
                <i class="fa fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </div>

    <div class="main-content">
        <a href="index.php?controller=order&action=history" class="btn-back">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>

        <?php if(isset($_SESSION['success'])): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:6px; border:1px solid #c3e6cb;">
                ✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="header-detail">
            <div>
                <h2>Đơn hàng #<?= $order['order_code'] ?></h2>
                <div class="order-meta">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
            </div>
            <div>
                <?php 
                    $st = $order['status'];
                    $color = '#6c757d'; $text = 'Không rõ';
                    if($st==1) { $color='#f0ad4e'; $text='Chờ xác nhận'; } // Cam
                    if($st==2) { $color='#007bff'; $text='Đã xác nhận'; } // Xanh dương
                    if($st==3) { $color='#17a2b8'; $text='Đang giao hàng'; } // Xanh ngọc
                    if($st==4) { $color='#28a745'; $text='Hoàn thành'; } // Xanh lá
                    if($st==5) { $color='#dc3545'; $text='Đã hủy'; } // Đỏ
                ?>
                <span class="status-badge" style="background: <?= $color ?>;">
                    <?= $text ?>
                </span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h4>Địa chỉ nhận hàng</h4>
                <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
            </div>
            <div class="info-box">
                <h4>Thanh toán & Ghi chú</h4>
                <p><strong>Hình thức:</strong> <?= $order['payment_method'] == 'COD' ? 'Thanh toán khi nhận (COD)' : 'Thanh toán Online (VNPAY)' ?></p>
                <p><strong>Ghi chú:</strong> <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có' ?></p>
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
                        <td>
                            <strong style="font-size:15px; color:#333;"><?= htmlspecialchars($item['product_name']) ?></strong>
                            </td>
                        <td align="center"><?= number_format($item['price']) ?>₫</td>
                        <td align="center"><?= $item['quantity'] ?></td>
                        <td align="right" style="font-weight:600;"><?= number_format($sub) ?>₫</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-area">
            <div class="total-box">
                <div class="row-line">
                    <span>Tạm tính:</span>
                    <span><?= number_format($tempTotal) ?>₫</span>
                </div>
                
                <?php if($order['discount_money'] > 0): ?>
                <div class="row-line" style="color: #28a745;">
                    <span>Giảm giá (<?= htmlspecialchars($order['coupon_code'] ?? 'Coupon') ?>):</span>
                    <span>-<?= number_format($order['discount_money']) ?>₫</span>
                </div>
                <?php endif; ?>

                <div class="row-line final-price">
                    <span>Tổng cộng:</span>
                    <span><?= number_format($order['total_money']) ?>₫</span>
                </div>

                <?php if($order['status'] == 1): ?>
                    <div style="margin-top: 25px; text-align: right;">
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
</div>

</body>
</html>