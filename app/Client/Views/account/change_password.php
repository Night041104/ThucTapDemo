<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; }
        .container { max-width: 1200px; margin: 20px auto; display: flex; gap: 20px; padding: 0 15px; }
        
        /* SIDEBAR (Dùng chung style) */
        .sidebar { width: 250px; background: white; border-radius: 8px; overflow: hidden; height: fit-content; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .user-brief { padding: 20px; text-align: center; border-bottom: 1px solid #eee; background: linear-gradient(to bottom, #cd1818, #a51212); color: white; }
        .user-brief img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.5); margin-bottom: 10px; background: white; }
        .menu-item { display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f9f9f9; transition: 0.2s; font-size: 14px; }
        .menu-item:hover { background: #f8f9fa; color: #cd1818; padding-left: 25px; }
        .menu-item i { width: 25px; color: #999; }
        .menu-item.active { color: #cd1818; font-weight: bold; background: #fff5f5; border-left: 3px solid #cd1818; }

        /* MAIN CONTENT */
        .main-content { flex: 1; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2.page-title { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; font-size: 20px; color: #333; }

        /* FORM */
        .form-group { margin-bottom: 20px; max-width: 500px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #555; }
        .input-wrapper { position: relative; }
        .input-wrapper input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .input-wrapper input:focus { border-color: #cd1818; outline: none; }
        .input-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; }

        .btn-save { background: #cd1818; color: white; border: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-save:hover { background: #b01212; }

        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;}
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="sidebar">
        <div class="user-brief">
            <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'https://i.imgur.com/6k0s8.png' ?>" alt="Avatar">
            <h3><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h3>
        </div>
        <a href="index.php?controller=account&action=profile" class="menu-item">
            <i class="fa fa-user"></i> Thông tin tài khoản
        </a>
        <a href="index.php?controller=order&action=history" class="menu-item">
            <i class="fa fa-file-invoice-dollar"></i> Quản lý đơn hàng
        </a>
        <a href="index.php?controller=account&action=changePassword" class="menu-item active">
            <i class="fa fa-key"></i> Đổi mật khẩu
        </a>
        <a href="index.php?module=client&controller=auth&action=logout" class="menu-item">
            <i class="fa fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>

    <div class="main-content">
        <h2 class="page-title">Đổi mật khẩu</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">❌ <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
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
                    <input type="password" name="new_password" placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" required>
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