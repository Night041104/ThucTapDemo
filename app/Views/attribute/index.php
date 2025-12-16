<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thuộc tính</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background-color: #f8f9fa; }
        .form-container { background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        input[type=text], textarea { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
        .checkbox-box { background: #e3f2fd; padding: 10px; border-radius: 4px; border: 1px solid #bbdefb; margin: 10px 0 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f1f1f1; }
        .btn-save { padding: 10px 25px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background: #0056b3; }
        .btn-edit { color: #007bff; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .btn-del { color: #dc3545; text-decoration: none; }
        .badge-custom { background: #6f42c1; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
        .badge-simple { background: #6c757d; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
        .nav-link { display: inline-block; margin-bottom: 15px; color: #555; text-decoration: none; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Quay lại Dashboard</a>
    <h1>QUẢN LÝ THUỘC TÍNH</h1>

    <div class="form-container">
        <h3><?= !empty($current['id']) ? "✏️ Chỉnh sửa thuộc tính" : "➕ Tạo thuộc tính mới" ?></h3>
        
        <form method="POST" action="index.php?act=store_attribute">
            <input type="hidden" name="id" value="<?= $current['id'] ?? '' ?>">

            <div style="display:flex; gap:20px; margin-bottom:15px;">
                <div style="flex:1">
                    <label><b>Mã (Code):</b></label>
                    <input type="text" name="code" value="<?= $current['code'] ?? '' ?>" required placeholder="VD: color">
                </div>
                <div style="flex:2">
                    <label><b>Tên hiển thị:</b></label>
                    <input type="text" name="name" value="<?= $current['name'] ?? '' ?>" required placeholder="VD: Màu sắc">
                </div>
            </div>

            <div class="checkbox-box">
                <label style="cursor:pointer; display:flex; align-items:center;">
                    <input type="checkbox" name="is_customizable" value="1" <?= (!empty($current['is_customizable']) && $current['is_customizable'] == 1) ? 'checked' : '' ?> style="width:20px; height:20px; margin-right:10px;">
                    <div>
                        <b>Cho phép đổi tên hiển thị (Custom Display)?</b><br>
                        <small>Tick chọn cho: Màu sắc. Bỏ chọn cho: RAM, ROM.</small>
                    </div>
                </label>
            </div>

            <label><b>Các giá trị (Options):</b> <small>(Ngăn cách dấu phẩy)</small></label>
            <textarea name="options" style="height:80px; margin-top:5px;" placeholder="VD: Đỏ, Xanh, Vàng"><?= $current['options_str'] ?? '' ?></textarea>
            
            <br><br>
            <button type="submit" class="btn-save">LƯU DỮ LIỆU</button>
            <?php if(!empty($current['id'])): ?>
                <a href="index.php?act=attributes" style="margin-left:15px; color:red;">Hủy bỏ</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Danh sách hiện có:</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th> <th>Mã</th> <th>Tên</th> <th>Loại</th> <th>Options</th> <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list as $attrId => $data): 
                $optStr = implode(', ', array_column($data['options'], 'value'));
            ?>
                <tr>
                    <td><?= $attrId ?></td>
                    <td><code><?= $data['code'] ?></code></td>
                    <td><b><?= $data['name'] ?></b></td>
                    <td><?= $data['is_customizable'] ? '<span class="badge-custom">Custom</span>' : '<span class="badge-simple">Fixed</span>' ?></td>
                    <td><?= $optStr ?></td>
                    <td>
                        <a href="index.php?act=attributes&edit=<?= $attrId ?>" class="btn-edit">Sửa</a>
                        <a href="index.php?act=delete_attribute&id=<?= $attrId ?>" class="btn-del" onclick="return confirm('Xóa sẽ ảnh hưởng sản phẩm cũ. Chắc chắn?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>