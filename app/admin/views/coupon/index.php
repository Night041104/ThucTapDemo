<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Quản lý Mã Giảm Giá</h4>
        <p class="text-muted small mb-0">Tạo và quản lý các chương trình khuyến mãi</p>
    </div>
    <a href="admin/coupon/create" class="btn btn-primary btn-sm shadow-sm">
        <i class="fa fa-plus me-1"></i> Thêm mã mới
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa fa-check-circle me-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom-0">
        <form id="filterForm" class="row g-2 align-items-center" onsubmit="return false;">
            
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="keyword" id="keyword" 
                           class="form-control bg-light border-start-0" 
                           placeholder="Nhập mã code, mô tả...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="type" id="type" class="form-select bg-light">
                    <option value="">-- Tất cả loại --</option>
                    <option value="percent">💎 Theo phần trăm (%)</option>
                    <option value="fixed">💵 Theo tiền mặt (VNĐ)</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1">✅ Đang kích hoạt</option>
                    <option value="0">⛔ Đã tắt</option>
                </select>
            </div>

            <div class="col-md-auto d-flex align-items-center gap-2">
                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
                <button type="button" class="btn btn-light text-danger fw-bold" onclick="resetFilter()" title="Xóa bộ lọc">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Thông tin Mã</th>
                        <th>Loại / Giá trị</th>
                        <th>Điều kiện</th>
                        <th>SL / Limit</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody id="couponTableBody"> 
                    <?php if(!empty($coupons)): ?>
                        <?php foreach ($coupons as $c): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded p-2 text-center text-white me-3" 
                                             style="width:45px; height:45px; background: <?= $c['type'] == 'percent' ? '#4e73df' : '#1cc88a' ?>;">
                                            <i class="fa <?= $c['type'] == 'percent' ? 'fa-percent' : 'fa-money-bill' ?> fa-lg"></i>
                                        </div>
                                        <div>
                                            <strong class="text-primary text-uppercase"><?= $c['code'] ?></strong>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                                <?= $c['description'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($c['type'] == 'percent'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">
                                            Giảm <?= $c['value'] ?>%
                                        </span>
                                        <?php if($c['max_discount_amount'] > 0): ?>
                                            <div class="small mt-1 text-danger">
                                                Max: <?= number_format($c['max_discount_amount']/1000) ?>k
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">
                                            Giảm <?= number_format($c['value']) ?>đ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">Đơn từ:</small><br>
                                    <strong><?= number_format($c['min_order_amount']) ?>đ</strong>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span>Tổng: <b><?= $c['quantity'] ?></b></span>
                                        <span class="text-muted">User: <?= $c['usage_limit_per_user'] == 0 ? '∞' : $c['usage_limit_per_user'] ?></span>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    <div><i class="fa fa-clock me-1"></i> <?= date('d/m/y H:i', strtotime($c['start_date'])) ?></div>
                                    <div><i class="fa fa-ban me-1"></i> <?= date('d/m/y H:i', strtotime($c['end_date'])) ?></div>
                                </td>
                                <td>
                                    <?php 
                                        $now = date('Y-m-d H:i:s');
                                        if ($c['status'] == 0) {
                                            echo '<span class="badge bg-secondary">Đã tắt</span>';
                                        } elseif ($c['end_date'] < $now) {
                                            echo '<span class="badge bg-danger">Hết hạn</span>';
                                        } elseif ($c['quantity'] <= 0) {
                                            echo '<span class="badge bg-warning text-dark">Hết mã</span>';
                                        } else {
                                            echo '<span class="badge bg-success">Đang chạy</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="admin/coupon/edit?id=<?= $c['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="admin/coupon/delete?id=<?= $c['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa mã này?')" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy mã giảm giá nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('filterForm');
        const inputs = form.querySelectorAll('input, select');
        const spinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById('couponTableBody');
        let timeout = null;

        function fetchCoupons() {
            spinner.classList.remove('d-none');
            
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // [FIX AJAX] Gọi về admin/coupon
            fetch('admin/coupon?' + params.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('couponTableBody');
                    
                    if(newTbody) {
                        tableBody.innerHTML = newTbody.innerHTML;
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    spinner.classList.add('d-none');
                });
        }

        inputs.forEach(input => {
            if (input.type === 'text') {
                input.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchCoupons, 400); 
                });
            }
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', fetchCoupons);
            }
        });
        
        window.resetFilter = function() {
            form.reset();
            fetchCoupons();
        }
    });
</script>