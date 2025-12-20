<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <style>
        /* GIỮ NGUYÊN CSS CŨ CỦA BẠN */
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background-color: #f4f6f8; color: #333; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
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

        .upload-area { border: 2px dashed #ccc; padding: 20px; text-align: center; background: #fafafa; border-radius: 6px; cursor: pointer; transition: 0.2s; position: relative; margin-top: 10px; }
        .upload-area:hover { border-color: #1976d2; background: #e3f2fd; }
        
        #thumb-container-new { position: relative; width: 120px; height: 120px; margin: 10px auto; display: none; border: 1px solid #ddd; padding: 2px; background: white; }
        #thumb-preview-new { width: 100%; height: 100%; object-fit: contain; }
        
        .gal-wrap { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .gal-item { width: 80px; height: 80px; border: 1px solid #ddd; position: relative; border-radius: 4px; overflow: hidden; background: #fff; }
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
        .old-img-section { border: 1px solid #eee; padding: 10px; border-radius: 4px; margin-bottom: 10px; background: #fdfdfd; }
    </style>
</head>
<body>

    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1 style="color:#1a237e; margin:0;">✏️ SỬA SẢN PHẨM: <span style="color:#1565c0"><?= htmlspecialchars($rowProd['name']) ?></span></h1>
        
        <a href="index.php?module=admin&controller=product&action=index" style="color:#666; text-decoration:none; font-weight:500;">&larr; Về danh sách</a>
    </div>
    <hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">

    <?php if(isset($_GET['msg'])): ?>
        <div class="msg-box <?= (strpos($_GET['msg'], 'error') !== false) ? 'msg-error' : 'msg-success' ?>">
            <?php 
                if($_GET['msg'] == 'updated') echo "✅ Cập nhật thành công!";
                elseif($_GET['msg'] == 'cloned') echo "📋 Đã nhân bản! Hãy cập nhật thông tin.";
                else echo $_GET['msg'];
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?module=admin&controller=product&action=update&id=<?= $rowProd['id'] ?>" enctype="multipart/form-data">
        
        <div class="box">
            <h3>1. Thông tin chung</h3>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div style="flex:2; min-width:300px;">
                    <label>Tên Sản phẩm <span style="color:red">*</span>:</label><br>
                    <input type="text" name="name" value="<?= htmlspecialchars($rowProd['name']) ?>" required style="width:100%">
                    <small style="color:#666; display:block; margin-top:5px;">Slug hiện tại: <i><?= $rowProd['slug'] ?></i> (Sẽ tự đổi nếu sửa tên)</small>
                </div>
                <div style="flex:1; min-width:200px;">
                    <label>Thương hiệu <span style="color:red">*</span>:</label><br>
                    <select name="brand_id" required style="width:100%">
                        <?php foreach($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $rowProd['brand_id']==$b['id']?'selected':'' ?>><?= $b['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="cate_id" value="<?= $rowProd['category_id'] ?>">
            </div>

            <div style="margin-top:25px; padding-top:20px; border-top:1px dashed #ddd;">
                <div style="display:flex; gap:30px; flex-wrap:wrap;">
                    
                    <div style="width: 220px; text-align: center;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Thumbnail (Ảnh đại diện)</label>
                        
                        <div class="old-img-section">
                            <small>Hiện tại:</small><br>
                            <img src="<?= $rowProd['thumbnail'] ?>" style="height:100px; width:100px; object-fit:contain; border:1px solid #ddd; margin-top:5px;">
                        </div>

                        <div class="upload-area" onclick="document.getElementById('thumb-input').click()">
                            <span>📂 Thay ảnh mới</span>
                            <input type="file" id="thumb-input" name="thumbnail" accept="image/*" style="display:none" onchange="previewThumb(this)">
                        </div>
                        
                        <div id="thumb-container-new">
                            <img id="thumb-preview-new" src="">
                            <button type="button" class="btn-remove-img" onclick="removeThumb()">✕</button>
                        </div>
                    </div>
                    
                    <div style="flex:1;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Gallery (Bộ sưu tập)</label>
                        
                        <div class="gal-wrap" style="margin-bottom: 20px;">
                            <?php if(!empty($gallery)): ?>
                                <?php foreach($gallery as $img): ?>
                                    <div class="gal-item" title="Ảnh hiện có">
                                        <img src="<?= $img['image_url'] ?>">
                                        <a href="index.php?module=admin&controller=product&action=deleteImage&del_img=<?= $img['id'] ?>&id=<?= $rowProd['id'] ?>" class="btn-remove-img" onclick="return confirm('Bạn chắc chắn muốn xóa ảnh này khỏi hệ thống?')">✕</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <small style="color:#999; font-style:italic;">Chưa có ảnh nào trong bộ sưu tập.</small>
                            <?php endif; ?>
                        </div>

                        <div style="border-top: 1px solid #eee; padding-top: 10px;">
                            <label style="font-size: 13px; color: #555;">Thêm ảnh mới:</label>
                            <div class="upload-area" onclick="document.getElementById('gallery-input').click()">
                                <span>📂 Chọn nhiều ảnh (Ctrl + Click)</span>
                                <input type="file" id="gallery-input" name="gallery[]" accept="image/*" multiple style="display:none">
                            </div>
                            <div id="gallery-preview-box" class="gal-wrap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box" style="background:#e8f5e9;">
            <h3>2. Thông tin bán hàng</h3>
            <div style="display:flex; gap:15px; flex-wrap:wrap;">
                <div style="flex:1; min-width:150px;">
                    <label>Giá bán:</label><br>
                    <input type="text" name="price" value="<?= number_format($rowProd['price']) ?>" style="font-weight:bold; color:#d32f2f;" class="money">
                </div>
                <div style="flex:1; min-width:150px;">
                    <label>Giá niêm yết:</label><br>
                    <input type="text" name="market_price" value="<?= number_format($rowProd['market_price']) ?>" class="money">
                </div>
                <div style="flex:1; min-width:150px;">
                    <label>Tồn kho:</label><br>
                    <input type="number" name="quantity" value="<?= $rowProd['quantity'] ?>">
                </div>
                <div style="flex:1; min-width:200px;">
                    <label>Trạng thái:</label><br>
                    <select name="status">
                        <option value="1" <?= $rowProd['status']==1?'selected':'' ?>>🟢 Đang bán</option>
                        <option value="0" <?= $rowProd['status']==0?'selected':'' ?>>⚪ Tạm ẩn (Nháp)</option>
                        <option value="-1" <?= $rowProd['status']==-1?'selected':'' ?>>⚫ Ngừng kinh doanh</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="box">
            <h3>3. Thông số kỹ thuật (Cập nhật sẽ đồng bộ)</h3>
            <?php foreach($currentSpecs as $gIndex => $group): ?>
                <div style="margin-bottom:20px; padding:15px; border-radius:6px; border:1px solid #eee;">
                    <strong style="color:#1565c0; display:block; margin-bottom:15px; font-size:1.1em;"><?= $group['group_name'] ?></strong>
                    <input type="hidden" name="spec_group[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>">
                    
                    <div class="items-list">
                        <?php foreach($group['items'] as $iIndex => $item): ?>
                            <div class="row-item">
                                <button type="button" class="btn-del" onclick="removeRow(this)">✕</button>
                                <input type="text" name="spec_item[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>" style="width:160px; background:#f5f5f5;" readonly>
                                
                                <?php 
                                    // Logic Template & Config (Giữ nguyên)
                                    // [SỬA QUAN TRỌNG] Vì View này được include, biến $catTemplate được truyền từ Controller.
                                    // Chúng ta cần hàm findTemplateDef hoặc logic tương tự. 
                                    // Để đơn giản, tôi sẽ viết logic tìm inline ở đây.
                                    $tplDef = null;
                                    if(!empty($catTemplate)) {
                                        foreach($catTemplate as $grpT) {
                                            foreach($grpT['items'] as $itmT) {
                                                if (mb_strtolower(trim($itmT['name'])) == mb_strtolower(trim($item['name']))) {
                                                    $tplDef = $itmT; break 2;
                                                }
                                            }
                                        }
                                    }

                                    $type = $tplDef ? $tplDef['type'] : ($item['type'] ?? 'text');
                                    $attrId = $tplDef ? ($tplDef['attribute_id'] ?? 0) : ($item['attr_id'] ?? 0);
                                    $val  = $item['value'];
                                ?>
                                <input type="hidden" name="spec_item[<?= $gIndex ?>][type][]" value="<?= $type ?>">

                                <?php if($type == 'text'): ?>
                                    <input type="text" name="spec_item[<?= $gIndex ?>][value_text][]" value="<?= $val ?>" style="flex:1">
                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][value_id][]" value="">
                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="">
                                <?php else: ?>
                                    <?php $canCustom = isset($attrConfigs[$attrId]) && $attrConfigs[$attrId] == 1; ?>
                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="<?= $attrId ?>">
                                    <select name="spec_item[<?= $gIndex ?>][value_id][]" style="width:200px">
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
                                        <input type="text" name="spec_item[<?= $gIndex ?>][value_custom][]" value="<?= $val ?>" style="flex:1" placeholder="Hoặc nhập tay...">
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
        </div>

        <div style="text-align:right;">
            <button type="submit" name="btn_update" class="submit-btn">LƯU CẬP NHẬT</button>
        </div>
    </form>

    <script>
        // JS XỬ LÝ ẢNH & SPECS (GIỮ NGUYÊN)
        function previewThumb(input) {
            const container = document.getElementById('thumb-container-new');
            const preview = document.getElementById('thumb-preview-new');
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
            document.getElementById('thumb-container-new').style.display = 'none';
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
                    div.innerHTML = `
                        <img src="${e.target.result}">
                        <button type="button" class="btn-remove-img" onclick="removeGalleryItem(${i})">✕</button>
                    `;
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
            const html = `
                <div class="row-item">
                    <button type="button" class="btn-del" onclick="removeRow(this)">✕</button>
                    <input type="text" name="spec_item[${groupIndex}][name][]" placeholder="Tên thông số..." style="width:160px" required>
                    <input type="hidden" name="spec_item[${groupIndex}][type][]" value="text">
                    <input type="text" name="spec_item[${groupIndex}][value_text][]" style="flex:1" placeholder="Nhập giá trị..." required>
                    <input type="hidden" name="spec_item[${groupIndex}][value_id][]" value="">
                    <input type="hidden" name="spec_item[${groupIndex}][value_custom][]" value="">
                    <input type="hidden" name="spec_item[${groupIndex}][attr_id][]" value="">
                </div>`;
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