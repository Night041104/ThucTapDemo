<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử mua hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; }
        .container { max-width: 1200px; margin: 20px auto; display: flex; gap: 20px; padding: 0 15px; }
        
        /* Copy style Sidebar từ profile.php sang hoặc include file CSS chung */
        .sidebar { width: 250px; background: white; border-radius: 8px; overflow: hidden; height: fit-content; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .user-brief { padding: 20px; text-align: center; border-bottom: 1px solid #eee; background: linear-gradient(to bottom, #cd1818, #a51212); color: white; }
        .user-brief img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.5); margin-bottom: 10px; background: white; }
        .menu-item { display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f9f9f9; transition: 0.2s; font-size: 14px; }
        .menu-item:hover { background: #f8f9fa; color: #cd1818; padding-left: 25px; }
        .menu-item i { width: 25px; color: #999; }
        .menu-item.active { color: #cd1818; font-weight: bold; background: #fff5f5; border-left: 3px solid #cd1818; }

        /* CONTENT */
        .main-content { flex: 1; background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2.page-title { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; font-size: 20px; color: #333; }

        /* TABLE */
        .order-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .order-table th { background: #f8f9fa; padding: 12px; text-align: left; color: #555; border-bottom: 2px solid #eee; }
        .order-table td { padding: 12px; border-bottom: 1px solid #eee; color: #333; }
        .order-table tr:hover { background: #fafafa; }

        /* STATUS BADGE */
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block; }
        .bg-pending { background: #fff3cd; color: #856404; }
        .bg-confirm { background: #cce5ff; color: #004085; }
        .bg-ship { background: #d1ecf1; color: #0c5460; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-danger { background: #f8d7da; color: #721c24; }

        .btn-view { padding: 5px 10px; background: #cd1818; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .btn-view:hover { background: #a51212; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>
<?php 
    // Lấy user từ session để hiển thị sidebar
    $user = $_SESSION['user'];
?>

<div class="container">
    <div class="sidebar">
        <div class="user-brief">
            <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'https://i.imgur.com/6k0s8.png' ?>" alt="Avatar">
            <h3><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h3>
        </div>
        <a href="index.php?controller=account&action=profile" class="menu-item">
            <i class="fa fa-user"></i> Thông tin tài khoản
        </a>
        <a href="index.php?controller=order&action=history" class="menu-item active">
            <i class="fa fa-file-invoice-dollar"></i> Quản lý đơn hàng
        </a>
        <a href="index.php?controller=account&action=changePassword" class="menu-item">
            <i class="fa fa-key"></i> Đổi mật khẩu
        </a>
        <a href="index.php?module=client&controller=auth&action=logout" class="menu-item">
            <i class="fa fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>

    <div class="main-content">
        <h2 class="page-title">Đơn hàng của tôi</h2>

        <?php if(empty($orders)): ?>
            <div style="text-align: center; padding: 50px; color: #777;">
                <i class="fa fa-box-open" style="font-size: 50px; margin-bottom: 20px; color: #ddd;"></i>
                <p>Bạn chưa mua đơn hàng nào!</p>
                <a href="index.php" style="color: #cd1818; font-weight: bold;">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                        <tr>
                            <td><strong><?= $o['order_code'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                            <td style="color: #cd1818; font-weight: bold;"><?= number_format($o['total_money']) ?>₫</td>
                            <td>
                                <?php 
                                    $st = $o['status'];
                                    $stText = ''; $stClass = '';
                                    switch($st) {
                                        case 1: $stText = 'Chờ xác nhận'; $stClass = 'bg-pending'; break;
                                        case 2: $stText = 'Đã xác nhận'; $stClass = 'bg-confirm'; break;
                                        case 3: $stText = 'Đang giao'; $stClass = 'bg-ship'; break;
                                        case 4: $stText = 'Hoàn thành'; $stClass = 'bg-success'; break;
                                        case 5: $stText = 'Đã hủy'; $stClass = 'bg-danger'; break;
                                    }
                                ?>
                                <span class="badge <?= $stClass ?>"><?= $stText ?></span>
                            </td>
                            <td>
                                <a href="index.php?controller=order&action=detail&id=<?= $o['id'] ?>" class="btn-view">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>