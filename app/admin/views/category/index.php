<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
    <style>
        body{font-family:'Segoe UI', sans-serif; padding:20px; background:#f4f6f8; color:#333;}
        .table-box{background:white; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05);}
        
        table{width:100%; border-collapse:collapse; margin-top:15px;}
        th,td{padding:12px 10px; border-bottom:1px solid #eee; text-align:left; vertical-align: middle;}
        th{background:#e3f2fd; color:#0d47a1; font-weight:600;}
        tr:hover {background-color: #f9f9f9;}
        
        .btn{text-decoration:none; padding:6px 12px; border-radius:4px; font-size:13px; display:inline-block; margin-right:5px; font-weight:500; border:none; cursor:pointer;}
        .btn-edit{background:#e3f2fd; color:#1565c0; border:1px solid #bbdefb;} .btn-edit:hover{background:#bbdefb;}
        .btn-del{background:#ffebee; color:#c62828; border:1px solid #ffcdd2;} .btn-del:hover{background:#ffcdd2;}
        .btn-create{background:#2e7d32; color:white; padding:10px 20px; font-size:14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);}
        
        .badge-count { background: #eee; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; color: #555; }
        .badge-info { background: #e3f2fd; color: #1565c0; }
        
        .msg-box { padding:15px; margin-bottom:20px; border-radius:4px; font-weight:500; }
        .msg-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .msg-error { background:#ffebee; color:#c62828; border:1px solid #ef9a9a; }
    </style>
</head>
<body>
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="color:#1565c0; margin:0;">📂 QUẢN LÝ DANH MỤC</h1>
        <a href="index.php?module=admin&controller=category&action=create" class="btn btn-create">+ Thêm Danh Mục</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="msg-box <?= (strpos($_GET['msg'], '❌') !== false) ? 'msg-error' : 'msg-success' ?>">
            <?php 
                if ($_GET['msg'] == 'created') echo "✅ Tạo danh mục thành công!";
                elseif ($_GET['msg'] == 'updated') echo "✅ Cập nhật danh mục thành công!";
                elseif ($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa danh mục.";
                else echo $_GET['msg'];
            ?>
        </div>
    <?php endif; ?>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Tên Danh Mục</th>
                    <th>Slug (URL)</th>
                    <th>Cấu hình Template</th>
                    <th width="150">Hành động</th>
                </tr>
            </thead>
            <tbody>
            
            <?php if (!empty($listCates) && is_array($listCates)): ?>
                <?php foreach($listCates as $c): 
                    $tpl = json_decode($c['spec_template'], true) ?? []; 
                    $countItems = 0;
                    foreach($tpl as $g) $countItems += count($g['items']);
                ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td>
                        <strong style="color:#2c3e50; font-size:15px;"><?= htmlspecialchars($c['name']) ?></strong>
                    </td>
                    <td style="color:#666; font-style:italic;"><?= $c['slug'] ?></td>
                    <td>
                        <?php if(count($tpl) > 0): ?>
                            <span class="badge-count"><?= count($tpl) ?> nhóm</span>
                            <span class="badge-count badge-info"><?= $countItems ?> thông số</span>
                        <?php else: ?>
                            <span style="color:#999; font-size:12px;">(Chưa cấu hình)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?module=admin&controller=category&action=edit&id=<?= $c['id'] ?>" class="btn btn-edit">Sửa</a>
                        <a href="index.php?module=admin&controller=category&action=delete&id=<?= $c['id'] ?>" class="btn btn-del" onclick="return confirm('Bạn có chắc muốn xóa danh mục này? Các sản phẩm thuộc danh mục này sẽ bị ảnh hưởng!')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:30px; color:#777;">Chưa có danh mục nào.</td>
                </tr>
            <?php endif; ?>
            
            </tbody>
        </table>
    </div>

</body>
</html>