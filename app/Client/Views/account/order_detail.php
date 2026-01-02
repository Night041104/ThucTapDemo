<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded overflow-hidden">
                <div class="card-header border-0 text-center text-white py-4" style="background: linear-gradient(135deg, #cd1818 0%, #a51212 100%);">
                    <?php 
                        $u = $_SESSION['user'];
                        $defaultAvt = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                        $avt = !empty($u['avatar']) ? $u['avatar'] : $defaultAvt;
                    ?>
                    <img src="<?= htmlspecialchars($avt) ?>" class="rounded-circle mb-2 bg-white p-1" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= $defaultAvt ?>'">
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($u['lname'] . ' ' . $u['fname']) ?></h6>
                </div>
                <div class="list-group list-group-flush py-2">
                    <a href="index.php?controller=account&action=profile" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="index.php?controller=order&action=history" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-danger bg-light border-start border-4 border-danger">
                        <i class="fa fa-shopping-bag me-2"></i> Quản lý đơn hàng
                    </a>
                    <a href="index.php?controller=account&action=changePassword" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-lock me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="index.php?module=client&controller=auth&action=logout" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card border-0 shadow-sm p-4">
                
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <div>
                        <a href="index.php?controller=order&action=history" class="text-decoration-none text-muted mb-1 d-block small">
                            <i class="fa fa-arrow-left"></i> Quay lại
                        </a>
                        <h4 class="mb-0">Đơn hàng #<?= $order['order_code'] ?></h4>
                        <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                    </div>
                    <div>
                        <?php 
                            $st = $order['status'];
                            $badges = [
                                1 => ['bg-warning text-dark', 'Chờ xử lý'],
                                2 => ['bg-primary', 'Đã xác nhận'],
                                3 => ['bg-info text-dark', 'Đang giao'],
                                4 => ['bg-success', 'Thành công'],
                                5 => ['bg-danger', 'Đã hủy']
                            ];
                            $bClass = $badges[$st][0] ?? 'bg-secondary';
                            $bText  = $badges[$st][1] ?? 'Không rõ';
                        ?>
                        <span class="badge <?= $bClass ?> fs-6 px-3 py-2"><?= $bText ?></span>
                    </div>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded h-100">
                            <h6 class="text-danger fw-bold border-bottom border-secondary pb-2 mb-2">ĐỊA CHỈ NHẬN HÀNG</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($order['fullname']) ?></strong></p>
                            <p class="mb-1 text-muted small">SĐT: <?= htmlspecialchars($order['phone']) ?></p>
                            <p class="mb-0 text-muted small">ĐC: <?= htmlspecialchars($order['address']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded h-100">
                            <h6 class="text-danger fw-bold border-bottom border-secondary pb-2 mb-2">THANH TOÁN & GHI CHÚ</h6>
                            <p class="mb-1 text-muted small">
                                Hình thức: <strong><?= $order['payment_method'] == 'COD' ? 'Thanh toán khi nhận (COD)' : 'VNPAY Online' ?></strong>
                            </p>
                            <p class="mb-0 text-muted small">
                                Ghi chú: <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $tempTotal = 0; ?>
                            <?php foreach($items as $item): ?>
                                <?php $sub = $item['price'] * $item['quantity']; $tempTotal += $sub; ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td class="text-center"><?= number_format($item['price']) ?>₫</td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold"><?= number_format($sub) ?>₫</td>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <div style="width: 300px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?= number_format($tempTotal) ?>₫</span>
                        </div>
                        <?php if($order['discount_money'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá:</span>
                            <span>-<?= number_format($order['discount_money']) ?>₫</span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fw-bold fs-5">TỔNG CỘNG:</span>
                            <span class="fw-bold fs-5 text-danger"><?= number_format($order['total_money']) ?>₫</span>
                        </div>

                        <?php if($order['status'] == 1): ?>
                            <div class="text-end mt-4">
                                <a href="index.php?controller=order&action=cancel&id=<?= $order['id'] ?>" 
                                   class="btn btn-outline-danger"
                                   onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này chứ?');">
                                   Hủy đơn hàng
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>