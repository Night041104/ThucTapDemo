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
                    <a href="tai-khoan" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-danger bg-light border-start border-4 border-danger">
                        <i class="fa fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="lich-su-don" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-500 text-secondary">
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

                <form id="profileForm" action="index.php?controller=account&action=update" method="POST" enctype="multipart/form-data">
                    
                    <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                        <img id="img-preview" src="<?= htmlspecialchars($avt) ?>" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover; margin-right: 20px;" onerror="this.src='<?= $defaultAvt ?>'">
                        <div>
                            <label for="file-upload" class="btn btn-outline-secondary btn-sm mb-1">
                                <i class="fa fa-camera"></i> Đổi ảnh đại diện
                            </label>
                            <input id="file-upload" type="file" name="avatar" style="display: none;" accept="image/*" onchange="previewImage(this)">
                            <div class="text-muted small">Dung lượng tối đa 1MB (JPG, PNG)</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ (Last Name) <span class="text-danger">*</span></label>
                            <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($user['lname']) ?>" required minlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên (First Name) <span class="text-danger">*</span></label>
                            <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($user['fname']) ?>" required minlength="2">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                   pattern="(03|05|07|08|09)[0-9]{8}"
                                   maxlength="10" minlength="10" required
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   title="Số điện thoại phải có 10 chữ số và bắt đầu bằng 03, 05, 07, 08, 09">
                            <div class="invalid-feedback">Số điện thoại không hợp lệ.</div>
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
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="changeAddress">
                                    <label class="form-check-label" for="changeAddress">
                                        Tôi muốn thay đổi địa chỉ
                                    </label>
                                </div>
                            <?php endif; ?>

                            <div id="address-container" style="<?= !empty($user['city']) ? 'display:none;' : '' ?>">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <select id="province" class="form-select"><option value="">-- Tỉnh/Thành --</option></select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="district" class="form-select"><option value="">-- Quận/Huyện --</option></select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="ward" class="form-select"><option value="">-- Phường/Xã --</option></select>
                                    </div>
                                </div>

                                <input type="text" name="street_address" class="form-control" 
                                       id="street_input"
                                       value="<?= htmlspecialchars($user['street_address'] ?? '') ?>" 
                                       placeholder="Số nhà, tên đường cụ thể">
                                
                                <input type="hidden" name="city" id="city_text" value="<?= $user['city'] ?? '' ?>">
                                <input type="hidden" name="district" id="district_text" value="<?= $user['district'] ?? '' ?>">
                                <input type="hidden" name="ward" id="ward_text" value="<?= $user['ward'] ?? '' ?>">
                                <input type="hidden" name="district_id" id="district_id" value="<?= $user['district_id'] ?? '' ?>">
                                <input type="hidden" name="ward_code" id="ward_code" value="<?= $user['ward_code'] ?? '' ?>">
                            </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/js/address_auto.js"></script>

<script>
    // 1. Preview Ảnh
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // 2. Logic ẩn/hiện địa chỉ
        $('#changeAddress').change(function() {
            if(this.checked) {
                $('#address-container').slideDown();
                // Reset các trường hidden để bắt buộc chọn lại
                $('#city_text').val(''); 
                $('#district_text').val('');
                $('#ward_text').val('');
            } else {
                $('#address-container').slideUp();
                // Nếu bỏ chọn, có thể cần logic khôi phục lại giá trị cũ (tùy nhu cầu)
                // Ở đây ta cứ để submit, Controller sẽ kiểm tra nếu 3 ô select rỗng thì giữ nguyên địa chỉ cũ
            }
        });

        // 3. Validation Form khi Submit
        $('#profileForm').on('submit', function(e) {
            let isValid = true;
            let errorMsg = '';

            // Validate Phone
            const phone = $('input[name="phone"]').val();
            const phoneRegex = /(03|05|07|08|09)+([0-9]{8})\b/;
            if (!phoneRegex.test(phone)) {
                isValid = false;
                errorMsg += '- Số điện thoại không hợp lệ (Phải là 10 số, đầu mạng VN)\n';
                $('input[name="phone"]').addClass('is-invalid');
            } else {
                $('input[name="phone"]').removeClass('is-invalid');
            }

            // Validate Address (Chỉ khi vùng chọn địa chỉ đang hiện)
            if ($('#address-container').is(':visible')) {
                const city = $('#province').val();
                const district = $('#district').val();
                const ward = $('#ward').val();
                const street = $('input[name="street_address"]').val().trim();

                // Nếu user đã mở vùng chọn địa chỉ thì bắt buộc phải chọn đủ 3 cấp
                // (Trừ khi họ muốn xóa địa chỉ - nhưng thường profile ko ai làm vậy)
                if (city === "" || district === "" || ward === "") {
                    // Logic: Nếu chưa chọn gì cả (để trống hết) -> Có thể server sẽ giữ nguyên
                    // Nhưng nếu đã chọn Tỉnh mà chưa chọn Huyện -> Lỗi
                    if(city !== "" || street !== "") {
                         if(city === "" || district === "" || ward === "") {
                             isValid = false;
                             errorMsg += '- Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện, Phường/Xã\n';
                         }
                    }
                }
                
                if (street === "" && city !== "") {
                    isValid = false;
                    errorMsg += '- Vui lòng nhập số nhà/tên đường\n';
                     $('input[name="street_address"]').addClass('is-invalid');
                } else {
                     $('input[name="street_address"]').removeClass('is-invalid');
                }
            }

            if (!isValid) {
                e.preventDefault(); // Chặn submit
                alert('Vui lòng kiểm tra lại thông tin:\n' + errorMsg);
            }
        });
    });
</script>