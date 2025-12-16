<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto; background-color: #f4f6f8; }
        .form-container { background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input[type=text], select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 5px; }
        .group-box { background: #e3f2fd; padding: 15px; margin-bottom: 15px; border: 1px solid #90caf9; border-radius: 5px; }
        .item-row { display: flex; align-items: center; gap: 10px; margin-top: 10px; background: white; padding: 8px; border-radius: 4px; border: 1px solid #eee; }
        .btn-save { background: #1976d2; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-add-group { background: #4caf50; color: white; padding: 8px 15px; border: none; cursor: pointer; border-radius: 4px; margin-bottom: 20px; }
        .btn-add-item { background: #ff9800; color: white; padding: 4px 10px; border: none; cursor: pointer; border-radius: 4px; font-size: 12px; }
        .btn-del { color: red; background: none; border: none; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <a href="index.php">← Dashboard</a>
    <h1>QUẢN LÝ DANH MỤC & TEMPLATE</h1>

    <div class="form-container">
        <h3><?= !empty($current['id']) ? "Chỉnh sửa: " . $current['name'] : "Tạo Danh mục Mới" ?></h3>
        
        <form method="POST" action="index.php?act=store_category">
            <input type="hidden" name="id" value="<?= $current['id'] ?? '' ?>">
            
            <div style="display:flex; gap: 20px;">
                <div style="flex:1">
                    <label>Tên Danh mục:</label><br>
                    <input type="text" name="name" value="<?= $current['name'] ?? '' ?>" required style="width:100%">
                </div>
                <div style="flex:1">
                    <label>Slug (URL):</label><br>
                    <input type="text" name="slug" value="<?= $current['slug'] ?? '' ?>" required style="width:100%">
                </div>
            </div>

            <hr>
            <h3>⚙️ Cấu hình Template (Thông số kỹ thuật)</h3>
            <div id="template-container">
                <?php 
                $jsGroupCount = 0;
                if (!empty($current['template'])): 
                    foreach ($current['template'] as $gIndex => $group): 
                        $jsGroupCount = max($jsGroupCount, $gIndex + 1);
                ?>
                    <div class="group-box" id="group-<?= $gIndex ?>">
                        <div style="display:flex; justify-content:space-between;">
                            <div><b>Nhóm:</b> <input type="text" name="groups[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>" style="font-weight:bold;"></div>
                            <button type="button" class="btn-del" onclick="removeElement('group-<?= $gIndex ?>')">✕ Xóa Nhóm</button>
                        </div>
                        <div class="items-list-<?= $gIndex ?>">
                            <?php foreach ($group['items'] as $iIndex => $item): ?>
                                <div class="item-row">
                                    <span>Tên:</span>
                                    <input type="text" name="items[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>">
                                    <span>Loại:</span>
                                    <select name="items[<?= $gIndex ?>][type][]" onchange="toggleAttr(this)">
                                        <option value="text" <?= $item['type']=='text'?'selected':'' ?>>Text thường</option>
                                        <option value="attribute" <?= $item['type']=='attribute'?'selected':'' ?>>🔗 Liên kết Attribute</option>
                                    </select>
                                    <select name="items[<?= $gIndex ?>][attr_id][]" style="display: <?= $item['type']=='attribute'?'inline-block':'none' ?>;">
                                        <option value="">-- Chọn --</option>
                                        <?php foreach($attrs as $a): ?>
                                            <option value="<?= $a['id'] ?>" <?= (isset($item['attribute_id']) && $item['attribute_id'] == $a['id']) ? 'selected' : '' ?>><?= $a['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn-del" onclick="this.parentElement.remove()">✕</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <br><button type="button" class="btn-add-item" onclick="addItem(<?= $gIndex ?>, this)">+ Thêm dòng</button>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <button type="button" class="btn-add-group" onclick="addGroup()">+ THÊM NHÓM</button>
            <br><br>
            <button type="submit" class="btn-save">LƯU DANH MỤC</button>
            <?php if(!empty($current['id'])): ?> <a href="index.php?act=category_list" style="margin-left:15px; color:red;">Hủy bỏ</a> <?php endif; ?>
        </form>
    </div>

    <h3>Danh sách hiện có:</h3>
    <table>
        <thead><tr><th>ID</th><th>Tên</th><th>Slug</th><th>Template</th><th>Hành động</th></tr></thead>
        <tbody>
            <?php foreach($list as $c): 
                $tpl = json_decode($c['spec_template'], true);
                $preview = $tpl ? count($tpl) . " nhóm" : "Trống";
            ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><b><?= $c['name'] ?></b></td>
                <td><?= $c['slug'] ?></td>
                <td><?= $preview ?></td>
                <td>
                    <a href="index.php?act=category_list&edit=<?= $c['id'] ?>" style="color:blue; margin-right:10px;">Sửa</a>
                    <a href="index.php?act=delete_category&id=<?= $c['id'] ?>" style="color:red;" onclick="return confirm('Xóa?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        const attributesList = <?php echo json_encode($attrs); ?>;
        let groupCounter = <?= isset($jsGroupCount) ? $jsGroupCount : 0 ?>;

        function addGroup() {
            const container = document.getElementById('template-container');
            const idx = groupCounter++;
            const html = `<div class="group-box" id="group-${idx}">
                    <div style="display:flex; justify-content:space-between;">
                        <div><b>Nhóm:</b> <input type="text" name="groups[${idx}]" placeholder="VD: Màn hình" required></div>
                        <button type="button" class="btn-del" onclick="removeElement('group-${idx}')">✕ Xóa Nhóm</button>
                    </div>
                    <div class="items-list-${idx}"></div>
                    <br><button type="button" class="btn-add-item" onclick="addItem(${idx}, this)">+ Thêm dòng</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function addItem(groupIdx, btn) {
            let attrOptions = '<option value="">-- Chọn Attribute --</option>';
            attributesList.forEach(attr => { attrOptions += `<option value="${attr.id}">${attr.name}</option>`; });
            const html = `<div class="item-row">
                    <span>Tên:</span> <input type="text" name="items[${groupIdx}][name][]" required>
                    <span>Loại:</span>
                    <select name="items[${groupIdx}][type][]" onchange="toggleAttr(this)">
                        <option value="text">Text thường</option>
                        <option value="attribute">🔗 Liên kết Attribute</option>
                    </select>
                    <select name="items[${groupIdx}][attr_id][]" style="display:none">${attrOptions}</select>
                    <button type="button" class="btn-del" onclick="this.parentElement.remove()">✕</button>
                </div>`;
            const listBox = btn.previousElementSibling.previousElementSibling;
            listBox.insertAdjacentHTML('beforeend', html);
        }

        function toggleAttr(select) { select.nextElementSibling.style.display = (select.value === 'attribute') ? 'inline-block' : 'none'; }
        function removeElement(id) { if(confirm('Xóa nhóm này?')) document.getElementById(id).remove(); }
    </script>
</body>
</html>