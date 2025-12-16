<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $currentData['id'] ? 'Chỉnh sửa' : 'Thêm mới' ?> Danh mục</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f6f8; max-width: 1000px; margin: 0 auto; }
        .form-container { background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; }
        
        /* Input Styles */
        input[type=text], select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        input[type=text]:focus { border-color: #1976d2; outline: none; }
        
        /* Template Builder Styles */
        .group-box { background: #e3f2fd; padding: 15px; margin-bottom: 15px; border: 1px solid #90caf9; border-radius: 5px; position: relative; }
        .item-row { display: flex; align-items: center; gap: 10px; margin-top: 10px; background: white; padding: 10px; border-radius: 4px; border: 1px solid #eee; }
        
        /* Buttons */
        .btn-save { background: #1976d2; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background: #1565c0; }
        
        .btn-cancel { color: #d32f2f; text-decoration: none; margin-left: 15px; font-weight: bold; }
        
        .btn-add-group { background: #4caf50; color: white; padding: 8px 15px; border: none; cursor: pointer; border-radius: 4px; margin-bottom: 20px; font-weight: bold;}
        .btn-add-item { background: #ff9800; color: white; padding: 5px 10px; border: none; cursor: pointer; border-radius: 4px; font-size: 12px; }
        .btn-del { color: #d32f2f; background: none; border: none; cursor: pointer; font-weight: bold; }
        .btn-del:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?= $currentData['id'] ? "Chỉnh sửa: " . htmlspecialchars($currentData['name']) : "Tạo Danh mục Mới" ?></h2>

    <form method="POST" action="index.php?controller=category&action=save">
        
        <input type="hidden" name="id" value="<?= $currentData['id'] ?>">
        
        <div style="display:flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex:1">
                <label><b>Tên Danh mục:</b></label><br>
                <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" required style="width:100%; margin-top:5px;">
            </div>
            <div style="flex:1">
                <label><b>Slug (URL):</b></label><br>
                <input type="text" name="slug" value="<?= htmlspecialchars($currentData['slug']) ?>" placeholder="Để trống sẽ tự tạo" style="width:100%; margin-top:5px;">
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h3>⚙️ Cấu hình Thông số kỹ thuật</h3>
        <p style="color:#666; font-size: 0.9em; margin-bottom: 15px;">Xây dựng các nhóm thông số cho sản phẩm (VD: Màn hình, Camera...).</p>
        
        <div id="template-container">
            <?php 
            // Biến đếm để JS tiếp tục đánh số thứ tự, tránh trùng ID
            $jsGroupCount = 0; 
            
            // Nếu đang Sửa (có dữ liệu cũ), loop ra để hiển thị
            if (!empty($currentData['template'])): 
                foreach ($currentData['template'] as $gIndex => $group): 
                    $jsGroupCount = max($jsGroupCount, $gIndex + 1);
            ?>
                <div class="group-box" id="group-<?= $gIndex ?>">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="flex-grow: 1;">
                            <b>Nhóm:</b> 
                            <input type="text" name="groups[<?= $gIndex ?>]" value="<?= htmlspecialchars($group['group_name']) ?>" placeholder="Tên nhóm (VD: Màn hình)" required style="width: 70%; font-weight: bold;">
                        </div>
                        <button type="button" class="btn-del" onclick="removeElement('group-<?= $gIndex ?>')">✕ Xóa Nhóm</button>
                    </div>
                    
                    <div class="items-list-<?= $gIndex ?>">
                        <?php if(isset($group['items']) && is_array($group['items'])): ?>
                            <?php foreach ($group['items'] as $item): ?>
                                <div class="item-row">
                                    <span>Tên:</span>
                                    <input type="text" name="items[<?= $gIndex ?>][name][]" value="<?= htmlspecialchars($item['name']) ?>" required>
                                    
                                    <span>Loại:</span>
                                    <select name="items[<?= $gIndex ?>][type][]" onchange="toggleAttr(this)">
                                        <option value="text" <?= $item['type']=='text'?'selected':'' ?>>Text thường</option>
                                        <option value="attribute" <?= $item['type']=='attribute'?'selected':'' ?>>🔗 Liên kết Attribute</option>
                                    </select>
                                    
                                    <select name="items[<?= $gIndex ?>][attr_id][]" style="display: <?= $item['type']=='attribute'?'inline-block':'none' ?>;">
                                        <option value="">-- Chọn Attribute --</option>
                                        <?php foreach($attrs as $a): ?>
                                            <option value="<?= $a['id'] ?>" <?= (isset($item['attribute_id']) && $item['attribute_id'] == $a['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($a['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <button type="button" class="btn-del" onclick="this.parentElement.remove()">✕</button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin-top:10px;">
                        <button type="button" class="btn-add-item" onclick="addItem(<?= $gIndex ?>, this)">+ Thêm dòng thông số</button>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <button type="button" class="btn-add-group" onclick="addGroup()">+ THÊM NHÓM MỚI</button>
        <br><br><br>
        
        <div style="border-top: 1px solid #ddd; padding-top: 20px;">
            <button type="submit" class="btn-save">
                <?= $currentData['id'] ? "LƯU CẬP NHẬT" : "TẠO DANH MỤC" ?>
            </button>
            <a href="index.php?controller=category&action=index" class="btn-cancel">Hủy bỏ</a>
        </div>
    </form>
</div>

<script>
    // 1. Nhận dữ liệu attributes từ PHP controller để dùng cho nút "Thêm dòng"
    const attributesList = <?php echo json_encode($attrs); ?>;
    
    // 2. Tiếp tục đếm từ số lượng group đã có
    let groupCounter = <?= isset($jsGroupCount) ? $jsGroupCount : 0 ?>;

    // Hàm thêm Nhóm (Group)
    function addGroup() {
        const container = document.getElementById('template-container');
        const idx = groupCounter++;
        
        const html = `
            <div class="group-box" id="group-${idx}">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="flex-grow: 1;">
                        <b>Nhóm:</b> 
                        <input type="text" name="groups[${idx}]" placeholder="Tên nhóm (VD: Camera)" required style="width: 70%; font-weight: bold;">
                    </div>
                    <button type="button" class="btn-del" onclick="removeElement('group-${idx}')">✕ Xóa Nhóm</button>
                </div>
                <div class="items-list-${idx}"></div>
                <div style="margin-top:10px;">
                    <button type="button" class="btn-add-item" onclick="addItem(${idx}, this)">+ Thêm dòng thông số</button>
                </div>
            </div>`;
        
        container.insertAdjacentHTML('beforeend', html);
    }

    // Hàm thêm Dòng (Item) vào trong Nhóm
    function addItem(groupIdx, btn) {
        // Tạo options cho select attribute từ biến attributesList
        let attrOptions = '<option value="">-- Chọn Attribute --</option>';
        if (attributesList && attributesList.length > 0) {
            attributesList.forEach(attr => {
                attrOptions += `<option value="${attr.id}">${attr.name}</option>`;
            });
        }

        const html = `
            <div class="item-row">
                <span>Tên:</span> 
                <input type="text" name="items[${groupIdx}][name][]" placeholder="VD: Độ phân giải" required>
                
                <span>Loại:</span>
                <select name="items[${groupIdx}][type][]" onchange="toggleAttr(this)">
                    <option value="text">Text thường</option>
                    <option value="attribute">🔗 Liên kết Attribute</option>
                </select>
                
                <select name="items[${groupIdx}][attr_id][]" style="display:none">
                    ${attrOptions}
                </select>
                
                <button type="button" class="btn-del" onclick="this.parentElement.remove()">✕</button>
            </div>`;
        
        // Tìm div chứa list (nằm trước cái div chứa nút bấm)
        const itemsListDiv = btn.parentElement.previousElementSibling;
        itemsListDiv.insertAdjacentHTML('beforeend', html);
    }

    // Hàm ẩn/hiện dropdown Attribute
    function toggleAttr(select) {
        const attrSelect = select.nextElementSibling;
        attrSelect.style.display = (select.value === 'attribute') ? 'inline-block' : 'none';
    }

    // Hàm xóa phần tử
    function removeElement(id) {
        if(confirm('Bạn có chắc muốn xóa nhóm này?')) {
            document.getElementById(id).remove();
        }
    }
</script>

</body>
</html>