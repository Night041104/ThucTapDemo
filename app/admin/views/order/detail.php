<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?module=admin&controller=order&action=index" class="btn btn-outline-secondary btn-sm rounded-circle">
                <i class="fa fa-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0">Đơn hàng #<?= $order['order_code'] ?></h3>
            
            <?php 
                $st = $order['status'];
                $colors = [1=>'warning', 2=>'info', 3=>'primary', 4=>'success', 5=>'danger'];
                $labels = [1=>'Chờ xác nhận', 2=>'Đã xác nhận', 3=>'Đang giao', 4=>'Hoàn thành', 5=>'Đã hủy'];
                $color = $colors[$st] ?? 'secondary';
                $label = $labels[$st] ?? 'Không rõ';
            ?>
            <span class="badge bg-<?= $color ?> rounded-pill ms-2"><?= $label ?></span>
        </div>
        <p class="text-muted small ms-5 mb-0">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    </div>
    
    <button class="btn btn-dark shadow-sm" onclick="printInvoice()">
        <i class="fa fa-print me-1"></i> In Hóa Đơn
    </button>
</div>

<div class="row d-print-none">
    <div class="col-lg-8">
        
        <?php if (!empty($order['tracking_code'])): ?>
            <div class="card mb-4 border-success shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h6 class="text-success fw-bold mb-1"><i class="fa fa-truck me-2"></i>THÔNG TIN VẬN CHUYỂN</h6>
                            <p class="mb-0">Đơn vị: <strong>Giao Hàng Nhanh (GHN)</strong></p>
                            <p class="mb-0">Mã vận đơn: <strong class="text-danger fs-5"><?= $order['tracking_code'] ?></strong></p>
                        </div>
                        <div class="col-md-5 text-end">
                            <a href="https://tracking.ghn.dev/?order_code=<?= $order['tracking_code'] ?>" 
                               target="_blank" 
                               class="btn btn-outline-success fw-bold">
                                <i class="fa fa-map-marked-alt me-1"></i> Xem hành trình
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-primary"><i class="fa fa-box-open me-2"></i>Chi tiết đơn hàng</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th class="ps-4">Sản phẩm</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-center">SL</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                <i class="fa fa-image text-secondary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></div>
                                                <div class="small text-muted">ID: <?= $item['product_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end"><?= number_format($item['price']) ?>₫</td>
                                    <td class="text-center fw-bold">x<?= $item['quantity'] ?></td>
                                    <td class="text-end pe-4 fw-bold text-dark">
                                        <?= number_format($item['price'] * $item['quantity']) ?>₫
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end py-3 fw-bold text-uppercase text-muted">Tổng tiền hàng:</td>
                                <td class="text-end py-3 pe-4">
                                    <span class="h5 fw-bold text-danger mb-0"><?= number_format($order['total_money']) ?>₫</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-success"><i class="fa fa-tasks me-2"></i>Cập nhật trạng thái</h6>
            </div>
            <div class="card-body">
                <form id="updateStatusForm">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    
                    <label class="form-label small fw-bold text-muted">Trạng thái đơn hàng:</label>
                    <select name="status" class="form-select mb-3">
                        <option value="1" <?= $order['status']==1 ? 'selected':'' ?>>1. 🟡 Chờ xác nhận</option>
                        <option value="2" <?= $order['status']==2 ? 'selected':'' ?>>2. 🔵 Đã xác nhận / Đã thanh toán</option>
                        <option value="3" <?= $order['status']==3 ? 'selected':'' ?>>3. 🚚 Đang giao hàng</option>
                        <option value="4" <?= $order['status']==4 ? 'selected':'' ?>>4. 🟢 Hoàn thành (Đã giao)</option>
                        <option value="5" <?= $order['status']==5 ? 'selected':'' ?>>5. 🔴 Hủy đơn hàng</option>
                    </select>

                    <button type="button" onclick="updateStatusAJAX()" class="btn btn-primary w-100 fw-bold">
                        Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa fa-address-card me-2"></i>Thông tin nhận hàng</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Khách hàng</label>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($order['fullname']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Liên hệ</label>
                    <div><?= $order['phone'] ?></div>
                    <div class="small"><?= htmlspecialchars($order['email']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Địa chỉ</label>
                    <div><?= htmlspecialchars($order['address']) ?></div>
                </div>
                <div class="mb-0">
                    <label class="small text-muted fw-bold text-uppercase">Ghi chú</label>
                    <div class="fst-italic bg-light p-2 rounded small text-secondary">
                        <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có ghi chú' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="invoice-template" class="d-none d-print-block">
    <div class="p-4" style="font-family: 'Times New Roman', Times, serif; color: #000;">
        
        <div class="row mb-4 border-bottom pb-3">
            <div class="col-6">
                <h2 class="fw-bold text-uppercase mb-1">FPT SHOP</h2>
                <p class="mb-0 small">Địa chỉ: 123 Nguyễn Trãi, Bắc Nha Trang, Nha Trang, Khánh Hòa</p>
                <p class="mb-0 small">Hotline: 1800 6601</p>
            </div>
            <div class="col-6 text-end">
                <h3 class="fw-bold mb-1">HÓA ĐƠN BÁN HÀNG</h3>
                <p class="mb-0">Mã đơn: <strong>#<?= $order['order_code'] ?></strong></p>
                <p class="mb-0 small">Ngày: <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <p class="mb-1"><strong>Khách hàng:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                <p class="mb-1"><strong>Điện thoại:</strong> <?= $order['phone'] ?></p>
                <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p class="mb-0"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></p>
            </div>
        </div>

        <table class="table table-bordered border-dark mb-4">
            <thead>
                <tr class="text-center">
                    <th style="width: 50px;">STT</th>
                    <th>Tên sản phẩm</th>
                    <th style="width: 100px;">Đơn giá</th>
                    <th style="width: 60px;">SL</th>
                    <th style="width: 120px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; foreach ($items as $item): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td>
                        <?= htmlspecialchars($item['product_name']) ?>
                        <div class="small fst-italic">Mã SP: <?= $item['product_id'] ?></div>
                    </td>
                    <td class="text-end"><?= number_format($item['price']) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold text-uppercase">Tổng thanh toán:</td>
                    <td class="text-end fw-bold fs-5"><?= number_format($order['total_money']) ?> đ</td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5">
            <div class="col-6 text-center">
                <p class="fw-bold">Người mua hàng</p>
                <p class="small fst-italic">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="col-6 text-center">
                <p class="fw-bold">Người bán hàng</p>
                <p class="small fst-italic">(Ký, ghi rõ họ tên)</p>
            </div>
        </div>
        
        <div class="text-center mt-5 pt-3 border-top small fst-italic">
            Cảm ơn quý khách đã mua hàng tại FPT Shop!
        </div>
    </div>
</div>

<script>
    // 1. Hàm in hóa đơn
    function printInvoice() {
        window.print();
    }

    // 2. AJAX Cập nhật trạng thái
    function updateStatusAJAX() {
        const form = document.getElementById('updateStatusForm');
        const formData = new FormData(form);

        Swal.fire({
            title: 'Đang xử lý...',
            didOpen: () => { Swal.showLoading() }
        });

        fetch('index.php?module=admin&controller=order&action=update_status', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Thành công', text: data.message, timer: 1000, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire('Lỗi', data.message, 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ', 'error');
        });
    }
</script>

<style>
    /* 1. Tắt Header/Footer mặc định của trình duyệt */
    @page {
        size: auto;
        margin: 0mm; /* Đặt lề trang in về 0 để ẩn tiêu đề và URL */
    }

    @media print {
        /* Ẩn tất cả mọi thứ mặc định */
        body * {
            visibility: hidden;
        }
        
        /* Ẩn Sidebar, Topbar, Header layout, nút in */
        .sidebar, .topbar, footer, .d-print-none { display: none !important; }

        /* Chỉ hiển thị vùng hóa đơn */
        #invoice-template, #invoice-template * {
            visibility: visible;
        }

        /* Định vị hóa đơn full màn hình trắng */
        #invoice-template {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20px; /* Thêm padding để nội dung không bị sát mép giấy quá */
            background: white;
            color: black !important;
        }
        
        /* Reset các style của Bootstrap gây ảnh hưởng khi in */
        .badge { border: 1px solid #000 !important; color: #000 !important; background: none !important; }
        .bg-light { background-color: #fff !important; } /* Đổi nền xám thành trắng cho sạch */
        
        /* Ẩn các đường link (href) hiển thị bên cạnh chữ */
        a[href]:after {
            content: none !important;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>