<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">
        <?= $currentData['id'] ? "Chỉnh sửa Thương hiệu" : "Tạo Thương hiệu mới" ?>
    </h3>
    <a href="index.php?module=admin&controller=brand&action=index" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if(isset($_GET['msg']) && (strpos($_GET['msg'], 'error') !== false || strpos($_GET['msg'], 'Fail') !== false)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-tag me-2"></i>Thông tin Thương hiệu</h6>
            </div>
            
            <div class="card-body pt-0">
                <form method="POST" action="index.php?module=admin&controller=brand&action=save" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $currentData['id'] ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tên Thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" 
                               class="form-control form-control-lg" required placeholder="VD: Apple, Samsung...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Thuộc Danh mục (Ngành hàng)</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            <?php if(!empty($allCats)): ?>
                                <div class="row g-2">
                                    <?php foreach($allCats as $cat): ?>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="categories[]" 
                                                       value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>"
                                                       <?= in_array($cat['id'], $selectedCats) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small">Chưa có danh mục nào.</div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Thương hiệu này sẽ xuất hiện khi lọc ở các danh mục được chọn.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Logo Thương hiệu</label>
                        
                        <div class="d-flex align-items-start gap-3">
                            <?php 
                                $hasOldImg = !empty($currentData['logo_url']);
                                $displayStyle = $hasOldImg ? 'block' : 'none';
                            ?>
                            <div id="preview-area" class="position-relative border rounded p-1 bg-white shadow-sm" 
                                 style="width: 100px; height: 100px; display: <?= $displayStyle ?>;">
                                <img id="img-preview" src="<?= $hasOldImg ? $currentData['logo_url'] : '' ?>" 
                                     style="width: 100%; height: 100%; object-fit: contain;">
                                
                                <?php if($hasOldImg): ?>
                                    <a href="index.php?module=admin&controller=brand&action=deleteImage&id=<?= $currentData['id'] ?>" 
                                       id="btn-server-del" 
                                       class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white shadow-sm text-decoration-none" 
                                       onclick="return confirm('Xóa vĩnh viễn logo này?')" title="Xóa ảnh cũ">✕</a>
                                <?php endif; ?>

                                <button type="button" id="btn-client-cancel" 
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary border border-white shadow-sm" 
                                        style="display:none; border:none;" onclick="cancelPreview()" title="Hủy chọn">✕</button>
                            </div>

                            <div class="flex-grow-1">
                                <input type="file" id="logo-input" name="logo" accept="image/*" class="form-control" onchange="previewImage(this)">
                                <div class="form-text mt-2">Định dạng: JPG, PNG. Dung lượng tối đa 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" name="btn_save" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa fa-save me-2"></i><?= $currentData['id'] ? "CẬP NHẬT THƯƠNG HIỆU" : "LƯU THƯƠNG HIỆU MỚI" ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic JS giữ nguyên nhưng cập nhật ID cho khớp HTML mới
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
                
                if(btnServerDel) btnServerDel.style.display = 'none';
                btnClientCancel.style.display = 'block';
            }
            reader.readAsDataURL(inp.files[0]);
        }
    }

    function cancelPreview() {
        input.value = ""; 

        if (hasOldImg) {
            imgPreview.src = oldImgSrc;
            if(btnServerDel) btnServerDel.style.display = 'block';
            btnClientCancel.style.display = 'none';
        } else {
            previewArea.style.display = 'none';
            imgPreview.src = '';
            btnClientCancel.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>