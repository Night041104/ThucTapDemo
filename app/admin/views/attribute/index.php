<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thuộc tính</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background-color: #f4f6f8; color: #333; }
        .table-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        th { background: #e3f2fd; color: #0d47a1; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        
        .btn { text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 500; display: inline-block; margin-right: 5px; cursor: pointer;}
        .btn-create { background: #2e7d32; color: white; padding: 10px 20px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .btn-edit { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
        .btn-del { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-right: 5px; display: inline-block; }
        .bg-custom { background: #9c27b0; color: white; }
        .bg-variant { background: #ff9800; color: white; }
        .bg-simple { background: #9e9e9e; color: white; }
        
        .msg-box { padding:15px; margin-bottom:20px; border-radius:4px; font-weight:500; }
        .msg-success { background:#d4edda; color:#155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="color:#1565c0; margin:0;">⚙️ QUẢN LÝ THUỘC TÍNH</h1>
        <a href="index.php?module=admin&controller=attribute&action=create" class="btn btn-create">+ Thêm Thuộc tính</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-box msg-success">
            <?php 
                if ($_GET['msg'] == 'created') echo "✅ Tạo mới thành công!";
                elseif ($_GET['msg'] == 'updated') echo "✅ Cập nhật thành công!";
                elseif ($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa thành công!";
                else echo htmlspecialchars($_GET['msg']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th width="100">Mã</th>
                    <th width="250">Tên & Loại</th>
                    <th>Danh sách Options</th>
                    <th width="150">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($listAttrs)): ?>
                    <?php foreach($listAttrs as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><code style="background:#eee; padding:2px 5px; border-radius:4px;"><?= $row['code'] ?></code></td>
                            <td>
                                <b style="font-size: 1.1em; color:#333;"><?= htmlspecialchars($row['name']) ?></b><br>
                                <div style="margin-top:5px;">
                                    <?php if(!empty($row['is_variant'])): ?>
                                        <span class="badge bg-variant">Biến thể</span>
                                    <?php endif; ?>

                                    <?php if($row['is_customizable']): ?>
                                        <span class="badge bg-custom">Custom</span>
                                    <?php endif; ?>

                                    <?php if(empty($row['is_variant']) && empty($row['is_customizable'])): ?>
                                        <span class="badge bg-simple">Thông số</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="color:#555; line-height: 1.4;"><?= htmlspecialchars($row['opts_list'] ?? '') ?></td>
                            <td>
                                <a href="index.php?module=admin&controller=attribute&action=edit&id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                                <a href="index.php?module=admin&controller=attribute&action=delete&id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Bạn có chắc muốn xóa thuộc tính này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px; color:#777;">Chưa có thuộc tính nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>