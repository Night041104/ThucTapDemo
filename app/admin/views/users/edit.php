<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thành viên | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .edit-container { width: 100%; max-width: 550px; padding: 15px; }
        
        .card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { background: #343a40; color: white; padding: 20px; text-align: center; border: none; }
        .card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        
        .avatar-preview { width: 100px; height: 100px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.15); margin-top: -50px; margin-bottom: 20px; object-fit: cover; background: #fff; }
        
        .form-label { font-weight: 600; font-size: 0.9rem; color: #555; }
        .form-control, .form-select { padding: 10px 15px; border-radius: 8px; border: 1px solid #ddd; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #343a40; box-shadow: 0 0 0 3px rgba(52, 58, 64, 0.1); }
        .form-control:disabled { background-color: #f8f9fa; color: #6c757d; }

        .btn-save { background-color: #0d6efd; border: none; padding: 12px; font-weight: 600; width: 100%; border-radius: 8px; transition: 0.3s; }
        .btn-save:hover { background-color: #0b5ed7; transform: translateY(-2px); }
        
        .btn-back { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #6c757d; font-size: 0.9rem; transition: 0.2s; }
        .btn-back:hover { color: #343a40; }
    </style>
</head>
<body>

    <div class="edit-container">
        <div class="card">
            <div class="card-header">
                <h3>Cập nhật quyền hạn</h3>
            </div>
            <div class="card-body text-center p-4 pt-0">
                <?php
                    $defaultAvt = 'public/uploads/default/default_avt.png';
                    $avt = !empty($user['avatar']) ? $user['avatar'] : $defaultAvt;
                ?>
                <img src="<?= htmlspecialchars($avt) ?>" class="avatar-preview" onerror="this.src='<?= $defaultAvt ?>'">
                
                <h5 class="mb-1 text-dark fw-bold"><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h5>
                <p class="text-muted mb-4"><?= htmlspecialchars($user['email']) ?></p>

                <form action="" method="POST" class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Vai trò (Role)</label>
                        <select name="role_id" class="form-select">
                            <option value="0" <?= $user['role_id'] == 0 ? 'selected' : '' ?>>👤 Khách hàng (User)</option>
                            <option value="1" <?= $user['role_id'] == 1 ? 'selected' : '' ?>>👑 Quản trị viên (Admin)</option>
                        </select>
                        <div class="form-text">Admin có toàn quyền quản lý hệ thống.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Trạng thái tài khoản</label>
                        <select name="is_verified" class="form-select">
                            <option value="1" <?= $user['is_verified'] == 1 ? 'selected' : '' ?>>✅ Đã kích hoạt (Hoạt động)</option>
                            <option value="0" <?= $user['is_verified'] == 0 ? 'selected' : '' ?>>⛔ Bị khóa / Chưa kích hoạt</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-save">
                        <i class="fa fa-save me-2"></i> Lưu thay đổi
                    </button>
                </form>

                <a href="index.php?module=admin&controller=user&action=index" class="btn-back">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>

</body>
</html>