<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">Cập nhật thành viên</h3>
    <a href="admin/user" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-dark text-white text-center py-4 rounded-top">
                <h5 class="mb-0 fw-bold text-uppercase ls-1">Phân quyền & Trạng thái</h5>
            </div>
            
            <div class="card-body p-4 text-center">
                <div class="position-relative d-inline-block mb-3">
                    <?php
                        $defaultAvt = 'public/uploads/default/default_avt.png';
                        $avt = !empty($user['avatar']) ? $user['avatar'] : $defaultAvt;
                    ?>
                    <img src="<?= htmlspecialchars($avt) ?>" 
                         class="rounded-circle border border-4 border-white shadow" 
                         style="width: 120px; height: 120px; object-fit: cover; margin-top: -60px; background: #fff;"
                         onerror="this.src='<?= $defaultAvt ?>'">
                </div>

                <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h4>
                <p class="text-muted mb-4"><i class="fa fa-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></p>

                <hr class="dashed mb-4">

                <form action="" method="POST" class="text-start">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Vai trò hệ thống</label>
                        <div class="card border bg-light">
                            <div class="card-body p-2">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="role_id" id="role_user" value="0" <?= $user['role_id'] == 0 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_user">
                                        👤 <b>Khách hàng (User)</b>
                                        <div class="small text-muted ps-1">Chỉ có thể mua hàng và xem lịch sử đơn hàng.</div>
                                    </label>
                                </div>
                                <hr class="my-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role_id" id="role_admin" value="1" <?= $user['role_id'] == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger" for="role_admin">
                                        👑 <b>Quản trị viên (Admin)</b>
                                        <div class="small text-muted ps-1">Toàn quyền truy cập vào trang quản trị này.</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái tài khoản</label>
                        <select name="is_verified" class="form-select form-select-lg">
                            <option value="1" <?= $user['is_verified'] == 1 ? 'selected' : '' ?>>✅ Đã kích hoạt (Hoạt động)</option>
                            <option value="0" <?= $user['is_verified'] == 0 ? 'selected' : '' ?>>⛔ Bị khóa / Chưa kích hoạt</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa fa-save me-2"></i> LƯU THAY ĐỔI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    hr.dashed { border-top: 1px dashed #e0e0e0; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>