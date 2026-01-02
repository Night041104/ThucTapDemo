<style>
    .auth-wrapper {
        background-color: #f0f2f5;
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 0;
    }
    .auth-container { background: white; width: 450px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    
    .auth-header { text-align: center; margin-bottom: 25px; }
    .auth-header h2 { color: #333; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
    .auth-header p { color: #666; font-size: 14px; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #333; }
    .input-group { position: relative; }
    .input-group input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
    .input-group input:focus { border-color: #cd1818; outline: none; }
    .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
    
    .row-2 { display: flex; gap: 15px; }
    .col { flex: 1; }

    .btn-submit { width: 100%; background: #cd1818; color: white; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
    .btn-submit:hover { background: #b01515; }
    
    .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    .auth-footer a { color: #cd1818; text-decoration: none; font-weight: 600; }
    .auth-footer a:hover { text-decoration: underline; }

    .alert { padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    .alert-danger { background: #ffe6e6; color: #d63031; border: 1px solid #fab1a0; }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-header">
            <h2>Tạo tài khoản mới</h2>
            <p>Đăng ký thành viên để nhận ưu đãi và tích điểm</p>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> 
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?module=client&controller=auth&action=processRegister" method="POST">
            <div class="row-2">
                <div class="col form-group">
                    <label>Họ</label>
                    <div class="input-group">
                        <i class="fa fa-user"></i>
                        <input type="text" name="fname" placeholder="Họ" required>
                    </div>
                </div>
                <div class="col form-group">
                    <label>Tên</label>
                    <div class="input-group">
                        <i class="fa fa-user"></i>
                        <input type="text" name="lname" placeholder="Tên" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Ví dụ: email@gmail.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nhập lại mật khẩu</label>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="repassword" placeholder="Xác nhận mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">ĐĂNG KÝ NGAY</button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="index.php?module=client&controller=auth&action=login">Đăng nhập tại đây</a>
        </div>
    </div>
</div>