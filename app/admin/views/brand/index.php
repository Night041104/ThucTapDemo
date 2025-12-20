<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thương hiệu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background-color: #f4f6f8; color: #333; }
        .table-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h1 { color: #1565c0; margin: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        th { background: #e3f2fd; color: #0d47a1; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        
        .btn { text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 500; display: inline-block; margin-right: 5px; cursor: pointer;}
        .btn-create { background: #2e7d32; color: white; padding: 10px 20px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .btn-edit { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
        .btn-del { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        
        .msg-box { padding:15px; margin-bottom:20px; border-radius:4px; font-weight:500; }
        .msg-success { background:#d4edda; color:#155724; border: 1px solid #c3e6cb; }
        .msg-error { background:#ffebee; color:#c62828; border: 1px solid #ef9a9a; }

        .logo-thumb { width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; padding: 2px; border-radius: 4px; background: #fff; }
    </style>
</head>
<body>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>🏷️ QUẢN LÝ THƯƠNG HIỆU</h1>
        <a href="index.php?module=admin&controller=brand&action=create" class="btn btn-create">+ Thêm Thương hiệu</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-box <?= (strpos($_GET['msg'], '❌') !== false) ? 'msg-error' : 'msg-success' ?>">
            <?php 
                if ($_GET['msg'] == 'created') echo "✅ Tạo mới thành công!";
                elseif ($_GET['msg'] == 'updated') echo "✅ Cập nhật thành công!";
                elseif ($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa thành công!";
                else echo htmlspecialchars(urldecode($_GET['msg']));
            ?>
        </div>
    <?php endif; ?>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th width="80">Logo</th>
                    <th>Tên Thương hiệu</th>
                    <th>Slug</th>
                    <th width="150">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($listBrands)): ?>
                    <?php foreach($listBrands as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if(!empty($row['logo_url'])): ?>
                                    <img src="<?= $row['logo_url'] ?>" class="logo-thumb">
                                <?php else: ?>
                                    <span style="color:#ccc; font-size:12px;">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <b style="font-size: 1.1em; color:#333;"><?= htmlspecialchars($row['name']) ?></b>
                            </td>
                            <td style="color:#666; font-style:italic;"><?= $row['slug'] ?></td>
                            <td>
                                <a href="index.php?module=admin&controller=brand&action=edit&id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                                <a href="index.php?module=admin&controller=brand&action=delete&id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px; color:#777;">Chưa có thương hiệu nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>