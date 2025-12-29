<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thành viên | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 1200px; margin: 30px auto; padding: 0 15px; }
        
        /* Card Style */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; border-radius: 12px 12px 0 0 !important; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.25rem; font-weight: 700; color: #333; margin: 0; }
        
        /* Table Style */
        .table thead th { background-color: #f1f3f5; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; border: none; padding: 15px; }
        .table tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f0f0f0; color: #555; font-size: 0.95rem; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background-color: #fafafa; }
        
        /* Avatar */
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Badge Status */
        .badge { padding: 8px 12px; border-radius: 30px; font-weight: 500; font-size: 0.75rem; }
        .badge-admin { background-color: #ffe0e0; color: #d63031; }
        .badge-user { background-color: #e3f2fd; color: #0984e3; }
        .badge-active { background-color: #d4edda; color: #155724; }
        .badge-inactive { background-color: #fff3cd; color: #856404; }

        /* Buttons */
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: 0.2s; border: none; }
        .btn-edit { background-color: #e3f2fd; color: #0984e3; margin-right: 5px; }
        .btn-edit:hover { background-color: #0984e3; color: white; }
        .btn-delete { background-color: #ffe0e0; color: #d63031; }
        .btn-delete:hover { background-color: #d63031; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1"><i class="fa fa-cogs me-2"></i>Admin Dashboard</span>
            <a href="index.php" class="btn btn-outline-light btn-sm">Về trang chủ Web</a>
        </div>
    </nav>

    <div class="main-container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-users me-2 text-primary"></i>Danh sách thành viên</h2>
                <span class="badge bg-secondary"><?= count($users) ?> tài khoản</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Thành viên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                                // Xử lý ảnh đại diện
                                                $defaultAvt = 'public/uploads/default/default_avt.png';
                                                $avt = !empty($u['avatar']) ? $u['avatar'] : $defaultAvt;
                                            ?>
                                            <img src="<?= htmlspecialchars($avt) ?>" 
                                                 class="user-avatar me-3" 
                                                 alt="Avatar"
                                                 onerror="this.src='<?= $defaultAvt ?>'">
                                            <div>
                                                <div style="font-weight: 600; color: #333;"><?= htmlspecialchars($u['lname'] . ' ' . $u['fname']) ?></div>
                                                <div style="font-size: 12px; color: #999;">ID: <?= substr($u['id'], 0, 8) ?>...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <?php if($u['role_id'] == 1): ?>
                                            <span class="badge badge-admin"><i class="fa fa-crown me-1"></i> Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-user">Khách hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($u['is_verified'] == 1): ?>
                                            <span class="badge badge-active"><i class="fa fa-check-circle me-1"></i> Đã kích hoạt</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive"><i class="fa fa-clock me-1"></i> Chưa kích hoạt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="index.php?module=admin&controller=user&action=edit&id=<?= $u['id'] ?>" 
                                           class="btn-action btn-edit" title="Sửa quyền">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                        <a href="index.php?module=admin&controller=user&action=delete&id=<?= $u['id'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('⚠️ CẢNH BÁO:\n\nBạn có chắc chắn muốn xóa thành viên này không?\nHành động này không thể hoàn tác!');"
                                           title="Xóa user">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>