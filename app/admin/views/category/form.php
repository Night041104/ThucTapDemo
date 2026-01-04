<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">
        <?= $currentData['id'] ? "Chỉnh sửa Danh mục" : "Tạo Danh mục mới" ?>
    </h3>
    <a href="admin/category" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if(isset($msg) && $msg): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<form method="POST" action="admin/category/save">
    <input type="hidden" name="id" value="<?= $currentData['id'] ?>">

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-info-circle me-2"></i>Thông tin chung</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" 
                               class="form-control" required placeholder="VD: Điện thoại, Laptop...">
                    </div>
                    
                    <div class="alert alert-light border small text-muted">
                        <i class="fa fa-lightbulb text-warning me-1"></i> 
                        Slug sẽ được tự động tạo từ tên nếu để trống.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-success"><i class="fa fa-cogs me-2"></i>Cấu hình Template Thông số</h6>
                        <small class="text-muted">Định nghĩa các trường thông số kỹ thuật cho sản phẩm thuộc danh mục này.</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-success shadow-sm fw-bold" onclick="addGroup()">
                        <i class="fa fa-plus me-1"></i> Thêm Nhóm
                    </button>
                </div>
                
                <div class="card-body pt-0" id="template-container">
                    <?php 
                    $jsGroupCount = 0; 
                    if (!empty($currentData['template'])): 
                        foreach ($currentData['template'] as $gIndex => $group): 
                            $jsGroupCount = max($jsGroupCount, $gIndex + 1);
                    ?>
                        <div class="card mb-3 border bg-light" id="group-<?= $gIndex ?>">
                            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center py-2 px-3">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <span class="badge bg-secondary me-2">Nhóm</span>
                                    <input type="text" name="groups[<?= $gIndex ?>]" value="<?= htmlspecialchars($group['group_name']) ?>" 
                                           class="form-control form-control-sm fw-bold border-0 bg-transparent ps-0" 
                                           placeholder="Tên nhóm (VD: Màn hình)" required style="box-shadow:none;">
                                </div>
                                <button type="button" class="btn btn-sm text-danger" onclick="removeElement('group-<?= $gIndex ?>')" title="Xóa nhóm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            
                            <div class="card-body py-2 px-3 items-list-<?= $gIndex ?>">
                                <?php if(isset($group['items']) && is_array($group['items'])): ?>
                                    <?php foreach ($group['items'] as $item): ?>
                                        <div class="row g-2 mb-2 item-row align-items-center">
                                            <div class="col-md-4">
                                                <input type="text" name="items[<?= $gIndex ?>][name][]" value="<?= htmlspecialchars($item['name']) ?>" 
                                                       class="form-control form-control-sm" placeholder="Tên thông số" required>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="items[<?= $gIndex ?>][type][]" class="form-select form-select-sm" onchange="toggleAttr(this)">
                                                    <option value="text" <?= $item['type']=='text'?'selected':'' ?>>Text thường</option>
                                                    <option value="attribute" <?= $item['type']=='attribute'?'selected':'' ?>>🔗 Liên kết Attribute</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select name="items[<?= $gIndex ?>][attr_id][]" class="form-select form-select-sm" 
                                                        style="display: <?= $item['type']=='attribute'?'block':'none' ?>;">
                                                    <option value="">-- Chọn Attribute --</option>
                                                    <?php foreach($attrs as $a): ?>
                                                        <?php 
                                                            $isVar = isset($a['is_variant']) ? $a['is_variant'] : ($a['is_customizable'] ?? 0);
                                                            $label = $a['name'] . ($isVar ? ' (Variant)' : '');
                                                        ?>
                                                        <option value="<?= $a['id'] ?>" <?= (isset($item['attribute_id']) && $item['attribute_id'] == $a['id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($label) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-light text-danger border-0" onclick="this.closest('.item-row').remove()">✕</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="addItem(<?= $gIndex ?>, this)">
                                    <i class="fa fa-plus-circle"></i> Thêm thông số
                                </button>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                
                <div class="card-footer bg-white py-3 border-top">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="fa fa-save me-2"></i><?= $currentData['id'] ? "LƯU THAY ĐỔI" : "TẠO DANH MỤC" ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // 1. Nhận dữ liệu attributes từ PHP
    const attributesList = <?php echo json_encode($attrs); ?>;
    let groupCounter = <?= isset($jsGroupCount) ? $jsGroupCount : 0 ?>;

    function addGroup() {
        const container = document.getElementById('template-container');
        const idx = groupCounter++;
        
        const html = `
            <div class="card mb-3 border bg-light animate__animated animate__fadeIn" id="group-${idx}">
                <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center py-2 px-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="badge bg-secondary me-2">Nhóm</span>
                        <input type="text" name="groups[${idx}]" placeholder="Tên nhóm (VD: Camera)" 
                               class="form-control form-control-sm fw-bold border-0 bg-transparent ps-0" required style="box-shadow:none;">
                    </div>
                    <button type="button" class="btn btn-sm text-danger" onclick="removeElement('group-${idx}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div class="card-body py-2 px-3 items-list-${idx}"></div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="addItem(${idx}, this)">
                        <i class="fa fa-plus-circle"></i> Thêm thông số
                    </button>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addItem(groupIdx, btn) {
        let attrOptions = '<option value="">-- Chọn Attribute --</option>';
        if (attributesList && attributesList.length > 0) {
            attributesList.forEach(attr => {
                let isVar = attr.is_variant == 1 || attr.is_customizable == 1;
                let label = isVar ? `${attr.name} (Variant)` : attr.name;
                attrOptions += `<option value="${attr.id}">${label}</option>`;
            });
        }

        const html = `
            <div class="row g-2 mb-2 item-row align-items-center animate__animated animate__fadeIn">
                <div class="col-md-4">
                    <input type="text" name="items[${groupIdx}][name][]" placeholder="Tên thông số" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <select name="items[${groupIdx}][type][]" class="form-select form-select-sm" onchange="toggleAttr(this)">
                        <option value="text">Text thường</option>
                        <option value="attribute">🔗 Liên kết Attribute</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="items[${groupIdx}][attr_id][]" class="form-select form-select-sm" style="display:none">
                        ${attrOptions}
                    </select>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-light text-danger border-0" onclick="this.closest('.item-row').remove()">✕</button>
                </div>
            </div>`;
        
        // Chèn vào div chứa list items (là phần tử con thứ 2 của card)
        const cardBody = document.querySelector(`#group-${groupIdx} .items-list-${groupIdx}`);
        if(cardBody) cardBody.insertAdjacentHTML('beforeend', html);
    }

    function toggleAttr(select) {
        // Tìm select attribute kế bên (nằm ở col tiếp theo)
        const parentRow = select.closest('.item-row');
        const attrSelect = parentRow.querySelector('select[name*="[attr_id]"]');
        if(attrSelect) {
            attrSelect.style.display = (select.value === 'attribute') ? 'block' : 'none';
        }
    }

    function removeElement(id) {
        if(confirm('Bạn có chắc muốn xóa nhóm này?')) {
            const el = document.getElementById(id);
            if(el) el.remove();
        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>