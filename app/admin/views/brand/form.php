<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $currentData['id'] ? 'Chỉnh sửa' : 'Thêm mới' ?> Thương hiệu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; background-color: #f4f6f8; color:#333; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h2 { color: #1565c0; margin-top: 0; }
        
        input[type=text] { padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
        
        /* Style Checkbox Grid */
        .cat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 5px; max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; border-radius: 4px; }
        .cat-item { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; }
        .cat-item input { width: 16px; height: 16px; cursor: pointer; }

        .btn-save { padding: 12px 30px; background: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 20px; }
        .btn-cancel { display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none; }
        
        .img-preview-box { margin-top: 10px; position: relative; display: inline-block; border: 1px solid #ddd; padding: 5px; background: white; border-radius: 4px; }
        .img-preview-box img { max-width: 150px; max-height: 100px; object-fit: contain; display: block; }
        /* Nút xóa server (đỏ đậm) */
        .btn-del-server { position: absolute; top: -10px; right: -10px; background: #d32f2f; color: white; border-radius: 50%; width: 24px; height: 24px; text-decoration: none; text-align: center; line-height: 24px; font-weight: bold; }
        /* Nút hủy preview (xám) */
        .btn-cancel-preview { position: absolute; top: -10px; right: -10px; background: #757575; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-weight: bold; }

        .msg-error { background:#ffebee; color:#c62828; padding:15px; border-radius:4px; margin-bottom:20px; }
        .msg-success { background:#e8f5e9; color:#1b5e20; padding:15px; border-radius:4px; margin-bottom:20px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><?= $currentData['id'] ? "✏️ Chỉnh sửa Thương hiệu" : "➕ Tạo Thương hiệu mới" ?></h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="<?= strpos($_GET['msg'], 'updated')!==false || strpos($_GET['msg'], 'created')!==false ? 'msg-success' : 'msg-error' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?module=admin&controller=brand&action=save" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $currentData['id'] ?>">

            <div style="margin-bottom: 20px;">
                <label><b>Tên Thương hiệu <span style="color:red">*</span>:</b></label>
                <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" required placeholder="VD: Apple...">
            </div>

            <div style="margin-bottom: 20px;">
                <label><b>Thuộc Danh mục (Có thể chọn nhiều):</b></label>
                <div class="cat-grid">
                    <?php if(!empty($allCats)): ?>
                        <?php foreach($allCats as $cat): ?>
                            <label class="cat-item">
                                <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" 
                                    <?= in_array($cat['id'], $selectedCats) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color:#999; grid-column:span 2;">Chưa có danh mục nào.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label><b>Logo:</b></label><br>
                <input type="file" id="logo-input" name="logo" accept="image/*" style="margin-top: 5px;" onchange="previewImage(this)">
                
                <?php 
                    $hasOldImg = !empty($currentData['logo_url']);
                    $displayStyle = $hasOldImg ? 'block' : 'none';
                ?>
                <div id="preview-area" style="display: <?= $displayStyle ?>;">
                    <div class="img-preview-box">
                        <img id="img-preview" src="<?= $hasOldImg ? $currentData['logo_url'] : '' ?>">
                        
                        <?php if($hasOldImg): ?>
                            <a href="index.php?module=admin&controller=brand&action=deleteImage&id=<?= $currentData['id'] ?>" 
                               id="btn-server-del" class="btn-del-server" 
                               onclick="return confirm('Xóa vĩnh viễn logo này?')" title="Xóa ảnh cũ">✕</a>
                        <?php endif; ?>

                        <button type="button" id="btn-client-cancel" class="btn-cancel-preview" 
                                style="display:none;" onclick="cancelPreview()" title="Hủy chọn">✕</button>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="btn_save" class="btn-save">
                <?= $currentData['id'] ? "LƯU CẬP NHẬT" : "LƯU MỚI" ?>
            </button>
            <a href="index.php?module=admin&controller=brand&action=index" class="btn-cancel">Hủy bỏ</a>
        </form>
    </div>

    <script>
        // Lưu lại đường dẫn ảnh cũ (nếu có)
        const oldImgSrc = '<?= $hasOldImg ? $currentData['logo_url'] : '' ?>';
        const hasOldImg = <?= $hasOldImg ? 'true' : 'false' ?>;

        const previewArea = document.getElementById('preview-area');
        const imgPreview = document.getElementById('img-preview');
        const btnServerDel = document.getElementById('btn-server-del');
        const btnClientCancel = document.getElementById('btn-client-cancel');
        const input = document.getElementById('logo-input');

        function previewImage(inp) {
            if (inp.files && inp.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    previewArea.style.display = 'block';
                    
                    // Ẩn nút xóa server, Hiện nút hủy preview
                    if(btnServerDel) btnServerDel.style.display = 'none';
                    btnClientCancel.style.display = 'block';
                }
                reader.readAsDataURL(inp.files[0]);
            }
        }

        function cancelPreview() {
            input.value = ""; // Reset input file

            if (hasOldImg) {
                // Nếu có ảnh cũ -> Quay về ảnh cũ
                imgPreview.src = oldImgSrc;
                if(btnServerDel) btnServerDel.style.display = 'block';
                btnClientCancel.style.display = 'none';
            } else {
                // Nếu không có ảnh cũ -> Ẩn luôn khung
                previewArea.style.display = 'none';
                imgPreview.src = '';
                btnClientCancel.style.display = 'none';
            }
        }
    </script>
</body>
</html>