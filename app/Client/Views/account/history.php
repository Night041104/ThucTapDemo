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
                    <a href="tai-khoan" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="lich-su-don" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-danger bg-light border-start border-4 border-danger">
                        <i class="fa fa-shopping-bag me-2"></i> Quản lý đơn hàng
                    </a>
                    <a href="doi-mat-khau" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-lock me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="dang-xuat" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
                        <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card border-0 shadow-sm p-4">
                <h4 class="mb-4 pb-3 border-bottom">Lịch sử mua hàng</h4>

                <?php if(empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-box-open text-muted" style="font-size: 60px; margin-bottom: 20px;"></i>
                        <p class="text-muted">Bạn chưa có đơn hàng nào!</p>
                        <a href="index.php" class="btn btn-danger mt-2">Mua sắm ngay</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $o): ?>
                                    <tr>
                                        <td><strong class="text-dark">#<?= $o['order_code'] ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                                        <td class="text-danger fw-bold"><?= number_format($o['total_money']) ?>₫</td>
                                        <td>
                                            <?php 
                                                $st = $o['status'];
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
                                            <span class="badge <?= $bClass ?> rounded-pill fw-normal px-3"><?= $bText ?></span>
                                        </td>
                                        <td>
                                            <a href="chi-tiet-don/<?= $o['id'] ?>" class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>