<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-container { background: white; width: 400px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .auth-header { text-align: center; margin-bottom: 25px; }
        .auth-header h2 { color: #333; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .auth-header p { color: #666; font-size: 14px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #333; }
        .input-group { position: relative; }
        .input-group input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
        .input-group input:focus { border-color: #cd1818; outline: none; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
        
        .btn-submit { width: 100%; background: #cd1818; color: white; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #b01515; }
        
        .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
        .auth-footer a { color: #cd1818; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        /* Thông báo lỗi/thành công */
        .alert { padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .alert-danger { background: #ffe6e6; color: #d63031; border: 1px solid #fab1a0; }
        .alert-success { background: #e6fffa; color: #00b894; border: 1px solid #55efc4; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-header">
        <h2>Đăng nhập</h2>
        <p>Chào mừng bạn quay trở lại FPT Shop Demo</p>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> 
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> 
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?module=client&controller=auth&action=processLogin" method="POST">
        <div class="form-group">
            <label>Email / Số điện thoại</label>
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="text" name="email" placeholder="Nhập email của bạn" required>
            </div>
        </div>

        <div class="form-group">
            <label>Mật khẩu</label>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 15px;">
            <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                <input type="checkbox" style="margin-right: 5px;"> Ghi nhớ đăng nhập
            </label>
            <a href="index.php?module=client&controller=auth&action=forgotPassword" style="color: #666; text-decoration: none;">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
        <div style="text-align: center; margin: 20px 0;">
        <span style="color: #999; font-size: 13px;">HOẶC</span>
    </div>

    <a href="index.php?controller=auth&action=loginGoogle" style="
        display: flex; align-items: center; justify-content: center; gap: 10px;
        width: 100%; padding: 10px; box-sizing: border-box;
        border: 1px solid #ddd; border-radius: 4px; 
        background: white; color: #333; text-decoration: none; font-weight: 600; font-size: 14px;
        transition: 0.2s;
    ">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" style="width: 20px;">
        Đăng nhập bằng Google
    </a>
    <div class="auth-footer"></div>
    </form>

    <div class="auth-footer">
        Chưa có tài khoản? <a href="index.php?module=client&controller=auth&action=register">Đăng ký ngay</a>
        <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
            <a href="index.php" style="color: #555; font-size: 13px;">← Quay về trang chủ</a>
        </div>
    </div>
</div>

</body>
</html>