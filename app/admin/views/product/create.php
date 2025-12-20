<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Sản Phẩm Mới</title>
    <style>
        /* GIỮ NGUYÊN CSS CŨ */
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background-color: #f4f6f8; color: #333; }
        .box { background:#fff; padding:25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom:20px; }
        h1 { margin-top: 0; color: #1a237e; }
        h3 { border-bottom: 2px solid #e3f2fd; padding-bottom: 10px; margin-top: 0; color: #1565c0; }
        
        .row-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
        .btn-del { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; cursor: pointer; border-radius: 4px; width: 30px; height: 30px; font-weight: bold;}
        .btn-add { background: #e8f5e9; color: #2e7d32; border: 1px dashed #a5d6a7; cursor: pointer; padding: 10px; width: 100%; font-weight: 500; transition: 0.2s; border-radius: 4px; margin-top: 10px;}
        .btn-add:hover { background: #c8e6c9; }
        
        input[type=text], input[type=number], select { padding: 10px; border: 1px solid #ddd; border-radius: 4px; outline: none; transition: 0.2s; box-sizing: border-box; }
        input[type=text]:focus, select:focus { border-color: #1976d2; box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.1); }
        
        .msg-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; border-left: 5px solid; font-weight: 500; }
        .msg-success { background: #e8f5e9; border-color: #4caf50; color: #1b5e20; }
        .msg-error { background: #ffebee; border-color: #f44336; color: #b71c1c; }
        
        .submit-btn { padding:15px 40px; background:#1976d2; color:white; font-weight:bold; border:none; border-radius:5px; cursor:pointer; font-size: 16px; box-shadow: 0 2px 5px rgba(25, 118, 210, 0.3); transition: 0.2s; width: 100%; }
        .submit-btn:hover { background: #1565c0; transform: translateY(-1px); }

        .upload-area { border: 2px dashed #ccc; padding: 20px; text-align: center; background: #fafafa; border-radius: 6px; cursor: pointer; transition: 0.2s; position: relative; }
        .upload-area:hover { border-color: #1976d2; background: #e3f2fd; }
        
        #thumb-container { position: relative; width: 120px; height: 120px; margin: 10px auto; display: none; border: 1px solid #ddd; padding: 2px; background: white; }
        #thumb-preview { width: 100%; height: 100%; object-fit: contain; }
        
        #gallery-preview-box { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .gal-item { width: 80px; height: 80px; border: 1px solid #ddd; position: relative; border-radius: 4px; overflow: hidden; }
        .gal-item img { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-remove-img { 
            position: absolute; top: 0; right: 0; 
            background: rgba(255,0,0,0.8); color: white; 
            width: 20px; height: 20px; border: none; 
            font-size: 12px; font-weight: bold; cursor: pointer; 
            display: flex; align-items: center; justify-content: center;
        }
        .btn-remove-img:hover { background: red; }
        .badge-fixed { background: #78909c; color: white; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1 style="color:#1a237e; margin:0;">+ TẠO SẢN PHẨM MỚI</h1>
        <a href="index.php?module=admin&controller=product&action=index" style="color:#666; text-decoration:none; font-weight:500;">&larr; Về danh sách</a>
    </div>
    <hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="msg-box <?= (strpos($_GET['msg'], 'error') !== false) ? 'msg-error' : 'msg-success' ?>">
            <?= $_GET['msg'] ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <form method="GET" action="index.php">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="create">

            <label><b>1. Chọn danh mục sản phẩm:</b></label>
            <select name="cate_id" onchange="this.form.submit()" style="margin-left: 10px; min-width: 250px;">
                <option value="">-- Chọn danh mục --</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($selectedCateId) && $selectedCateId==$c['id']) ? 'selected' : '' ?>>
                        <?= $c['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if(!empty($selectedCateId)): ?>
        <form method="POST" action="index.php?module=admin&controller=product&action=store" enctype="multipart/form-data">
            
            <input type="hidden" name="cate_id" value="<?= $selectedCateId ?>">

            <div class="box">
                <h3>2. Thông tin chung</h3>
                <div style="display:flex; gap:20px; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 300px;">
                        <label>Tên Sản phẩm <span style="color:red">*</span>:</label><br>
                        <input type="text" name="name" required style="width: 100%" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="VD: iPhone 15 Pro Max 256GB"> 
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label>Thương hiệu <span style="color:red">*</span>:</label><br>
                        <select name="brand_id" required style="width: 100%">
                            <option value="">-- Chọn --</option>
                            <?php foreach($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= (isset($_POST['brand_id']) && $_POST['brand_id'] == $b['id']) ? 'selected' : '' ?>><?= $b['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 20px; display:flex; gap:20px;">
                    
                    <div style="width: 200px; text-align: center;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Thumbnail (Ảnh đại diện) <span style="color:red">*</span></label>
                        <div class="upload-area" onclick="document.getElementById('thumb-input').click()">
                            <span>📂 Chọn ảnh</span>
                            <input type="file" id="thumb-input" name="thumbnail" accept="image/*" style="display:none" onchange="previewThumb(this)">
                        </div>
                        <div id="thumb-container">
                            <img id="thumb-preview" src="">
                            <button type="button" class="btn-remove-img" onclick="removeThumb()">✕</button>
                        </div>
                    </div>
                    
                    <div style="flex:1;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Gallery (Bộ sưu tập) <span style="color:red">*</span></label>
                        <div class="upload-area" onclick="document.getElementById('gallery-input').click()">
                            <span>📂 Chọn nhiều ảnh (Ctrl + Click)</span>
                            <input type="file" id="gallery-input" name="gallery[]" accept="image/*" multiple style="display:none">
                        </div>
                        <div id="gallery-preview-box"></div>
                    </div>
                </div>
            </div>

            <div class="box" style="background:#e8f5e9;">
                <h3>3. Thông tin bán hàng</h3>
                <div style="display:flex; gap:15px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:150px;">
                        <label>Giá bán:</label><br>
                        <input type="text" name="price" required placeholder="0" class="money">
                    </div>
                    <div style="flex:1; min-width:150px;">
                        <label>Giá niêm yết:</label><br>
                        <input type="text" name="market_price" placeholder="0" class="money">
                    </div>
                    <div style="flex:1; min-width:150px;">
                        <label>Tồn kho:</label><br>
                        <input type="number" name="quantity" value="10" required>
                    </div>
                    <div style="flex:1; min-width:200px;">
                        <label>Trạng thái:</label><br>
                        <select name="status">
                            <option value="1">🟢 Đang bán</option>
                            <option value="0">⚪ Tạm ẩn (Nháp)</option>
                            <option value="-1">⚫ Ngừng kinh doanh</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="specs-container">
                <h3>4. Thông số kỹ thuật (Shared Specs)</h3>
                <?php if(!empty($template)): ?>
                    <?php foreach($template as $gIndex => $group): ?>
                        <div class="box">
                            <strong style="color:#1565c0; display:block; margin-bottom:15px; font-size: 1.1em;"><?= $group['group_name'] ?></strong>
                            <input type="hidden" name="spec_group[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>">
                            <div class="items-list">
                                <?php foreach($group['items'] as $iIndex => $item): ?>
                                    <div class="row-item">
                                        <button type="button" class="btn-del" onclick="removeRow(this)">✕</button>
                                        <input type="text" name="spec_item[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>" style="width:160px; background:#f5f5f5; color:#333;" readonly>
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][type][]" value="<?= $item['type'] ?>">
                                        
                                        <?php if($item['type'] == 'text'): ?>
                                            <input type="text" name="spec_item[<?= $gIndex ?>][value_text][]" style="flex:1" placeholder="Nhập giá trị..." required>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_id][]" value="">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="">
                                        <?php elseif($item['type'] == 'attribute'): ?>
                                            <?php 
                                                $attrId = $item['attribute_id'];
                                                $canCustom = isset($attrConfigs[$attrId]) && $attrConfigs[$attrId] == 1;
                                            ?>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="<?= $attrId ?>">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_text][]" value="">
                                            <select name="spec_item[<?= $gIndex ?>][value_id][]" style="width:200px" <?= (!$canCustom) ? 'required' : '' ?>>
                                                <option value="">-- Chọn --</option>
                                                <?php
                                                if($attrId && isset($allAttributeOptions[$attrId])){
                                                    foreach($allAttributeOptions[$attrId] as $opt) {
                                                        //logic check cho edit, với create thì  thường rỗng
                                                        $sel = (isset($val) && $opt['value']==$val) ? 'selected' : '';
                                                        echo "<option value = '{$opt['id']}' $sel> {$opt['value']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <?php if($canCustom): ?>
                                                <input type="text" name="spec_item[<?= $gIndex ?>][value_custom][]" style="flex:1" placeholder="Hoặc nhập tùy chỉnh...">
                                            <?php else: ?>
                                                <span class="badge-fixed">Fixed</span>
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn-add" onclick="addNewRow(this, <?= $gIndex ?>)">+ Thêm thông số khác</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="box"><i style="color:#666">Danh mục này chưa có cấu hình thông số kỹ thuật (Template).</i></div>
                <?php endif; ?>
            </div>

            <div style="text-align: right;">
                <button type="submit" name="btn_save_product" class="submit-btn">LƯU SẢN PHẨM</button>
            </div>
        </form>
    <?php endif; ?>

    <script>
        // JS GIỮ NGUYÊN
        function previewThumb(input) {
            const container = document.getElementById('thumb-container');
            const preview = document.getElementById('thumb-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function removeThumb() {
            document.getElementById('thumb-input').value = "";
            document.getElementById('thumb-container').style.display = 'none';
        }

        const galleryInput = document.getElementById('gallery-input');
        const galleryBox = document.getElementById('gallery-preview-box');
        const dt = new DataTransfer();

        if(galleryInput) {
            galleryInput.addEventListener('change', function() {
                for(let i = 0; i < this.files.length; i++){
                    dt.items.add(this.files[i]);
                }
                this.files = dt.files;
                renderGallery();
            });
        }

        function renderGallery() {
            galleryBox.innerHTML = '';
            for(let i = 0; i < dt.files.length; i++) {
                const file = dt.files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'gal-item';
                    div.innerHTML = `<img src="${e.target.result}"><button type="button" class="btn-remove-img" onclick="removeGalleryItem(${i})">✕</button>`;
                    galleryBox.appendChild(div);
                }
                reader.readAsDataURL(file);
            }
        }

        function removeGalleryItem(index) {
            dt.items.remove(index);
            galleryInput.files = dt.files;
            renderGallery();
        }

        function removeRow(btn) { btn.parentElement.remove(); }
        function addNewRow(btn, groupIndex) {
            const html = `<div class="row-item"><button type="button" class="btn-del" onclick="removeRow(this)">✕</button><input type="text" name="spec_item[${groupIndex}][name][]" placeholder="Tên thông số..." style="width:160px" required><input type="hidden" name="spec_item[${groupIndex}][type][]" value="text"><input type="text" name="spec_item[${groupIndex}][value_text][]" style="flex:1" placeholder="Nhập giá trị..." required><input type="hidden" name="spec_item[${groupIndex}][value_id][]" value=""><input type="hidden" name="spec_item[${groupIndex}][value_custom][]" value=""><input type="hidden" name="spec_item[${groupIndex}][attr_id][]" value=""></div>`;
            btn.previousElementSibling.insertAdjacentHTML('beforeend', html);
        }
        
        document.querySelectorAll('.money').forEach(inp => {
            inp.addEventListener('keyup', function() {
                let n = parseInt(this.value.replace(/\D/g,''), 10);
                this.value = isNaN(n) ? '' : n.toLocaleString('en-US');
            });
        });
    </script>
</body>
</html>