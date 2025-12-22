<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; }
        .container { max-width: 1200px; margin: 20px auto; display: flex; gap: 20px; padding: 0 15px; }
        
        /* SIDEBAR MENU */
        .sidebar { width: 250px; background: white; border-radius: 8px; overflow: hidden; height: fit-content; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .user-brief { padding: 20px; text-align: center; border-bottom: 1px solid #eee; background: linear-gradient(to bottom, #cd1818, #a51212); color: white; }
        .user-brief img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.5); margin-bottom: 10px; background: white; }
        .user-brief h3 { font-size: 16px; margin: 0; }
        
        .menu-item { display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f9f9f9; transition: 0.2s; font-size: 14px; }
        .menu-item:hover { background: #f8f9fa; color: #cd1818; padding-left: 25px; }
        .menu-item i { width: 25px; color: #999; }
        .menu-item.active { color: #cd1818; font-weight: bold; background: #fff5f5; border-left: 3px solid #cd1818; }

        /* MAIN CONTENT */
        .main-content { flex: 1; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2.page-title { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; font-size: 20px; color: #333; }
        
        /* FORM STYLES */
        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-control:focus { border-color: #cd1818; outline: none; }
        
        .avatar-upload { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .avatar-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; }
        
        .btn-save { background: #cd1818; color: white; border: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-save:hover { background: #b01212; }

        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="sidebar">
        <div class="user-brief">
            <?php $avt = !empty($user['avatar']) ? $user['avatar'] : 'https://i.imgur.com/6k0s8.png'; ?> <img src="<?= $avt ?>" alt="Avatar">
            <h3><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h3>
        </div>
        <a href="index.php?controller=account&action=profile" class="menu-item active">
            <i class="fa fa-user"></i> Thông tin tài khoản
        </a>
        <a href="index.php?controller=order&action=history" class="menu-item">
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
        <h2 class="page-title">Hồ sơ của tôi</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">❌ <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="index.php?controller=account&action=update" method="POST" enctype="multipart/form-data">
            
            <div class="avatar-upload">
                <img id="img-preview" src="<?= $avt ?>" class="avatar-preview">
                <div>
                    <label for="file-upload" style="cursor: pointer; background: #eee; padding: 8px 15px; border-radius: 4px; font-size: 13px;">
                        <i class="fa fa-camera"></i> Chọn ảnh mới
                    </label>
                    <input id="file-upload" type="file" name="avatar" style="display: none;" onchange="previewImage(this)">
                    <div style="font-size: 12px; color: #777; margin-top: 5px;">Dung lượng file tối đa 1MB. Định dạng: .JPEG, .PNG</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ (Last Name)</label>
                    <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($user['lname']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Tên (First Name)</label>
                    <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($user['fname']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email (Không thể thay đổi)</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: #f9f9f9; color: #777;">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Nhập số điện thoại">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Địa chỉ nhận hàng</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['street_address'] ?? '') ?>" placeholder="Số nhà, tên đường, phường/xã...">
            </div>

            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </form>
    </div>
</div>

<script>
    // Hàm hiển thị xem trước ảnh khi chọn file
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>