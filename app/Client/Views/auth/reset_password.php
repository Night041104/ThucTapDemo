<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .auth-container { background: white; width: 400px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .input-group { position: relative; margin-bottom: 15px; }
        .input-group input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
        .btn-submit { width: 100%; background: #cd1818; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; }
        .alert-error { background: #ffe6e6; color: #d63031; padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2 style="text-align: center;">Mật khẩu mới</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=processResetPassword" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">

            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Nhập mật khẩu mới" required>
            </div>
            
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="repassword" placeholder="Xác nhận mật khẩu mới" required>
            </div>

            <button type="submit" class="btn-submit">ĐỔI MẬT KHẨU</button>
        </form>
    </div>
</body>
</html>