<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* TỔNG QUAN */
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; color: #333; margin: 0; }
        
        /* [FIX LỖI DÍNH HEADER] Thêm margin-top và padding */
        .page-container { 
            max-width: 1200px; 
            margin: 30px auto 50px; /* Cách trên 30px, dưới 50px */
            display: flex; 
            gap: 20px; 
            padding: 0 15px; 
            align-items: flex-start; /* Để sidebar không bị giãn chiều cao theo nội dung chính */
        }

        /* SIDEBAR (MENU TRÁI) */
        .sidebar { 
            width: 280px; 
            background: white; 
            border-radius: 10px; 
            overflow: hidden; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            flex-shrink: 0; /* Không cho co lại */
        }
        
        .user-brief { 
            padding: 25px 20px; 
            text-align: center; 
            background: linear-gradient(135deg, #cd1818 0%, #a51212 100%); 
            color: white; 
        }
        .user-brief img { 
            width: 90px; height: 90px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 4px solid rgba(255,255,255,0.3); 
            margin-bottom: 10px; 
            background: white; 
        }
        .user-brief h3 { font-size: 18px; margin: 0; font-weight: 600; }
        .user-brief p { font-size: 13px; margin: 5px 0 0; opacity: 0.9; }

        .sidebar-menu { padding: 10px 0; }
        .menu-item { 
            display: flex; align-items: center; 
            padding: 12px 25px; 
            color: #555; text-decoration: none; 
            transition: all 0.2s; font-size: 15px; font-weight: 500;
        }
        .menu-item i { width: 30px; color: #999; font-size: 16px; transition: 0.2s; }
        .menu-item:hover { background: #f8f9fa; color: #cd1818; }
        .menu-item:hover i { color: #cd1818; }
        .menu-item.active { 
            background: #fff5f5; color: #cd1818; 
            border-right: 4px solid #cd1818; font-weight: 700; 
        }
        .menu-item.active i { color: #cd1818; }

        /* MAIN CONTENT (CỘT PHẢI) */
        .main-content { 
            flex: 1; 
            background: white; 
            border-radius: 10px; 
            padding: 30px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
        }
        h2.page-title { 
            margin-top: 0; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
            font-size: 22px; color: #333; font-weight: 700; 
        }

        /* FORM STYLES */
        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .form-group { flex: 1; margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #555; }
        
        .form-control { 
            width: 100%; padding: 10px 15px; 
            border: 1px solid #ddd; border-radius: 6px; 
            font-size: 14px; transition: 0.2s; box-sizing: border-box; 
        }
        .form-control:focus { border-color: #cd1818; outline: none; box-shadow: 0 0 0 3px rgba(205, 24, 24, 0.1); }
        .form-control:disabled { background: #f2f2f2; cursor: not-allowed; color: #888; }

        /* AVATAR UPLOAD */
        .avatar-section { display: flex; align-items: center; gap: 25px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px dashed #eee; }
        .avatar-preview-lg { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .upload-btn { 
            display: inline-block; padding: 8px 15px; 
            background: white; border: 1px solid #ddd; 
            border-radius: 6px; font-size: 13px; font-weight: 600; color: #555; 
            cursor: pointer; transition: 0.2s; margin-top: 5px;
        }
        .upload-btn:hover { background: #f8f9fa; border-color: #bbb; }

        /* BUTTON SAVE */
        .btn-save { 
            background: #cd1818; color: white; border: none; 
            padding: 12px 40px; border-radius: 6px; 
            font-weight: 700; font-size: 16px; 
            cursor: pointer; transition: 0.2s; display: block; 
            box-shadow: 0 4px 6px rgba(205, 24, 24, 0.2); 
        }
        .btn-save:hover { background: #b01212; transform: translateY(-1px); }

        /* ADDRESS BOX */
        .current-address-box {
            background: #fff8f8; border: 1px dashed #cd1818;
            padding: 12px 15px; border-radius: 6px; margin-bottom: 15px;
            font-size: 14px; color: #555; display: flex; align-items: flex-start; gap: 10px;
        }
        .address-select-group { display: flex; gap: 15px; margin-bottom: 15px; }
        .address-select-group select { flex: 1; }

        /* ALERT */
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* RESPONSIVE (MOBILE) */
        @media (max-width: 768px) {
            .page-container { flex-direction: column; margin-top: 20px; }
            .sidebar { width: 100%; }
            .form-row { flex-direction: column; gap: 0; }
            .address-select-group { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../header.php'; ?>

<div class="page-container">
    <div class="sidebar">
        <div class="user-brief">
            <?php
                // Xử lý ảnh đại diện
                $avt = !empty($user['avatar']) ? $user['avatar'] : 'https://i.imgur.com/6k0s8.png';
            ?>
            <img src="<?= htmlspecialchars($avt) ?>" alt="Avatar" onerror="this.src='https://i.imgur.com/6k0s8.png'">
            <h3><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h3>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>
        
        <div class="sidebar-menu">
            <a href="index.php?controller=account&action=profile" class="menu-item active">
                <i class="fa fa-user-circle"></i> Thông tin tài khoản
            </a>
            <a href="index.php?controller=order&action=history" class="menu-item">
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
        <h2 class="page-title">Hồ sơ cá nhân</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> 
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fa fa-exclamation-triangle"></i> 
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=account&action=update" method="POST" enctype="multipart/form-data">
            
            <div class="avatar-section">
                <img id="img-preview" src="<?= htmlspecialchars($avt) ?>" class="avatar-preview-lg" onerror="this.src='https://i.imgur.com/6k0s8.png'">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 5px;">Ảnh đại diện</label>
                    <label for="file-upload" class="upload-btn">
                        <i class="fa fa-camera"></i> Tải ảnh mới
                    </label>
                    <input id="file-upload" type="file" name="avatar" style="display: none;" onchange="previewImage(this)">
                    <div style="font-size: 13px; color: #777; margin-top: 8px;">Dung lượng tối đa 1MB<br>Định dạng: .JPEG, .PNG</div>
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
                    <label>Email đăng nhập</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    <small style="color: #888; font-size: 12px; margin-top: 3px;">Email không thể thay đổi</small>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Nhập số điện thoại">
                </div>
            </div>

            <div class="form-group">
                <label style="font-size: 15px; margin-bottom: 10px;">Địa chỉ nhận hàng</label>

                <?php if(!empty($user['city'])): ?>
                    <div class="current-address-box">
                        <i class="fa fa-map-marker-alt" style="color: #cd1818; margin-top: 3px;"></i> 
                        <div>
                            <strong>Địa chỉ hiện tại:</strong><br>
                            <?= htmlspecialchars($user['street_address']) ?>, 
                            <?= htmlspecialchars($user['ward']) ?>, 
                            <?= htmlspecialchars($user['district']) ?>, 
                            <?= htmlspecialchars($user['city']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="address-select-group">
                    <select id="province" class="form-control"><option value="0">Tỉnh/Thành phố</option></select>
                    <select id="district" class="form-control"><option value="0">Quận/Huyện</option></select>
                    <select id="ward" class="form-control"><option value="0">Phường/Xã</option></select>
                </div>

                <input type="text" name="street_address" class="form-control" 
                       value="<?= htmlspecialchars($user['street_address'] ?? '') ?>" 
                       placeholder="Số nhà, tên đường cụ thể (Ví dụ: 123 Nguyễn Huệ)">
                
                <input type="hidden" name="city" id="city_text" value="<?= $user['city'] ?? '' ?>">
                <input type="hidden" name="district" id="district_text" value="<?= $user['district'] ?? '' ?>">
                <input type="hidden" name="ward" id="ward_text" value="<?= $user['ward'] ?? '' ?>">
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-save">LƯU THAY ĐỔI</button>
            </div>
        </form>
    </div>
</div>

<script>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/js/address_auto.js"></script>

</body>
</html>