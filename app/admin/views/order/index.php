<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Quản lý Đơn hàng</h3>
        <p class="text-muted small mb-0">Theo dõi và xử lý đơn đặt hàng từ khách</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-white shadow-sm border" onclick="window.location.reload()">
            <i class="fa fa-sync-alt text-primary"></i>
        </button>
        <button class="btn btn-primary shadow-sm">
            <i class="fa fa-file-export me-1"></i> Xuất Excel
        </button>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form id="filterForm" class="row g-3 align-items-center">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="controller" value="order">
            <input type="hidden" name="action" value="index">

            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="keyword" id="keyword" class="form-control bg-light border-start-0" 
                           placeholder="Nhập mã đơn, tên khách, SĐT..." 
                           value="<?= isset($keyword) ? htmlspecialchars($keyword) : '' ?>">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" <?= (isset($status) && $status == '1') ? 'selected' : '' ?>>🟡 Chờ xác nhận</option>
                    <option value="2" <?= (isset($status) && $status == '2') ? 'selected' : '' ?>>🔵 Đã xác nhận/TT</option>
                    <option value="3" <?= (isset($status) && $status == '3') ? 'selected' : '' ?>>🚚 Đang giao hàng</option>
                    <option value="4" <?= (isset($status) && $status == '4') ? 'selected' : '' ?>>🟢 Hoàn thành</option>
                    <option value="5" <?= (isset($status) && $status == '5') ? 'selected' : '' ?>>🔴 Đã hủy</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="payment" id="payment" class="form-select bg-light">
                    <option value="">-- Loại thanh toán --</option>
                    <option value="COD" <?= (isset($payment) && $payment == 'COD') ? 'selected' : '' ?>>💵 Tiền mặt (COD)</option>
                    <option value="VNPAY" <?= (isset($payment) && $payment == 'VNPAY') ? 'selected' : '' ?>>💳 VNPAY</option>
                </select>
            </div>

            <div class="col-md-2 text-center d-none" id="loadingSpinner">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="small text-muted ms-1">Đang tải...</span>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="orderTable">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>PTTT</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">
                                    #<?= $row['order_code'] ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                                            <?= strtoupper(substr($row['fullname'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['fullname']) ?></div>
                                            <div class="small text-muted"><i class="fa fa-phone me-1"></i><?= $row['phone'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?><br>
                                    <?= date('H:i', strtotime($row['created_at'])) ?>
                                </td>
                                <td class="fw-bold text-danger">
                                    <?= number_format($row['total_money'], 0, ',', '.') ?>₫
                                </td>
                                <td>
                                    <?php if ($row['payment_method'] == 'VNPAY'): ?>
                                        <span class="badge bg-purple bg-opacity-10 text-purple border border-purple-light">VNPAY</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">COD</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $s = $row['status'];
                                        $badges = [
                                            1 => ['bg-warning text-dark', 'Chờ xác nhận'],
                                            2 => ['bg-info text-white', ($row['payment_method'] == 'VNPAY' ? 'Đã thanh toán' : 'Đã xác nhận')],
                                            3 => ['bg-primary text-white', 'Đang giao'],
                                            4 => ['bg-success text-white', 'Hoàn thành'],
                                            5 => ['bg-danger text-white', 'Đã hủy']
                                        ];
                                        $bClass = $badges[$s][0] ?? 'bg-secondary';
                                        $bLabel = $badges[$s][1] ?? 'Không rõ';
                                    ?>
                                    <span class="badge <?= $bClass ?> rounded-pill px-3"><?= $bLabel ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="index.php?module=admin&controller=order&action=detail&id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3">
                                        Chi tiết <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-purple { background-color: #f3e5f5 !important; }
    .text-purple { color: #7b1fa2 !important; }
    .border-purple-light { border-color: #e1bee7 !important; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('filterForm');
        const inputs = form.querySelectorAll('input, select');
        const spinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById('orderTableBody');

        let timeout = null;

        function fetchOrders() {
            spinner.classList.remove('d-none');
            
            // Tạo URL với query params
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Fetch HTML về và cắt lấy phần tbody
            fetch('index.php?' + params.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('orderTableBody');
                    
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
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(fetchOrders, 400); // Debounce 400ms
            });
            
            if(input.tagName === 'SELECT') {
                input.addEventListener('change', fetchOrders);
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>