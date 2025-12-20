<?php
    // 1. Xác định chế độ Edit
    $isEdit = isset($rowProd) && $rowProd;

    // 2. [QUAN TRỌNG] Chuẩn hóa biến $selectedCateId cho cả 2 trường hợp
    // - Nếu Edit: Lấy từ dữ liệu sản phẩm ($rowProd)
    // - Nếu Create: Lấy từ biến Controller truyền sang (hoặc mặc định 0)
    if ($isEdit) {
        $selectedCateId = $rowProd['category_id'];
    } else {
        $selectedCateId = isset($selectedCateId) ? $selectedCateId : 0;
    }
    
    // 3. Thiết lập tiêu đề & Action
    $pageTitle = $isEdit ? "Sửa sản phẩm: ". htmlspecialchars($rowProd['name']) : "Tạo sản phẩm mới";
    $formAction = $isEdit ? "index.php?module=admin&controller=product&action=update&id=".$rowProd['id'] :
                            "index.php?module=admin&controller=product&action=store";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <style>
        /* CSS giữ nguyên như cũ */
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background-color: #f4f6f8; color: #333; }
        .box { background:#fff; padding:25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom:20px; }
        h1 { margin-top: 0; color: #1a237e; }
        .row-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
        .btn-del { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; cursor: pointer; border-radius: 4px; width: 30px; height: 30px; font-weight: bold;}
        .btn-add { background: #e8f5e9; color: #2e7d32; border: 1px dashed #a5d6a7; cursor: pointer; padding: 10px; width: 100%; font-weight: 500; transition: 0.2s; border-radius: 4px; margin-top: 10px;}
        .upload-area { border: 2px dashed #ccc; padding: 20px; text-align: center; background: #fafafa; border-radius: 6px; cursor: pointer; transition: 0.2s; position: relative; }
        .submit-btn { padding:15px 40px; background:#1976d2; color:white; font-weight:bold; border:none; border-radius:5px; cursor:pointer; font-size: 16px; box-shadow: 0 2px 5px rgba(25, 118, 210, 0.3); transition: 0.2s; width: 100%; }
        .btn-remove-img { position: absolute; top: 0; right: 0; background: rgba(255,0,0,0.8); color: white; width: 20px; height: 20px; border: none; cursor: pointer; }
        .badge-fixed { background: #78909c; color: white; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        #gallery-preview-box { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .gal-item { width: 80px; height: 80px; border: 1px solid #ddd; position: relative; border-radius: 4px; overflow: hidden; }
        .gal-item img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>
    
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1 style='color:#1a237e; margin:0;'><?= $pageTitle ?></h1>
        <a href="index.php?module=admin&controller=product&action=index" style="color:#666; text-decoration:none; font-weight:500;">&larr; Về danh sách</a>
    </div>
    <hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="msg-box" style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;">
            <?= $_GET['msg'] ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <?php if(!$isEdit): ?>
        <form method="GET" action="index.php">
            
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="create">

            <label><b>1. Chọn danh mục sản phẩm:</b></label>
            <select name="cate_id" onchange="this.form.submit()" style="margin-left: 10px; min-width: 250px; padding: 5px;">
                <option value="">-- Chọn danh mục --</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($selectedCateId) && $selectedCateId==$c['id']) ? 'selected' : '' ?>>
                        <?= $c['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php else: ?>
            <p><b>Danh mục:</b> <?= $rowProd['category_id'] // Hoặc lấy tên danh mục nếu có join ?></p>
        <?php endif; ?>
    </div>

    <?php if(!empty($selectedCateId) || $isEdit): ?>
        <form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data">
            
            <input type="hidden" name="cate_id" value="<?= $selectedCateId ?>">

            <div class="box">
                <h3>2. Thông tin chung</h3>
                <div style="display:flex; gap:20px; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 300px;">
                        <label>Tên Sản phẩm <span style="color:red">*</span>:</label><br>
                        <input type="text" name="name" required style="width: 100%; padding: 8px;" 
                               value="<?= htmlspecialchars($rowProd['name'] ?? $_POST['name'] ?? '') ?>" placeholder="VD: iPhone 15 Pro Max"> 
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label>Thương hiệu <span style="color:red">*</span>:</label><br>
                        <select name="brand_id" required style="width: 100%; padding: 8px;">
                            <option value="">-- Chọn --</option>
                            <?php foreach($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= (isset($rowProd) && $rowProd['brand_id'] == $b['id']) ? 'selected' : '' ?>><?= $b['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 20px; display:flex; gap:20px;">
                    <div style="width: 200px; text-align: center;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Thumbnail</label>
                        
                        <?php if($isEdit && !empty($rowProd['thumbnail'])): ?>
                            <img src="<?= $rowProd['thumbnail'] ?>" style="width:100px; margin-bottom:5px; border:1px solid #eee;">
                        <?php endif; ?>

                        <div class="upload-area" onclick="document.getElementById('thumb-input').click()">
                            <span>📂 Chọn ảnh</span>
                            <input type="file" id="thumb-input" name="thumbnail" accept="image/*" style="display:none" onchange="previewThumb(this)">
                        </div>
                        <div id="thumb-container" style="display:none; margin-top:10px;">
                            <img id="thumb-preview" src="" style="width:100px;">
                            <button type="button" class="btn-remove-img" onclick="removeThumb()">✕</button>
                        </div>
                    </div>
                    
                    <div style="flex:1;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Gallery</label>
                        
                        <?php if($isEdit && !empty($gallery)): ?>
                            <div style="display:flex; gap:5px; margin-bottom:10px;">
                                <?php foreach($gallery as $img): ?>
                                    <div style="position:relative;">
                                        <img src="<?= $img['image_url'] ?>" style="width:60px; height:60px; object-fit:cover; border:1px solid #ddd;">
                                        <a href="index.php?module=admin&controller=product&action=deleteImage&del_img=<?= $img['id'] ?>&id=<?= $rowProd['id'] ?>" onclick="return confirm('Xóa ảnh?')" class="btn-remove-img">✕</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="upload-area" onclick="document.getElementById('gallery-input').click()">
                            <span>📂 Chọn thêm ảnh (Ctrl + Click)</span>
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
                        <input type="text" name="price" value="<?= number_format($rowProd['price'] ?? 0) ?>" required class="money" style="padding: 8px; width: 100%;">
                    </div>
                    <div style="flex:1; min-width:150px;">
                        <label>Giá niêm yết:</label><br>
                        <input type="text" name="market_price" value="<?= number_format($rowProd['market_price'] ?? 0) ?>" class="money" style="padding: 8px; width: 100%;">
                    </div>
                    <div style="flex:1; min-width:150px;">
                        <label>Tồn kho:</label><br>
                        <input type="number" name="quantity" value="<?= $rowProd['quantity'] ?? 10 ?>" required style="padding: 8px; width: 100%;">
                    </div>
                    <div style="flex:1; min-width:200px;">
                        <label>Trạng thái:</label><br>
                        <select name="status" style="padding: 8px; width: 100%;">
                            <option value="1" <?= (isset($rowProd) && $rowProd['status']==1) ? 'selected' : '' ?>>🟢 Đang bán</option>
                            <option value="0" <?= (isset($rowProd) && $rowProd['status']==0) ? 'selected' : '' ?>>⚪ Tạm ẩn</option>
                            <option value="-1" <?= (isset($rowProd) && $rowProd['status']==-1) ? 'selected' : '' ?>>⚫ Ngừng KD</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="specs-container">
                <h3>4. Thông số kỹ thuật (Shared Specs)</h3>
                
                <?php 
                // Xác định dữ liệu specs để hiển thị
                $specsData = [];
                if ($isEdit && isset($currentSpecs)) {
                    $specsData = $currentSpecs; // Dùng dữ liệu JSON cũ khi sửa
                } elseif (!empty($template)) {
                    $specsData = $template; // Dùng template khi tạo mới
                }
                ?>

                <?php if(!empty($specsData)): ?>
                    <?php foreach($specsData as $gIndex => $group): ?>
                        <div class="box">
                            <strong style="color:#1565c0; display:block; margin-bottom:15px; font-size: 1.1em;"><?= $group['group_name'] ?></strong>
                            <input type="hidden" name="spec_group[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>">
                            <div class="items-list">
                                <?php foreach($group['items'] as $iIndex => $item): ?>
                                    <div class="row-item">
                                        <button type="button" class="btn-del" onclick="removeRow(this)">✕</button>
                                        <input type="text" name="spec_item[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>" style="width:160px; background:#f5f5f5; color:#333;" readonly>
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][type][]" value="<?= $item['type'] ?>">
                                        
                                        <?php 
                                            // [ĐÃ SỬA LỖI 3] Khai báo biến $val để dropdown nhận diện giá trị cũ
                                            $val = $item['value'] ?? ''; 
                                        ?>

                                        <?php if($item['type'] == 'text'): ?>
                                            <input type="text" name="spec_item[<?= $gIndex ?>][value_text][]" value="<?= $val ?>" style="flex:1" placeholder="Nhập giá trị..." required>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_id][]" value="">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="">
                                        <?php elseif($item['type'] == 'attribute'): ?>
                                            <?php 
                                                $attrId = $item['attribute_id'] ?? $item['attr_id'] ?? 0;
                                                $canCustom = isset($attrConfigs[$attrId]) && $attrConfigs[$attrId] == 1;
                                            ?>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="<?= $attrId ?>">
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_text][]" value="">
                                            
                                            <select name="spec_item[<?= $gIndex ?>][value_id][]" style="width:200px" <?= (!$canCustom) ? 'required' : '' ?>>
                                                <option value="">-- Chọn --</option>
                                                <?php
                                                // [CHUẨN MVC] Dùng biến $allAttributeOptions truyền từ Controller
                                                if($attrId && isset($allAttributeOptions[$attrId])){
                                                    foreach($allAttributeOptions[$attrId] as $opt) {
                                                        $sel = ($val == $opt['value']) ? 'selected' : '';
                                                        echo "<option value='{$opt['id']}' $sel>{$opt['value']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            
                                            <?php if($canCustom): ?>
                                                <input type="text" name="spec_item[<?= $gIndex ?>][value_custom][]" value="<?= ($val) ?>" style="flex:1" placeholder="Hoặc nhập tùy chỉnh...">
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
                    <div class="box"><i style="color:#666">Chưa có thông số mẫu.</i></div>
                <?php endif; ?>
            </div>

            <div style="text-align: right;">
                <button type="submit" name="<?= $isEdit ? 'btn_update' : 'btn_save_product' ?>" class="submit-btn">
                    <?= $isEdit ? "CẬP NHẬT SẢN PHẨM" : "LƯU SẢN PHẨM" ?>
                </button>
            </div>
        </form>
    <?php endif; ?>

    <script>
        // JS XỬ LÝ ẢNH & SPECS (GIỮ NGUYÊN)
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