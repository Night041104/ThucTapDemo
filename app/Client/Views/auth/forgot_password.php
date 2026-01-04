<style>
    /* (Giữ nguyên style cũ) */
    .auth-wrapper { background-color: #f0f2f5; min-height: 80vh; display: flex; justify-content: center; align-items: center; padding: 40px 0; }
    .auth-container { background: white; width: 400px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
    .input-group { position: relative; margin: 20px 0; }
    .input-group input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
    .btn-submit { width: 100%; background: #cd1818; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; }
    .alert { padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px; text-align: left; }
    .alert-error { background: #ffe6e6; color: #d63031; }
    .alert-success { background: #e6fffa; color: #00b894; }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <h2>Quên mật khẩu?</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
            Nhập email đã đăng ký, chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu cho bạn.
        </p>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=sendResetLink" method="POST">
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Nhập email của bạn" required>
            </div>
            <button type="submit" class="btn-submit">GỬI YÊU CẦU</button>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="dang-nhap" style="color: #555; text-decoration: none; font-size: 14px;">Quay lại đăng nhập</a>
        </div>
    </div>
</div>