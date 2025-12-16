<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
    <style>body{font-family:sans-serif;padding:20px;max-width:1000px;margin:0 auto} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #ddd;padding:8px}</style>
</head>
<body>
    <h2>Danh sách Danh mục</h2>
    <a href="index.php?controller=category&action=create" style="background:green;color:white;padding:5px 10px;text-decoration:none">Thêm Danh mục</a>
    
    <table>
        <thead><tr><th>ID</th><th>Tên</th><th>Slug</th><th>Template</th><th>Hành động</th></tr></thead>
        <tbody>
        
        <?php if (!empty($listCates) && is_array($listCates)): ?>
            
            <?php foreach($listCates as $c): 
                $tpl = json_decode($c['spec_template'], true); 
            ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $c['slug'] ?></td>
                <td><?= count($tpl ?? []) ?> nhóm</td>
                <td>
                    <a href="index.php?controller=category&action=edit&id=<?= $c['id'] ?>">Sửa</a> |
                    <a href="index.php?controller=category&action=delete&id=<?= $c['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; color:red; padding:15px;">
                    Chưa có danh mục nào hoặc lỗi kết nối!
                </td>
            </tr>
        <?php endif; ?>
        
        </tbody>
    </table>
</body></html>