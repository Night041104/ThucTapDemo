<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
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
        h2.page-title { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; font-size: 22px; color: #333; font-weight: 700; }

        /* FORM STYLE */
        .form-group { margin-bottom: 20px; max-width: 500px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #555; }
        .input-wrapper { position: relative; }
        .input-wrapper input { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: 0.2s; }
        .input-wrapper input:focus { border-color: #cd1818; outline: none; box-shadow: 0 0 0 3px rgba(205, 24, 24, 0.1); }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }

        .btn-save { background: #cd1818; color: white; border: none; padding: 12px 40px; border-radius: 6px; font-weight: 700; font-size: 16px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px rgba(205, 24, 24, 0.2); }
        .btn-save:hover { background: #b01212; transform: translateY(-1px); }

        .alert { padding: 15px; border-radius: 6px; margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

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
            <a href="index.php?controller=order&action=history" class="menu-item">
                <i class="fa fa-shopping-bag"></i> Quản lý đơn hàng
            </a>
            <a href="index.php?controller=account&action=changePassword" class="menu-item active">
                <i class="fa fa-lock"></i> Đổi mật khẩu
            </a>
            <a href="index.php?module=client&controller=auth&action=logout" class="menu-item">
                <i class="fa fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="page-title">Thay đổi mật khẩu</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fa fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=account&action=changePassword" method="POST">
            <div class="form-group">
                <label>Mật khẩu hiện tại</label>
                <div class="input-wrapper">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="current_password" placeholder="Nhập mật khẩu cũ" required>
                </div>
            </div>

            <div class="form-group">
                <label>Mật khẩu mới</label>
                <div class="input-wrapper">
                    <i class="fa fa-key"></i>
                    <input type="password" name="new_password" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" required>
                </div>
            </div>

            <div class="form-group">
                <label>Xác nhận mật khẩu mới</label>
                <div class="input-wrapper">
                    <i class="fa fa-check-circle"></i>
                    <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                </div>
            </div>

            <button type="submit" class="btn-save">Cập nhật mật khẩu</button>
        </form>
    </div>
</div>

</body>
</html>