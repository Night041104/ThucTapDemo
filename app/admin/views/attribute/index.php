<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản lý Attribute</title>
<style>body{font-family:sans-serif;padding:20px;max-width:900px;margin:0 auto} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #ddd;padding:8px} .form-box{background:#f9f9f9;padding:15px;border:1px solid #ddd;}</style>
</head>
<body>
    <h2>Quản lý Thuộc tính</h2>
    <div class="form-box">
        <form method="POST" action="index.php?controller=attribute&action=save">
            <input type="hidden" name="id" value="<?= $currentData['id'] ?>">
            Mã: <input type="text" name="code" value="<?= $currentData['code'] ?>" required> 
            Tên: <input type="text" name="name" value="<?= $currentData['name'] ?>" required>
            <br><br>
            <label><input type="checkbox" name="is_customizable" <?= $currentData['is_customizable']?'checked':'' ?>> Cho phép đổi tên hiển thị?</label>
            <br><br>
            Options (phân cách dấu phẩy): <br>
            <textarea name="options" style="width:100%"><?= $currentData['options_str'] ?></textarea>
            <br><br>
            <button type="submit"><?= $currentData['id'] ? 'Cập nhật' : 'Thêm mới' ?></button>
            <?php if($currentData['id']): ?><a href="index.php?controller=attribute">Hủy</a><?php endif; ?>
        </form>
    </div>

    <table>
        <thead><tr><th>ID</th><th>Code</th><th>Tên</th><th>Options</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($listAttrs as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['code'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['opts_list'] ?></td>
                <td>
                    <a href="index.php?controller=attribute&action=edit&id=<?= $row['id'] ?>">Sửa</a> | 
                    <a href="index.php?controller=attribute&action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body></html>