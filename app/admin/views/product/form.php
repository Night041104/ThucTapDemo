<?php
    // 1. Logic PHP giữ nguyên
    $isEdit = isset($rowProd) && $rowProd;

    if ($isEdit) {
        $selectedCateId = $rowProd['category_id'];
    } else {
        $selectedCateId = isset($selectedCateId) ? $selectedCateId : 0;
    }
    
    $pageTitle = $isEdit ? "Sửa sản phẩm: ". htmlspecialchars($rowProd['name']) : "Tạo sản phẩm mới";
    $formAction = $isEdit ? "admin/product/update?id=".$rowProd['id'] : "admin/product/store";
    
    // Nhúng Layout Header
    require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0"><?= $pageTitle ?></h3>
    <a href="admin/product" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="fa fa-info-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<?php if(!$isEdit): ?>
    <div class="card card-custom mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="admin/product/create" class="row align-items-center g-3">
                
                
                <div class="col-auto">
                    <label class="fw-bold text-primary"><i class="fa fa-list me-2"></i>Chọn danh mục sản phẩm:</label>
                </div>
                <div class="col-md-4">
                    <select name="cate_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Vui lòng chọn --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (isset($selectedCateId) && $selectedCateId==$c['id']) ? 'selected' : '' ?>>
                                <?= $c['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($selectedCateId) || $isEdit): ?>
<form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data" id="productForm">
    <input type="hidden" name="cate_id" value="<?= $selectedCateId ?>">

    <div class="row">
        <div class="col-lg-8">
            
            <div class="card card-custom mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-info-circle me-2"></i>Thông tin chung</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tên Sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?= htmlspecialchars($rowProd['name'] ?? $_POST['name'] ?? '') ?>" 
                                   placeholder="VD: iPhone 15 Pro Max 256GB">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-select" required>
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (isset($rowProd) && $rowProd['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                        <?= $b['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-4 border-0 shadow-sm" id="specs-container">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-info"><i class="fa fa-sliders-h me-2"></i>Thông số kỹ thuật (Shared Specs)</h6>
                </div>
                <div class="card-body pt-0">
                    <?php 
                    $specsData = [];
                    if ($isEdit && isset($currentSpecs)) {
                        $specsData = $currentSpecs; 
                    } elseif (!empty($template)) {
                        $specsData = $template;
                    }
                    ?>

                    <?php if(!empty($specsData)): ?>
                        <?php foreach($specsData as $gIndex => $group): ?>
                            <div class="mb-4 p-3 bg-light rounded border border-light">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><?= $group['group_name'] ?></h6>
                                <input type="hidden" name="spec_group[<?= $gIndex ?>]" value="<?= $group['group_name'] ?>">
                                
                                <div class="items-list">
                                    <?php foreach($group['items'] as $iIndex => $item): ?>
                                        <div class="row-item input-group mb-2">
                                            <button type="button" class="btn btn-outline-danger px-3" onclick="removeRow(this)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            
                                            <input type="text" name="spec_item[<?= $gIndex ?>][name][]" value="<?= $item['name'] ?>" 
                                                   class="form-control bg-white fw-bold" style="max-width: 180px;" readonly>
                                            <input type="hidden" name="spec_item[<?= $gIndex ?>][type][]" value="<?= $item['type'] ?>">
                                            
                                            <?php $val = $item['value'] ?? ''; ?>

                                            <?php if($item['type'] == 'text'): ?>
                                                <input type="text" name="spec_item[<?= $gIndex ?>][value_text][]" value="<?= $val ?>" 
                                                       class="form-control" placeholder="Nhập giá trị..." required>
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][value_id][]" value="">
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="">
                                            
                                            <?php elseif($item['type'] == 'attribute'): ?>
                                                <?php 
                                                    $attrId = $item['attribute_id'] ?? $item['attr_id'] ?? 0;
                                                    $canCustom = isset($attrConfigs[$attrId]) && $attrConfigs[$attrId] == 1;
                                                    $currentOptId = isset($selectedOptions[$attrId]) ? $selectedOptions[$attrId] : 0;
                                                    $currentText = $val; 
                                                ?>
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][attr_id][]" value="<?= $attrId ?>">
                                                <input type="hidden" name="spec_item[<?= $gIndex ?>][value_text][]" value="">
                                                
                                                <select name="spec_item[<?= $gIndex ?>][value_id][]" class="form-select" style="max-width: 220px;" <?= (!$canCustom) ? 'required' : '' ?>>
                                                    <option value="">-- Chọn --</option>
                                                    <?php
                                                    if($attrId && isset($allAttributeOptions[$attrId])){
                                                        foreach($allAttributeOptions[$attrId] as $opt) {
                                                            $isSelected = ($currentOptId == $opt['id']);
                                                            echo "<option value='{$opt['id']}' ".($isSelected ? 'selected' : '').">{$opt['value']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                
                                                <?php if($canCustom): ?>
                                                    <input type="text" name="spec_item[<?= $gIndex ?>][value_custom][]" 
                                                           value="<?= htmlspecialchars($currentText) ?>" 
                                                           class="form-control" placeholder="Nhập chi tiết (VD: Đỏ đô)...">
                                                <?php else: ?>
                                                    <span class="input-group-text bg-secondary text-white small" style="font-size: 0.75rem;">FIXED</span>
                                                    <input type="hidden" name="spec_item[<?= $gIndex ?>][value_custom][]" value="">
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success mt-2 fw-bold" onclick="addNewRow(this, <?= $gIndex ?>)">
                                    <i class="fa fa-plus me-1"></i> Thêm dòng
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-clipboard-list fa-2x mb-2 opacity-50"></i>
                            <p>Chưa có thông số mẫu cho danh mục này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="card card-custom mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-warning"><i class="fa fa-images me-2"></i>Hình ảnh</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Ảnh đại diện</label>
                        <div class="text-center p-3 border rounded bg-light position-relative" style="min-height: 150px;">
                            <?php if($isEdit && !empty($rowProd['thumbnail'])): ?>
                                <img src="<?= $rowProd['thumbnail'] ?>" class="img-fluid rounded mb-2" style="max-height: 120px;">
                            <?php endif; ?>
                            
                            <div id="thumb-container" style="display:none;" class="mb-2">
                                <img id="thumb-preview" class="img-fluid rounded" style="max-height: 120px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removeThumb()">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="document.getElementById('thumb-input').click()">
                                <i class="fa fa-upload me-1"></i> Chọn ảnh
                            </button>
                            <input type="file" id="thumb-input" name="thumbnail" accept="image/*" class="d-none" onchange="previewThumb(this)">
                        </div>
                    </div>

                    <hr class="dashed">

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase">Album ảnh (Gallery)</label>
                        
                        <div class="d-flex flex-wrap gap-2 mb-2" id="current-gallery">
                            <?php if($isEdit && !empty($gallery)): ?>
                                <?php foreach($gallery as $img): ?>
                                    <div class="position-relative border rounded overflow-hidden" style="width: 60px; height: 60px;">
                                        <img src="<?= $img['image_url'] ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <a href="admin/product/deleteImage?del_img=<?= $img['id'] ?>&id=<?= $rowProd['id'] ?>" 
                                           onclick="return confirm('Xóa ảnh này?')" 
                                           class="position-absolute top-0 end-0 bg-danger text-white d-flex justify-content-center align-items-center" 
                                           style="width: 18px; height: 18px; font-size: 10px; text-decoration: none;">✕</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div id="gallery-preview-box" class="d-flex flex-wrap gap-2 mb-2"></div>

                        <div class="d-grid">
                             <button type="button" class="btn btn-light border border-dashed text-primary" onclick="document.getElementById('gallery-input').click()">
                                <i class="fa fa-plus me-1"></i> Thêm nhiều ảnh
                            </button>
                            <input type="file" id="gallery-input" name="gallery[]" accept="image/*" multiple class="d-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-success"><i class="fa fa-tag me-2"></i>Thông tin bán hàng</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá bán</label>
                        <div class="input-group">
                            <input type="text" name="price" value="<?= number_format($rowProd['price'] ?? 0) ?>" required class="form-control money fw-bold text-danger">
                            <span class="input-group-text">₫</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá niêm yết</label>
                        <div class="input-group">
                            <input type="text" name="market_price" value="<?= number_format($rowProd['market_price'] ?? 0) ?>" class="form-control money">
                            <span class="input-group-text">₫</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tồn kho</label>
                        <input type="number" name="quantity" value="<?= $rowProd['quantity'] ?? 10 ?>" required class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= (isset($rowProd) && $rowProd['status']==1) ? 'selected' : '' ?>>🟢 Đang bán</option>
                            <option value="0" <?= (isset($rowProd) && $rowProd['status']==0) ? 'selected' : '' ?>>⚪ Tạm ẩn</option>
                            <option value="-1" <?= (isset($rowProd) && $rowProd['status']==-1) ? 'selected' : '' ?>>⚫ Ngừng kinh doanh</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="<?= $isEdit ? 'btn_update' : 'btn_save_product' ?>" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa fa-save me-2"></i> <?= $isEdit ? "CẬP NHẬT" : "LƯU SẢN PHẨM" ?>
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>
<?php endif; ?>

<div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="sharedInfoToast" class="toast align-items-center text-white bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-6">
                <i class="fa fa-exclamation-triangle me-2"></i>
                <b>Cảnh báo:</b> Bạn đang sửa thông tin CHUNG.<br>Thay đổi này sẽ áp dụng cho tất cả sản phẩm cùng dòng!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const variantIds = <?= json_encode($variantIds ?? []) ?>.map(String); 
        const isEditMode = <?= isset($isEdit) && $isEdit ? 'true' : 'false' ?>;
        let hasSharedChange = false; 

        // Bootstrap Toast
        const toastEl = document.getElementById('sharedInfoToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

        function showWarningToast(element) {
            hasSharedChange = true;
            if(element) {
                element.classList.add('border-warning', 'border-2');
                setTimeout(() => element.classList.remove('border-warning', 'border-2'), 2000);
            }
            toast.show();
        }

        // 1. Lắng nghe thay đổi thông tin chung
        ['name', 'brand_id', 'status'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.addEventListener('change', () => showWarningToast(el));
        });

        // 2. Lắng nghe thay đổi Specs
        const specsContainer = document.getElementById('specs-container');
        if (specsContainer) {
            specsContainer.addEventListener('change', function(e) {
                if ((e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') && e.target.type !== 'hidden') {
                    const rowItem = e.target.closest('.row-item');
                    if (!rowItem) return;
                    
                    const attrIdInput = rowItem.querySelector('input[name*="[attr_id]"]');
                    if (attrIdInput) {
                        if (variantIds.includes(attrIdInput.value)) return; 
                    }
                    showWarningToast(e.target);
                }
            });
        }

        // 3. Confirm Submit
        const productForm = document.getElementById('productForm');
        if(productForm) {
            productForm.addEventListener('submit', function(e) {
                if (isEditMode && hasSharedChange) {
                    const msg = "⚠️ CẢNH BÁO QUAN TRỌNG:\n\nBạn đã sửa thông tin CHUNG (Tên, Hãng, Specs chung...).\nViệc này sẽ thay đổi hàng loạt sản phẩm liên quan.\n\nBạn có chắc chắn muốn tiếp tục?";
                    if (!confirm(msg)) e.preventDefault(); 
                }
            });
        }

        // Format Money
        document.querySelectorAll('.money').forEach(inp => {
            inp.addEventListener('keyup', function() {
                let n = parseInt(this.value.replace(/\D/g,''), 10);
                this.value = isNaN(n) ? '' : n.toLocaleString('en-US');
            });
        });
    });

    // Preview Thumb
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

    // Gallery Logic
    const galleryInput = document.getElementById('gallery-input');
    const galleryBox = document.getElementById('gallery-preview-box');
    const dt = new DataTransfer();

    if(galleryInput) {
        galleryInput.addEventListener('change', function() {
            for(let i = 0; i < this.files.length; i++) dt.items.add(this.files[i]);
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
                div.className = 'position-relative border rounded overflow-hidden';
                div.style.width = '60px'; div.style.height = '60px';
                div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">
                                 <button type="button" class="position-absolute top-0 end-0 bg-danger text-white border-0 d-flex justify-content-center align-items-center" 
                                 style="width:18px;height:18px;font-size:10px;" onclick="removeGalleryItem(${i})">✕</button>`;
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

    // Dynamic Row Logic
    function removeRow(btn) { btn.closest('.row-item').remove(); }
    
    function addNewRow(btn, groupIndex) {
        const html = `
            <div class="row-item input-group mb-2">
                <button type="button" class="btn btn-outline-danger px-3" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                <input type="text" name="spec_item[${groupIndex}][name][]" class="form-control bg-white fw-bold" placeholder="Tên thông số" style="max-width: 180px;" required>
                <input type="hidden" name="spec_item[${groupIndex}][type][]" value="text">
                <input type="text" name="spec_item[${groupIndex}][value_text][]" class="form-control" placeholder="Nhập giá trị..." required>
                <input type="hidden" name="spec_item[${groupIndex}][value_id][]" value="">
                <input type="hidden" name="spec_item[${groupIndex}][value_custom][]" value="">
                <input type="hidden" name="spec_item[${groupIndex}][attr_id][]" value="">
            </div>`;
        btn.previousElementSibling.insertAdjacentHTML('beforeend', html);
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>