<style>
    /* CSS Riêng cho trang Thanh toán */
    .checkout-container { 
        max-width: 1100px; 
        margin: 40px auto; 
        display: flex; 
        gap: 30px; 
        font-family: 'Roboto', sans-serif;
    }
    
    .checkout-box { 
        background: white; 
        padding: 30px; 
        border-radius: 8px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
        border: 1px solid #eee;
    }
    .left-col { flex: 1.6; }
    .right-col { flex: 1; height: fit-content; position: sticky; top: 20px; }
    
    h2.section-title { 
        margin-top: 0; 
        color: #333; 
        border-bottom: 2px solid #f4f4f4; 
        padding-bottom: 15px; 
        margin-bottom: 25px; 
        font-size: 18px; 
        font-weight: 700;
        text-transform: uppercase;
    }
    
    /* Form Styles */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #444; }
    .form-control { 
        width: 100%; 
        padding: 12px 15px; 
        border: 1px solid #ddd; 
        border-radius: 6px; 
        box-sizing: border-box; 
        font-size: 14px; 
        transition: 0.3s;
    }
    .form-control:focus { border-color: #cd1818; outline: none; box-shadow: 0 0 0 3px rgba(205, 24, 24, 0.1); }
    textarea.form-control { height: 100px; resize: vertical; }
    
    /* Order Summary Styles */
    .order-summary table { width: 100%; font-size: 14px; border-collapse: collapse; }
    .order-summary td { padding: 12px 0; border-bottom: 1px dashed #eee; vertical-align: middle; }
    .order-summary td strong { font-weight: 600; color: #333; display: block; margin-bottom: 4px; }
    
    .total-row { 
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 20px; padding-top: 20px; border-top: 2px solid #f4f4f4;
    }
    .total-label { font-size: 15px; font-weight: normal; color: #555; }
    .total-price { font-size: 22px; font-weight: 800; color: #cd1818; }
    
    /* Button Styles */
    .btn-confirm { 
        width: 100%; padding: 15px; 
        background: #cd1818; color: white; 
        border: none; font-weight: 700; font-size: 16px; 
        border-radius: 6px; cursor: pointer; margin-top: 25px; 
        text-transform: uppercase; transition: background 0.3s; 
        box-shadow: 0 4px 6px rgba(205, 24, 24, 0.2);
    }
    .btn-confirm:hover { background: #b0181d; transform: translateY(-1px); }
    
    /* Payment Method Styles */
    .payment-methods { margin: 25px 0; background: #fcfcfc; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
    .payment-option { 
        display: flex; align-items: center; 
        margin-bottom: 12px; cursor: pointer; 
        padding: 12px; border-radius: 6px; border: 1px solid #eee;
        background: white; transition: 0.2s;
    }
    .payment-option:last-child { margin-bottom: 0; }
    .payment-option:hover { border-color: #cd1818; background: #fff5f5; }
    .payment-option input { margin-right: 12px; accent-color: #cd1818; transform: scale(1.1); }
    .payment-option span { font-weight: 500; font-size: 14px; }

    /* Responsive */
    @media (max-width: 768px) {
        .checkout-container { flex-direction: column; padding: 0 15px; }
        .right-col { position: static; }
    }
</style>

<form action="index.php?controller=checkout&action=submit" method="POST">
    <div class="checkout-container">
        
        <div class="checkout-box left-col">
            <h2 class="section-title">🚚 Thông tin giao hàng</h2>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Họ và tên người nhận <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control" required placeholder="Nhập họ tên..."
                           value="<?= isset($user['fullname']) ? htmlspecialchars($user['fullname']) : '' ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" required placeholder="Nhập số điện thoại..."
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
                
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <select id="province" class="form-control"><option value="0">Tỉnh/Thành</option></select>
                    </div>
                    <div class="col-md-4">
                        <select id="district" class="form-control"><option value="0">Quận/Huyện</option></select>
                    </div>
                    <div class="col-md-4">
                        <select id="ward" class="form-control"><option value="0">Phường/Xã</option></select>
                    </div>
                </div>

                <input type="text" name="street_address" class="form-control" 
                       value="<?= isset($user['street_address']) ? htmlspecialchars($user['street_address']) : '' ?>" 
                       placeholder="Số nhà, tên đường, tòa nhà..." required>

                <?php if(!empty($user['city'])): ?>
                    <div class="mt-2 text-success small">
                        <i class="fa fa-check-circle"></i> Sử dụng địa chỉ mặc định: 
                        <b><?= $user['street_address'] ?>, <?= $user['ward'] ?>, <?= $user['district'] ?>, <?= $user['city'] ?></b>
                    </div>
                    <input type="hidden" name="city" id="city_text" value="<?= $user['city'] ?>">
                    <input type="hidden" name="district" id="district_text" value="<?= $user['district'] ?>">
                    <input type="hidden" name="ward" id="ward_text" value="<?= $user['ward'] ?>">
                <?php else: ?>
                    <input type="hidden" name="city" id="city_text">
                    <input type="hidden" name="district" id="district_text">
                    <input type="hidden" name="ward" id="ward_text">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ghi chú đơn hàng (Tùy chọn)</label>
                <textarea name="note" class="form-control" placeholder="Ví dụ: Giao hàng giờ hành chính, gọi trước khi giao..."></textarea>
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
                                <small style="color: #777;">Số lượng: <?= $_SESSION['cart'][$p['id']] ?></small>
                            </td>
                            <td align="right" style="white-space: nowrap; font-weight: 500;">
                                <?= number_format($p['price'] * $_SESSION['cart'][$p['id']], 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr style="border-top: 1px solid #eee;">
                        <td style="padding-top: 15px; color: #666;">Tạm tính:</td>
                        <td style="padding-top: 15px; text-align: right; font-weight: 600;">
                            <?= number_format($totalMoney, 0, ',', '.') ?>₫
                        </td>
                    </tr>

                    <?php if(isset($discountMoney) && $discountMoney > 0): ?>
                    <tr>
                        <td style="color: #28a745;">
                            <i class="fa fa-tag"></i> Mã giảm giá (<?= htmlspecialchars($_SESSION['coupon']['code']) ?>)
                        </td>
                        <td style="text-align: right; color: #28a745; font-weight: bold;">
                            -<?= number_format($discountMoney, 0, ',', '.') ?>₫
                        </td>
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
                
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="COD" checked> 
                    <span>💵 Thanh toán khi nhận hàng (COD)</span>
                </label>
                
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="VNPAY"> 
                    <span>💳 Thanh toán Online qua VNPAY</span>
                </label>
            </div>

            <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="gio-hang" style="text-decoration: none; color: #666; font-size: 13px; border-bottom: 1px dashed #999;">
                    <i class="fa fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </div>
        </div>

    </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="public/js/address_auto.js"></script>