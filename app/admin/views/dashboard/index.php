<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden text-white" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <div class="card-body position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Doanh thu</div>
                        <div class="h3 mb-0 fw-bold mt-1">
                            <?= number_format($stats['total_revenue'] / 1000000, 1) ?> Tr
                            <div class="fs-6 fw-normal text-white-50">VNĐ</div>
                        </div>
                    </div>
                    <i class="fa fa-dollar-sign fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #1cc88a;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-success small text-uppercase fw-bold">Đơn hàng</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $stats['total_orders'] ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fa fa-shopping-cart fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #36b9cc;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-info small text-uppercase fw-bold">Sản phẩm</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $stats['total_products'] ?></div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                        <i class="fa fa-box-open fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #f6c23e;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-warning small text-uppercase fw-bold">Thành viên</div>
                        <div class="h3 mb-0 fw-bold text-gray-800"><?= $stats['total_users'] ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fa fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary"><i class="fa fa-chart-line me-2"></i>Biểu đồ Doanh thu</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="m-0 fw-bold text-dark"><i class="fa fa-pie-chart me-2"></i>Tỷ lệ đơn hàng</h6>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="chart-pie pt-2 pb-2" style="height: 220px; position: relative;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-3 text-center small text-muted">
                    <span><i class="fas fa-circle text-warning"></i> Mới</span> &nbsp;
                    <span><i class="fas fa-circle text-info"></i> Duyệt</span> &nbsp;
                    <span><i class="fas fa-circle text-primary"></i> Giao</span> &nbsp;
                    <span><i class="fas fa-circle text-success"></i> Xong</span> &nbsp;
                    <span><i class="fas fa-circle text-danger"></i> Hủy</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Đơn hàng mới nhất</h6>
                <a href="index.php?module=admin&controller=order&action=index" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3">Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-3">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentOrders)): ?>
                            <?php foreach($recentOrders as $o): ?>
                                <tr>
                                    <td class="ps-3"><span class="fw-bold text-dark">#<?= $o['order_code'] ?></span></td>
                                    <td><?= htmlspecialchars($o['fullname']) ?></td>
                                    <td class="fw-bold text-danger"><?= number_format($o['total_money']) ?>₫</td>
                                    <td>
                                        <?php
                                            $s = $o['status'];
                                            $badges = [
                                                1 => ['bg-warning text-dark', 'Mới'],
                                                2 => ['bg-info text-white', 'Đã duyệt'],
                                                3 => ['bg-primary text-white', 'Đang giao'],
                                                4 => ['bg-success text-white', 'Hoàn thành'],
                                                5 => ['bg-danger text-white', 'Đã hủy']
                                            ];
                                            $cls = $badges[$s][0] ?? 'bg-secondary';
                                            $lbl = $badges[$s][1] ?? 'Khác';
                                        ?>
                                        <span class="badge <?= $cls ?> rounded-pill"><?= $lbl ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="index.php?module=admin&controller=order&action=detail&id=<?= $o['id'] ?>" class="btn btn-sm btn-light text-primary"><i class="fa fa-arrow-right"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có đơn hàng nào</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-danger"><i class="fa fa-exclamation-triangle me-2"></i>Sắp hết hàng (SL <= 5)</h6>
            </div>
            <div class="list-group list-group-flush">
                <?php if(!empty($lowStockProducts)): ?>
                    <?php foreach($lowStockProducts as $p): ?>
                        <div class="list-group-item d-flex align-items-center p-3">
                            <div class="me-3">
                                <img src="<?= $p['thumbnail'] ?>" class="rounded border" style="width: 40px; height: 40px; object-fit: contain; padding:2px;">
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-truncate fw-bold text-dark mb-1" style="font-size: 0.9rem;"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="small text-muted">SKU: <?= $p['sku'] ?></div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger rounded-pill"><?= $p['quantity'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted small">
                        <i class="fa fa-check-circle text-success fa-3x mb-3 opacity-50"></i><br>Kho hàng ổn định
                    </div>
                <?php endif; ?>
            </div>
            <?php if(!empty($lowStockProducts)): ?>
            <div class="card-footer bg-white text-center">
                <a href="index.php?module=admin&controller=product&action=index" class="text-decoration-none small">Quản lý kho hàng <i class="fa fa-arrow-right ms-1"></i></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Biểu đồ Doanh thu (Line Chart)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    // Gradient Background
    let gradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.5)'); 
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0.05)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode($chartData['data']) ?>,
                borderColor: '#4e73df',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#4e73df',
                fill: true,
                tension: 0.3 
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value / 1000000 + ' Tr'; }
                    }
                }
            }
        }
    });

    // 2. Biểu đồ Trạng thái (Doughnut Chart)
    const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Mới', 'Duyệt', 'Giao', 'Xong', 'Hủy'],
            datasets: [{
                data: [
                    <?= $orderStatusCounts[1] ?>,
                    <?= $orderStatusCounts[2] ?>,
                    <?= $orderStatusCounts[3] ?>,
                    <?= $orderStatusCounts[4] ?>,
                    <?= $orderStatusCounts[5] ?>
                ],
                backgroundColor: ['#f6c23e', '#17a2b8', '#4e73df', '#1cc88a', '#e74a3b'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>