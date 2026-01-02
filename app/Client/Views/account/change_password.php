<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded overflow-hidden">
                <div class="card-header border-0 text-center text-white py-4" style="background: linear-gradient(135deg, #cd1818 0%, #a51212 100%);">
                    <?php
                        $u = $_SESSION['user'];
                        $defaultAvt = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
                        $avt = !empty($u['avatar']) ? $u['avatar'] : $defaultAvt;
                    ?>
                    <img src="<?= htmlspecialchars($avt) ?>" alt="Avatar" class="rounded-circle mb-2 bg-white p-1" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= $defaultAvt ?>'">
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($u['lname'] . ' ' . $u['fname']) ?></h6>
                </div>
                
                <div class="list-group list-group-flush py-2">
                    <a href="index.php?controller=account&action=profile" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="index.php?controller=order&action=history" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-shopping-bag me-2"></i> Quản lý đơn hàng
                    </a>
                    <a href="index.php?controller=account&action=changePassword" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-danger bg-light border-start border-4 border-danger">
                        <i class="fa fa-lock me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="index.php?module=client&controller=auth&action=logout" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card border-0 shadow-sm p-4">
                <h4 class="mb-4 pb-3 border-bottom">Thay đổi mật khẩu</h4>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=account&action=changePassword" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-lock text-muted"></i></span>
                            <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu cũ" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-key text-muted"></i></span>
                            <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-check-circle text-muted"></i></span>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">
                        <i class="fa fa-sync-alt me-1"></i> Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>