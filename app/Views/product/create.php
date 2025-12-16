<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Sản Phẩm Gốc</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background-color: #f0f2f5; }
        .box { background:#fff; padding:20px; border-radius: 8px; margin-bottom:20px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .row-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
        .btn-add { background: #e8f5e9; color: #2e7d32; border: 1px dashed #a5d6a7; cursor: pointer; padding: 10px; width: 100%; }
        input[type=text], select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .image-box { width: 80px; height: 80px; border: 1px solid #ddd; display: inline-block; margin-right: 10px; overflow: hidden; }
        .image-box img { width: 100%; height: 100%; object-fit: cover; }
        .badge-fixed { background: #607d8b; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
        .btn-submit { padding:15px 40px; background:#1976d2; color:white; font-weight:bold; border:none; border-radius:5px; cursor:pointer; }
    </style>
</head>
<body>
    <a href="index.php">← Dashboard</a>
    <h1>TẠO SẢN PHẨM CHA (GỐC)</h1>

    <div class="box">
        <label><b>Chọn Loại sản phẩm:</b></label>
        <select name="cate_id" onchange="window.location.href='index.php?act=create_product&cate_id='+this.value" style="margin-left: 10px; min-width: 200px;">
            <option value="">-- Chọn danh mục --</option>
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $selectedCateId==$c['id']?'selected':'' ?>><?= $c['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if($selectedCateId): ?>
        <form method="POST" action="index.php?act=store_product&cate_id=<?= $selectedCateId ?>" enctype="multipart/form-data">
            
            <div class="box">
                <h3>1. Thông tin chung</h3>
                <div style="display:flex; gap:15px;">
                    <div style="flex:2">
                        <label>Tên Sản phẩm:</label><br>
                        <input type="text" name="name" required style="width: 100%" placeholder="VD: iPhone 15 Pro Max"> 
                    </div>
                    <div style="flex:1">
                        <label>SKU Gốc:</label><br>
                        <input type="text" name="sku" required style="width: 100%" placeholder="VD: IP15PM">
                    </div>
                </div>
            </div>

            <div class="box">
                <h3>2. Hình ảnh (Chọn nhiều)</h3>
                <input type="file" id="product_images" name="product_images[]" multiple accept="image/*" onchange="previewImages(event)">
                <div id="image-preview-container" style="margin-top:10px;"></div>
            </div>

            <div id="specs-container">
                <h3>3. Thông số kỹ thuật</h3>
                <?php foreach($template as $gIndex => $group): ?>
                    <div class="box">
                        <strong style="color:#1976d2; display:block; margin-bottom:10px;"><?= $group['group_name'] ?></strong>
                        <input type="hidden" name="spec_group[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>">
                        
                        <div class="items-list">
                            <?php foreach($group['items'] as $iIndex => $item): ?>
                                <div class="row-item">
                                    <button type="button" onclick="this.parentElement.remove()" style="color:red; border:none; background:none; cursor:pointer;">✕</button>
                                    
                                    <input type="text" name="spec_item[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>" style="width:160px; background:#f5f5f5;" readonly>
                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][type][]" value="<?= $item['type'] ?>">

                                    <?php if($item['type'] == 'text'): ?>
                                        <input type="text" name="spec_item[<?= $gIndex ?>][value_text][]" style="flex:1" placeholder="Nhập giá trị...">
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][value_id][]" value="">
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="">
                                    
                                    <?php elseif($item['type'] == 'attribute'): ?>
                                        <?php 
                                            $attrId = $item['attribute_id'];
                                            // Kiểm tra xem attr này có cho custom không (từ biến attrConfigs truyền từ Controller)
                                            $canCustom = isset($attrConfigs[$attrId]) && $attrConfigs[$attrId]['is_customizable'] == 1;
                                        ?>
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="<?= $attrId ?>">
                                        <input type="hidden" name="spec_item[<?= $gIndex ?>][value_text][]" value="">

                                        <select name="spec_item[<?= $gIndex ?>][value_id][]" style="width:200px">
                                            <option value="">-- Chọn --</option>
                                            <?php 
                                                // Lấy options của attr này từ $attrConfigs
                                                if(isset($attrConfigs[$attrId]['options'])) {
                                                    foreach($attrConfigs[$attrId]['options'] as $opt){
                                                        echo "<option value='{$opt['id']}'>{$opt['value']}</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                        
                                        <?php if($canCustom): ?>
                                            <input type="text" name="spec_item[<?= $gIndex ?>][value_custom][]" style="flex:1" placeholder="Tên tùy chỉnh...">
                                        <?php else: ?>
                                            <span class="badge-fixed" style="margin-left:5px;">Fixed</span>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn-add" onclick="addNewRow(this, <?= $gIndex ?>)">+ Thêm dòng</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" name="btn_save_product" class="btn-submit">LƯU SẢN PHẨM</button>
        </form>
    <?php endif; ?>

    <script>
        function previewImages(event) {
            const container = document.getElementById('image-preview-container');
            container.innerHTML = '';
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    container.innerHTML += `<div class="image-box"><img src="${e.target.result}"></div>`;
                }
                reader.readAsDataURL(files[i]);
            }
        }

        function addNewRow(btn, groupIndex) {
            const html = `
                <div class="row-item">
                    <button type="button" onclick="this.parentElement.remove()" style="color:red; border:none; background:none;">✕</button>
                    <input type="text" name="spec_item[${groupIndex}][name][]" placeholder="Tên thông số..." style="width:160px">
                    <input type="hidden" name="spec_item[${groupIndex}][type][]" value="text">
                    <input type="text" name="spec_item[${groupIndex}][value_text][]" style="flex:1" placeholder="Giá trị...">
                    <input type="hidden" name="spec_item[${groupIndex}][value_id][]" value="">
                    <input type="hidden" name="spec_item[${groupIndex}][value_custom][]" value="">
                    <input type="hidden" name="spec_item[${groupIndex}][attr_id][]" value="">
                </div>`;
            btn.previousElementSibling.insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>