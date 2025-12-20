<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $currentData['id'] ? 'Chỉnh sửa' : 'Thêm mới' ?> Thuộc tính</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; background-color: #f4f6f8; color:#333; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h2 { color: #1565c0; margin-top: 0; }
        
        input[type=text], textarea { padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; font-family: inherit;}
        input[type=text]:focus, textarea:focus { border-color: #1976d2; outline: none; }
        
        .checkbox-group { display: flex; gap: 15px; margin: 20px 0; }
        .checkbox-box { flex: 1; padding: 15px; border-radius: 6px; border: 1px solid transparent; display: flex; align-items: flex-start; gap: 10px; cursor: pointer; transition: 0.2s; }
        .checkbox-box:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .checkbox-box input { width: 20px; height: 20px; margin-top: 2px; cursor: pointer; }
        
        .box-custom { background: #f3e5f5; border-color: #e1bee7; }
        .box-variant { background: #fff3e0; border-color: #ffe0b2; }
        
        .btn-save { padding: 12px 30px; background: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px; width: 100%; }
        .btn-save:hover { background: #1565c0; }
        
        .btn-cancel { display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none; }
        
        .msg-error { background:#ffebee; color:#c62828; padding:15px; border-radius:4px; margin-bottom:20px; border: 1px solid #ef9a9a; font-weight: 500;}
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= $currentData['id'] ? "✏️ Chỉnh sửa thuộc tính" : "➕ Tạo thuộc tính mới" ?></h2>

        <?php if(isset($msg) && $msg): ?>
            <div class="msg-error"><?= $msg ?></div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?module=admin&controller=attribute&action=save">
            <input type="hidden" name="id" value="<?= $currentData['id'] ?>">

            <div style="display:flex; gap:20px; margin-bottom:15px;">
                <div style="flex:1">
                    <label><b>Mã (Code) <span style="color:red">*</span>:</b></label>
                    <input type="text" name="code" value="<?= htmlspecialchars($currentData['code']) ?>" required placeholder="VD: color, ram">
                </div>
                <div style="flex:2">
                    <label><b>Tên hiển thị <span style="color:red">*</span>:</b></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" required placeholder="VD: Màu sắc, Bộ nhớ trong">
                </div>
            </div>

            <div class="checkbox-group">
                <label class="checkbox-box box-custom">
                    <input type="checkbox" name="is_customizable" value="1" <?= $currentData['is_customizable'] == 1 ? 'checked' : '' ?>>
                    <div>
                        <b style="color:#6a1b9a">Cho phép đổi tên (Custom)?</b><br>
                        <small style="color:#555">Dùng cho: <b>Màu sắc</b> (Để sửa Tím -> Tím Mộng Mơ).</small>
                    </div>
                </label>

                <label class="checkbox-box box-variant">
                    <input type="checkbox" name="is_variant" value="1" <?= $currentData['is_variant'] == 1 ? 'checked' : '' ?>>
                    <div>
                        <b style="color:#e65100">Dùng sinh biến thể (Variant)?</b><br>
                        <small style="color:#555">Check nếu thuộc tính này tạo ra sản phẩm con (VD: Màu, RAM).</small>
                    </div>
                </label>
            </div>

            <label><b>Các giá trị (Options):</b> <small>(Ngăn cách bằng dấu phẩy)</small></label>
            <textarea name="options" style="height:100px; margin-top:5px;" placeholder="VD: Đỏ, Xanh, Vàng, Tím"><?= htmlspecialchars($currentData['options_str']) ?></textarea>
            <small style="color:#666; display:block; margin-top:5px;"><i>* Lưu ý: Khi chỉnh sửa, hệ thống sẽ thêm các giá trị mới vào danh sách. Các giá trị cũ sẽ được giữ nguyên để không ảnh hưởng đến sản phẩm đã tạo.</i></small>
            
            <br><br>
            <button type="submit" name="btn_save" class="btn-save">
                <?= $currentData['id'] ? "LƯU CẬP NHẬT" : "LƯU MỚI" ?>
            </button>

            <a href="index.php?module=admin&controller=attribute&action=index" class="btn-cancel">Hủy bỏ, quay lại danh sách</a>
        </form>
    </div>

</body>
</html>