<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS DÙNG CHUNG (Đồng bộ với Profile) */
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
        h2.page-title { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; font-size: 22px; color: #333; font-weight: 700; }

        /* TABLE STYLES */
        .table-responsive { overflow-x: auto; }
        .order-table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 600px; }
        .order-table th { background: #f8f9fa; padding: 15px; text-align: left; color: #555; font-weight: 600; border-bottom: 2px solid #eee; white-space: nowrap; }
        .order-table td { padding: 15px; border-bottom: 1px solid #eee; color: #444; vertical-align: middle; }
        .order-table tr:hover { background: #fafafa; }

        /* STATUS BADGE */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .bg-pending { background: #fff3cd; color: #856404; }   /* Chờ xác nhận */
        .bg-confirm { background: #cce5ff; color: #004085; }   /* Đã xác nhận */
        .bg-ship    { background: #d1ecf1; color: #0c5460; }   /* Đang giao */
        .bg-success { background: #d4edda; color: #155724; }   /* Hoàn thành */
        .bg-danger  { background: #f8d7da; color: #721c24; }   /* Đã hủy */

        .btn-view { padding: 6px 15px; background: white; color: #cd1818; border: 1px solid #cd1818; border-radius: 4px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s; display: inline-block; }
        .btn-view:hover { background: #cd1818; color: white; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 60px; color: #eee; margin-bottom: 20px; }
        .empty-state p { color: #888; margin-bottom: 20px; }

        @media (max-width: 768px) {
            .page-container { flex-direction: column; margin-top: 20px; }
            .sidebar { width: 100%; }
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
        <h2 class="page-title">Đơn hàng của tôi</h2>

        <?php if(empty($orders)): ?>
            <div class="empty-state">
                <i class="fa fa-box-open"></i>
                <p>Bạn chưa mua đơn hàng nào!</p>
                <a href="index.php" class="btn-view" style="background:#cd1818; color:white;">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
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
                                <td><strong style="color:#333;">#<?= $o['order_code'] ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                                <td style="color: #cd1818; font-weight: 700;"><?= number_format($o['total_money']) ?>₫</td>
                                <td>
                                    <?php 
                                        $st = $o['status'];
                                        $class = 'bg-pending'; $text = 'Chờ xử lý';
                                        if($st==2) { $class='bg-confirm'; $text='Đã xác nhận'; }
                                        if($st==3) { $class='bg-ship'; $text='Đang giao'; }
                                        if($st==4) { $class='bg-success'; $text='Thành công'; }
                                        if($st==5) { $class='bg-danger'; $text='Đã hủy'; }
                                    ?>
                                    <span class="badge <?= $class ?>"><?= $text ?></span>
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
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>