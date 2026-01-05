<style>
    /* (Giữ nguyên các style cũ) */
    .checkout-container { max-width: 1100px; margin: 40px auto; display: flex; gap: 30px; font-family: 'Roboto', sans-serif; }
    .checkout-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .left-col { flex: 1.6; }
    .right-col { flex: 1; height: fit-content; position: sticky; top: 20px; }
    h2.section-title { margin-top: 0; color: #333; border-bottom: 2px solid #f4f4f4; padding-bottom: 15px; margin-bottom: 25px; font-size: 18px; font-weight: 700; text-transform: uppercase; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #444; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 14px; transition: 0.3s; }
    .form-control:focus { border-color: #cd1818; outline: none; box-shadow: 0 0 0 3px rgba(205, 24, 24, 0.1); }
    .form-control:valid { border-color: #28a745; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right calc(0.375em + 0.1875rem) center; background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem); }
    textarea.form-control { height: 100px; resize: vertical; }
    .order-summary table { width: 100%; font-size: 14px; border-collapse: collapse; }
    .order-summary td { padding: 12px 0; border-bottom: 1px dashed #eee; vertical-align: middle; }
    .total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 2px solid #f4f4f4; }
    .total-label { font-size: 15px; font-weight: normal; color: #555; }
    .total-price { font-size: 22px; font-weight: 800; color: #cd1818; }
    .btn-confirm { width: 100%; padding: 15px; background: #cd1818; color: white; border: none; font-weight: 700; font-size: 16px; border-radius: 6px; cursor: pointer; margin-top: 25px; text-transform: uppercase; transition: background 0.3s; box-shadow: 0 4px 6px rgba(205, 24, 24, 0.2); }
    .btn-confirm:hover { background: #b0181d; transform: translateY(-1px); }
    .payment-methods { margin: 25px 0; background: #fcfcfc; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
    .payment-option { display: flex; align-items: center; margin-bottom: 12px; cursor: pointer; padding: 12px; border-radius: 6px; border: 1px solid #eee; background: white; transition: 0.2s; }
    .payment-option:hover { border-color: #cd1818; background: #fff5f5; }
    .payment-option input { margin-right: 12px; accent-color: #cd1818; transform: scale(1.1); }
    
    /* STYLE MỚI CHO TOGGLE */
    .toggle-address { margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-weight: 600; color: #007bff; cursor: pointer; }
    .toggle-address input { accent-color: #007bff; width: 18px; height: 18px; cursor: pointer; }
    
    @media (max-width: 768px) { .checkout-container { flex-direction: column; padding: 0 15px; } .right-col { position: static; } }
</style>

<form id="checkoutForm" action="index.php?controller=checkout&action=submit" method="POST">
    <div class="checkout-container">
        
        <div class="checkout-box left-col">
            <h2 class="section-title">🚚 Thông tin giao hàng</h2>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Họ và tên người nhận <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control" required minlength="3" maxlength="50"
                           placeholder="Ví dụ: Nguyễn Văn A"
                           value="<?= isset($user['fullname']) ? htmlspecialchars($user['fullname']) : '' ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" required 
                           pattern="(03|05|07|08|09)[0-9]{8}" maxlength="10" minlength="10"
                           title="10 số, đầu số nhà mạng VN"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           value="<?= isset($user['phone']) ? htmlspecialchars($user['phone']) : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email nhận hóa đơn <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required placeholder="email@example.com"
                       value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                
                <input type="text" name="street_address" class="form-control mb-3" 
                       value="<?= isset($user['street_address']) ? htmlspecialchars($user['street_address']) : '' ?>" 
                       placeholder="Số nhà, tên đường, tòa nhà..." required minlength="5">

                <?php 
                // Kiểm tra có địa chỉ mặc định hợp lệ không
                $hasDefault = !empty($user['city']) && !empty($user['district_id']) && !empty($user['ward_code']);
                ?>

                <?php if ($hasDefault): ?>
                    <div id="default_address_block">
                        <div class="alert alert-success d-flex align-items-center p-2 small">
                            <i class="fa fa-check-circle me-2"></i>
                            <div>
                                Đang dùng địa chỉ mặc định: 
                                <b><?= htmlspecialchars($user['ward']) ?>, <?= htmlspecialchars($user['district']) ?>, <?= htmlspecialchars($user['city']) ?></b>
                            </div>
                        </div>

                        <input type="hidden" name="city" class="default-input" value="<?= htmlspecialchars($user['city']) ?>">
                        <input type="hidden" name="district" class="default-input" value="<?= htmlspecialchars($user['district']) ?>">
                        <input type="hidden" name="ward" class="default-input" value="<?= htmlspecialchars($user['ward']) ?>">
                        <input type="hidden" name="district_id" class="default-input" value="<?= htmlspecialchars($user['district_id']) ?>">
                        <input type="hidden" name="ward_code" class="default-input" value="<?= htmlspecialchars($user['ward_code']) ?>">
                    </div>

                    <label class="toggle-address">
                        <input type="checkbox" id="change_address_cb"> 
                        Giao hàng đến địa chỉ khác
                    </label>
                <?php endif; ?>

                <div id="new_address_block" style="<?= $hasDefault ? 'display:none;' : '' ?>">
                    <?php if(!empty($user['city']) && !$hasDefault): ?>
                        <div class="alert alert-warning small py-2 mb-2">
                            <i class="fa fa-info-circle"></i> Vui lòng cập nhật lại định danh xã/phường.
                        </div>
                    <?php endif; ?>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <select id="province" class="form-control new-input">
                                <option value="">-- Tỉnh/Thành --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="district" class="form-control new-input">
                                <option value="">-- Quận/Huyện --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="ward" class="form-control new-input">
                                <option value="">-- Phường/Xã --</option>
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" name="city" id="city_text" class="new-input">
                    <input type="hidden" name="district" id="district_text" class="new-input">
                    <input type="hidden" name="ward" id="ward_text" class="new-input">
                    <input type="hidden" name="district_id" id="district_id" class="new-input">
                    <input type="hidden" name="ward_code" id="ward_code" class="new-input">
                </div>
            </div>

            <div class="form-group">
                <label>Ghi chú đơn hàng (Tùy chọn)</label>
                <textarea name="note" class="form-control" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
            </div>
        </div>

        <div class="checkout-box right-col">
            <h2 class="section-title">📦 Đơn hàng của bạn</h2>
            <div class="order-summary">
                <table>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($p['name']) ?></strong>
                                <small style="color: #777;">x<?= $_SESSION['cart'][$p['id']] ?></small>
                            </td>
                            <td align="right">
                                <?= number_format($p['price'] * $_SESSION['cart'][$p['id']], 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="border-top: 1px solid #eee;">
                        <td style="padding-top: 15px;">Tạm tính:</td>
                        <td style="padding-top: 15px; text-align: right; font-weight: 600;">
                            <?= number_format($totalMoney, 0, ',', '.') ?>₫
                        </td>
                    </tr>
                    <?php if(isset($discountMoney) && $discountMoney > 0): ?>
                    <tr>
                        <td style="color: #28a745;">Mã giảm giá</td>
                        <td style="text-align: right; color: #28a745;">-<?= number_format($discountMoney, 0, ',', '.') ?>₫</td>
                    </tr>
                    <?php endif; ?>
                </table>
                <div class="total-row">
                    <span class="total-label">Tổng thanh toán:</span>
                    <span class="total-price"><?= number_format($finalTotal, 0, ',', '.') ?>₫</span>
                </div>
            </div>

            <div class="payment-methods">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #555;">Phương thức thanh toán</h3>
                <?php if ($finalTotal <= 50000000): ?>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="COD" checked> 
                        <span>💵 Thanh toán khi nhận hàng (COD)</span>
                    </label>
                <?php else: ?>
                    <div class="alert alert-warning small p-2 mb-2">
                        Vui lòng thanh toán Online cho đơn > 50tr.
                    </div>
                <?php endif; ?>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="VNPAY" <?= ($finalTotal > 50000000) ? 'checked' : '' ?>> 
                    <span>💳 Thanh toán Online qua VNPAY</span>
                </label>
            </div>
            <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/js/address_auto.js"></script>

<script>
    $(document).ready(function() {
        // --- LOGIC 1: XỬ LÝ CHECKBOX "GIAO ĐỊA CHỈ KHÁC" ---
        var hasDefault = <?= $hasDefault ? 'true' : 'false' ?>;

        // Hàm bật/tắt các ô input dựa trên trạng thái
        function toggleAddressMode(useNewAddress) {
            if (useNewAddress) {
                // 1. Ẩn block mặc định, Hiện block mới
                $('#default_address_block').hide();
                $('#new_address_block').slideDown();

                // 2. DISABLE input mặc định (để server không nhận nó)
                $('.default-input').prop('disabled', true);

                // 3. ENABLE input mới (để server nhận nó)
                $('.new-input').prop('disabled', false);
            } else {
                // Ngược lại
                $('#default_address_block').slideDown();
                $('#new_address_block').hide();
                $('.default-input').prop('disabled', false);
                $('.new-input').prop('disabled', true);
            }
        }

        // Khởi tạo trạng thái ban đầu
        if (hasDefault) {
            $('.new-input').prop('disabled', true); // Mặc định tắt ô nhập mới
        }

        // Sự kiện khi bấm checkbox
        $('#change_address_cb').change(function() {
            toggleAddressMode(this.checked);
        });

        // --- LOGIC 2: VALIDATION FORM ---
        $('#checkoutForm').on('submit', function(e) {
            // Kiểm tra xem đang ở chế độ nào
            var isUsingNewAddress = $('#new_address_block').is(':visible');

            // Chỉ validate dropdown nếu đang dùng địa chỉ mới
            if (isUsingNewAddress) {
                var province = $('#province').val();
                var district = $('#district').val();
                var ward = $('#ward').val();

                if (!province || province == '0') {
                    alert('Vui lòng chọn Tỉnh/Thành phố!');
                    $('#province').focus();
                    e.preventDefault(); return false;
                }
                if (!district || district == '0') {
                    alert('Vui lòng chọn Quận/Huyện!');
                    $('#district').focus();
                    e.preventDefault(); return false;
                }
                if (!ward || ward == '0') {
                    alert('Vui lòng chọn Phường/Xã!');
                    $('#ward').focus();
                    e.preventDefault(); return false;
                }
            }

            // Validate Phone
            var phone = $('input[name="phone"]').val();
            var phoneRegex = /(03|05|07|08|09)+([0-9]{8})\b/;
            if (!phoneRegex.test(phone)) {
                alert('Số điện thoại không hợp lệ (10 số, đầu mạng VN)!');
                $('input[name="phone"]').focus();
                e.preventDefault(); return false;
            }
            return true;
        });
    });
</script>