<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $isEdit ? 'Cập nhật Mã Giảm Giá' : 'Thêm Mã Giảm Giá Mới' ?></h1>
    <a href="index.php?module=admin&controller=coupon" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="index.php?module=admin&controller=coupon&action=save" method="POST">
            <?php if($isEdit): ?>
                <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Coupon (Code) <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase fw-bold" required 
                               placeholder="VD: SALE50, TET2025"
                               value="<?= $isEdit ? $coupon['code'] : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <input type="text" name="description" class="form-control" 
                               placeholder="VD: Giảm giá nhân dịp tết..."
                               value="<?= $isEdit ? $coupon['description'] : '' ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại giảm giá</label>
                            <select name="type" id="coupon_type" class="form-select" onchange="toggleMaxDiscount()">
                                <option value="fixed" <?= ($isEdit && $coupon['type']=='fixed') ? 'selected' : '' ?>>Số tiền cố định (VND)</option>
                                <option value="percent" <?= ($isEdit && $coupon['type']=='percent') ? 'selected' : '' ?>>Theo phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá trị giảm <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="value" class="form-control money fw-bold text-danger" required
                                    placeholder="Nhập số tiền hoặc %"
                                    value="<?= $isEdit ? number_format($coupon['value']) : '' ?>">
                                <span class="input-group-text" id="value-addon">₫</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="max_discount_div" style="display: none;">
                        <label class="form-label fw-bold text-danger">Giảm tối đa (VNĐ)</label>
                        <div class="input-group">
                            <input type="text" name="max_discount_amount" class="form-control money" 
                                placeholder="VD: 50,000 (Để 0 nếu không giới hạn)"
                                value="<?= $isEdit ? number_format($coupon['max_discount_amount']) : '0' ?>">
                            <span class="input-group-text">₫</span>
                        </div>
                        <small class="text-muted">Ví dụ: Giảm 50% nhưng tối đa chỉ giảm 50.000đ.</small>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                        <div class="input-group">
                            <input type="text" name="min_order_amount" class="form-control money" 
                                value="<?= $isEdit ? number_format($coupon['min_order_amount']) : '0' ?>">
                            <span class="input-group-text">₫</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tổng số lượng phát hành</label>
                            <input type="number" name="quantity" class="form-control" required value="<?= $isEdit ? $coupon['quantity'] : '100' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" title="Nhập 0 để không giới hạn">Giới hạn mỗi người dùng</label>
                            <input type="number" name="usage_limit_per_user" class="form-control" value="<?= $isEdit ? $coupon['usage_limit_per_user'] : '1' ?>">
                            <small class="text-muted" style="font-size: 11px;">(Nhập 0 = Không giới hạn)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_date" class="form-control" required 
                               value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($coupon['start_date'])) : date('Y-m-d\TH:i') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_date" class="form-control" required 
                               value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($coupon['end_date'])) : '' ?>">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" <?= (!$isEdit || $coupon['status']==1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="statusSwitch">Kích hoạt mã ngay</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fa fa-save me-1"></i> Lưu thông tin
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. Logic ẩn hiện ô Giảm tối đa & Thay đổi đơn vị
    function toggleMaxDiscount() {
        var type = document.getElementById('coupon_type').value;
        var maxDiv = document.getElementById('max_discount_div');
        var valueAddon = document.getElementById('value-addon');
        
        if (type === 'percent') {
            maxDiv.style.display = 'block';
            valueAddon.innerText = '%';
        } else {
            maxDiv.style.display = 'none';
            valueAddon.innerText = '₫';
        }
    }
    
    // 2. [MỚI] Script định dạng tiền tệ (Giống form Product)
    document.addEventListener("DOMContentLoaded", function() {
        toggleMaxDiscount(); // Chạy khi load

        // Format Money (Tự động thêm dấu phẩy khi gõ)
        document.querySelectorAll('.money').forEach(inp => {
            inp.addEventListener('keyup', function() {
                // Xóa các ký tự không phải số
                let n = parseInt(this.value.replace(/\D/g,''), 10);
                // Format lại theo chuẩn US (1,000,000)
                this.value = isNaN(n) ? '' : n.toLocaleString('en-US');
            });
        });
    });
</script>