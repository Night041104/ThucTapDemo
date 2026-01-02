<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded overflow-hidden">
                <div class="card-header border-0 text-center text-white py-4" style="background: linear-gradient(135deg, #cd1818 0%, #a51212 100%);">
                    <?php
                        $defaultAvt = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
                        $avt = !empty($user['avatar']) ? $user['avatar'] : $defaultAvt;
                    ?>
                    <img src="<?= htmlspecialchars($avt) ?>" alt="Avatar" class="rounded-circle mb-2 bg-white p-1" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= $defaultAvt ?>'">
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($user['lname'] . ' ' . $user['fname']) ?></h6>
                    <small style="opacity: 0.8;"><?= htmlspecialchars($user['email']) ?></small>
                </div>
                
                <div class="list-group list-group-flush py-2">
                    <a href="index.php?controller=account&action=profile" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-danger bg-light border-start border-4 border-danger">
                        <i class="fa fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="index.php?controller=order&action=history" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
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
                <h4 class="mb-4 pb-3 border-bottom">Hồ sơ cá nhân</h4>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=account&action=update" method="POST" enctype="multipart/form-data">
                    
                    <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                        <img id="img-preview" src="<?= htmlspecialchars($avt) ?>" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover; margin-right: 20px;" onerror="this.src='<?= $defaultAvt ?>'">
                        <div>
                            <label for="file-upload" class="btn btn-outline-secondary btn-sm mb-1">
                                <i class="fa fa-camera"></i> Đổi ảnh đại diện
                            </label>
                            <input id="file-upload" type="file" name="avatar" style="display: none;" onchange="previewImage(this)">
                            <div class="text-muted small">Dung lượng tối đa 1MB (JPG, PNG)</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ (Last Name)</label>
                            <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($user['lname']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên (First Name)</label>
                            <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($user['fname']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold mb-3">Địa chỉ nhận hàng</label>
                            
                            <?php if(!empty($user['city'])): ?>
                                <div class="alert alert-light border border-danger d-flex align-items-center mb-3">
                                    <i class="fa fa-map-marker-alt text-danger fs-4 me-3"></i>
                                    <div>
                                        <strong>Địa chỉ hiện tại:</strong><br>
                                        <?= htmlspecialchars($user['street_address']) ?>, 
                                        <?= htmlspecialchars($user['ward']) ?>, 
                                        <?= htmlspecialchars($user['district']) ?>, 
                                        <?= htmlspecialchars($user['city']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <select id="province" class="form-select"><option value="0">Tỉnh/Thành phố</option></select>
                                </div>
                                <div class="col-md-4">
                                    <select id="district" class="form-select"><option value="0">Quận/Huyện</option></select>
                                </div>
                                <div class="col-md-4">
                                    <select id="ward" class="form-select"><option value="0">Phường/Xã</option></select>
                                </div>
                            </div>

                            <input type="text" name="street_address" class="form-control" 
                                   value="<?= htmlspecialchars($user['street_address'] ?? '') ?>" 
                                   placeholder="Số nhà, tên đường cụ thể">
                            
                            <input type="hidden" name="city" id="city_text" value="<?= $user['city'] ?? '' ?>">
                            <input type="hidden" name="district" id="district_text" value="<?= $user['district'] ?? '' ?>">
                            <input type="hidden" name="ward" id="ward_text" value="<?= $user['ward'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">
                            <i class="fa fa-save me-1"></i> LƯU THAY ĐỔI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/js/address_auto.js"></script>