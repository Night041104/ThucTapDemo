<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">
        <?= $currentData['id'] ? "Chỉnh sửa thuộc tính" : "Tạo thuộc tính mới" ?>
    </h3>
    <a href="index.php?module=admin&controller=attribute&action=index" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if(isset($msg) && $msg): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <i class="fa fa-exclamation-triangle me-2"></i> <?= $msg ?>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-cog me-2"></i>Thông tin cấu hình</h6>
            </div>
            
            <div class="card-body pt-0">
                <form method="POST" action="index.php?module=admin&controller=attribute&action=save">
                    <input type="hidden" name="id" value="<?= $currentData['id'] ?>">

                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Tên hiển thị <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="<?= htmlspecialchars($currentData['name']) ?>" 
                                   class="form-control form-control-lg" required placeholder="VD: Màu sắc, Bộ nhớ trong...">
                            <div class="form-text">Tên này sẽ hiện thị ngoài trang chủ shop.</div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-bold">Mã hệ thống (Code) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-code"></i></span>
                                <input type="text" name="code" value="<?= htmlspecialchars($currentData['code']) ?>" 
                                       class="form-control form-control-lg" required placeholder="VD: color, ram">
                            </div>
                            <div class="form-text">Viết liền không dấu, dùng để định danh.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Loại thuộc tính:</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="feature-card h-100 cursor-pointer">
                                    <input type="checkbox" name="is_variant" value="1" class="d-none peer" 
                                           <?= $currentData['is_variant'] == 1 ? 'checked' : '' ?>>
                                    <div class="card-body border rounded p-3 transition-all h-100 peer-checked-variant">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle me-3">
                                                <i class="fa fa-tags"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">Dùng làm biến thể</h6>
                                        </div>
                                        <p class="text-muted small mb-0 lh-sm">
                                            Check mục này nếu thuộc tính tạo ra các phiên bản con (VD: Màu sắc, Dung lượng).
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="feature-card h-100 cursor-pointer">
                                    <input type="checkbox" name="is_customizable" value="1" class="d-none peer" 
                                           <?= $currentData['is_customizable'] == 1 ? 'checked' : '' ?>>
                                    <div class="card-body border rounded p-3 transition-all h-100 peer-checked-custom">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-purple bg-opacity-10 text-purple rounded-circle me-3">
                                                <i class="fa fa-pen-fancy"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">Cho phép đổi tên</h6>
                                        </div>
                                        <p class="text-muted small mb-0 lh-sm">
                                            Cho phép nhập giá trị khác với danh sách có sẵn (VD: Sửa "Vàng" thành "Vàng Ánh Kim").
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Các giá trị mặc định (Options)</label>
                        <textarea name="options" class="form-control" style="height: 120px;" 
                                  placeholder="Nhập các giá trị ngăn cách nhau bằng dấu phẩy. VD: Đỏ, Xanh, Vàng, Tím"><?= htmlspecialchars($currentData['options_str']) ?></textarea>
                        
                        <div class="alert alert-light border mt-2 d-flex align-items-start">
                            <i class="fa fa-info-circle text-info mt-1 me-2"></i>
                            <div class="small text-muted">
                                <b>Lưu ý:</b> Khi chỉnh sửa, hệ thống sẽ thêm các giá trị mới vào danh sách. Các giá trị cũ sẽ được giữ nguyên để không ảnh hưởng đến sản phẩm đã tạo.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" name="btn_save" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                        <i class="fa fa-save me-2"></i><?= $currentData['id'] ? "CẬP NHẬT THUỘC TÍNH" : "LƯU THUỘC TÍNH MỚI" ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .cursor-pointer { cursor: pointer; }
    
    /* Hiệu ứng khi check checkbox */
    .peer:checked + .peer-checked-variant {
        border-color: #ff9800 !important;
        background-color: #fff3e0;
        box-shadow: 0 0 0 2px #ff9800;
    }
    .peer:checked + .peer-checked-custom {
        border-color: #9c27b0 !important;
        background-color: #f3e5f5;
        box-shadow: 0 0 0 2px #9c27b0;
    }
    
    .bg-purple { background-color: #9c27b0 !important; }
    .text-purple { color: #9c27b0 !important; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>