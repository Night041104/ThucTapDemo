<style>
    /* --- CSS CƠ BẢN GIỎ HÀNG --- */
    .cart-container { 
        max-width: 1000px; margin: 30px auto 50px auto; 
        background: white; padding: 30px; border-radius: 8px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    }
    h2.cart-title { 
        margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 15px; 
        font-size: 24px; color: #333; font-weight: 700; text-transform: uppercase; 
    }
    
    /* Table */
    .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
    .cart-table th { background: #f8f9fa; text-align: left; font-weight: 600; color: #555; text-transform: uppercase; font-size: 13px; }
    .cart-table td { vertical-align: middle; }
    .product-name { text-align: left; font-weight: 600; color: #333; font-size: 15px; }

    /* Input số lượng */
    .qty-input { 
        width: 60px; text-align: center; padding: 5px; 
        border: 1px solid #ddd; border-radius: 4px; outline: none; transition: 0.2s;
    }
    .qty-input:focus { border-color: #cd1818; box-shadow: 0 0 5px rgba(205, 24, 24, 0.2); }

    /* Buttons */
    .btn-delete { color: #999; text-decoration: none; font-size: 20px; transition: 0.2s; }
    .btn-delete:hover { color: #dc3545; }
    .btn-continue { display: inline-block; margin-top: 20px; text-decoration: none; color: #555; font-weight: 600; }
    .btn-continue:hover { color: #cd1818; }
    .btn-confirm { 
        display: block; width: 100%; text-align: center; background: #cd1818; color: white; 
        padding: 15px; font-weight: bold; text-decoration: none; border-radius: 4px; 
        text-transform: uppercase; margin-top: 15px; transition: 0.2s;
    }
    .btn-confirm:hover { background: #a50e0e; }

    /* Summary Box */
    .summary-box { background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #eee; }
    .row-total { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
    .final-price { font-size: 24px; color: #cd1818; font-weight: 800; }

    /* Form Coupon */
    .input-coupon { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; outline: none; }
    .input-coupon:focus { border-color: #cd1818; }
    .btn-apply { background: #333; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; }
    .btn-apply:hover { background: #000; }

    /* --- CSS CHO MODAL DANH SÁCH MÃ --- */
    .coupon-item {
        border: 1px dashed #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px;
        position: relative; cursor: pointer; transition: 0.2s; background: #fff;
        display: flex; justify-content: space-between; align-items: center;
    }
    .coupon-item:hover { border-color: #cd1818; background: #fff5f5; }
    
    /* Trạng thái Disabled */
    .coupon-disabled {
        background: #f9f9f9; opacity: 0.6; cursor: not-allowed; border-style: solid;
    }
    .coupon-disabled:hover { border-color: #ccc; background: #f9f9f9; }

    .coupon-code-text { font-weight: bold; color: #cd1818; font-size: 16px; }
    .coupon-desc { font-size: 13px; color: #555; margin-top: 5px; }
    
    .btn-select-coupon {
        padding: 5px 15px; font-size: 12px; border-radius: 20px;
        border: 1px solid #cd1818; color: #cd1818; background: white; cursor: pointer;
    }
    .btn-select-coupon:hover { background: #cd1818; color: white; }
    .btn-select-coupon.disabled { border-color: #999; color: #999; cursor: not-allowed; }
    .btn-select-coupon.disabled:hover { background: white; color: #999; }
</style>

<div class="cart-container">
    <h2 class="cart-title"><i class="fa fa-shopping-cart me-2"></i> Giỏ hàng của bạn</h2>

    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 80px 20px;">
            <i class="fa fa-shopping-basket" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; color: #666; margin-bottom: 30px;">Giỏ hàng của bạn đang trống!</p>
            <a href="index.php" class="btn-confirm" style="width: 250px; margin: 0 auto; background: #333;">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th width="45%">Sản phẩm</th>
                    <th width="15%">Đơn giá</th>
                    <th width="15%" class="text-center">Số lượng</th>
                    <th width="15%">Thành tiền</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <?php 
                        $qty = $_SESSION['cart'][$p['id']];
                        $subtotal = $p['price'] * $qty; 
                    ?>
                    <tr>
                        <td class="product-name">
                            <div class="d-flex align-items-center gap-3">
                                <?php if(!empty($p['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($p['thumbnail']) ?>" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; padding: 2px;">
                                <?php endif; ?>
                                <div>
                                    <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </a>
                                    <div style="font-size: 12px; color: #999; margin-top: 4px;">Mã: <?= $p['sku'] ?? $p['id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 500;"><?= number_format($p['price'], 0, ',', '.') ?>₫</td>
                        <td class="text-center">
                            <input type="number" class="qty-input" data-id="<?= $p['id'] ?>" 
                                   value="<?= $qty ?>" min="1">
                        </td>
                        <td style="font-weight: 700; color: #cd1818;" id="subtotal-<?= $p['id'] ?>">
                            <?= number_format($subtotal, 0, ',', '.') ?>₫
                        </td>
                        <td>
                            <a href="index.php?controller=cart&action=delete&id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')" title="Xóa">
                                <i class="fa fa-times-circle"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="row mt-5">
            <div class="col-md-6">
                <h3 style="font-size: 16px; margin-bottom: 15px; font-weight: 700; text-transform: uppercase;">🎫 Mã giảm giá / Quà tặng</h3>
                
                <div class="d-flex gap-2 mb-2">
                    <form id="form-apply-coupon" action="index.php?controller=cart&action=applyCoupon" method="POST" class="d-flex flex-grow-1 gap-2">
                        <input type="text" id="input-coupon-code" name="code" class="input-coupon" placeholder="Nhập hoặc chọn mã" 
                               value="<?= isset($_SESSION['coupon']) ? $_SESSION['coupon']['code'] : '' ?>" required>
                        <button type="submit" class="btn-apply">Áp dụng</button>
                    </form>
                    
                    <button type="button" class="btn btn-outline-danger" style="border: 1px solid #cd1818; color: #cd1818; background: white; padding: 0 15px; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#couponModal">
                        <i class="fa fa-ticket-alt"></i> Chọn mã
                    </button>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-2 py-2 small">
                        <i class="fa fa-exclamation-circle me-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success mt-2 py-2 small">
                        <i class="fa fa-check-circle me-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <a href="index.php" class="btn-continue mt-3"><i class="fa fa-arrow-left me-1"></i> Tiếp tục mua sắm</a>
            </div>

            <div class="col-md-6">
                <div class="summary-box">
                    <div class="row-total">
                        <span>Tạm tính:</span>
                        <strong id="cart-total-money"><?= number_format($totalMoney, 0, ',', '.') ?>₫</strong>
                    </div>

                    <div id="coupon-row" class="row-total text-success" style="<?= isset($_SESSION['coupon']) ? '' : 'display:none;' ?>">
                        <span>
                            Mã giảm <strong id="coupon-code-display"><?= $_SESSION['coupon']['code'] ?? '' ?></strong> 
                            <a href="index.php?controller=cart&action=removeCoupon" class="text-danger ms-2" title="Gỡ mã" style="font-size: 12px;"><i class="fa fa-times"></i></a>
                        </span>
                        <span id="cart-discount">-<?= number_format($discountAmount, 0, ',', '.') ?>₫</span>
                    </div>

                    <div id="coupon-error-msg" class="text-danger small text-end mt-1"></div>

                    <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>

                    <div class="row-total align-items-center">
                        <span style="font-size: 16px; font-weight: 700;">TỔNG CỘNG:</span>
                        <span class="final-price" id="cart-final-total"><?= number_format($finalTotal, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="text-end small text-muted mb-3">(Đã bao gồm VAT nếu có)</div>

                    <a href="index.php?controller=checkout" class="btn-confirm">TIẾN HÀNH THANH TOÁN <i class="fa fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="couponModalLabel">Chọn mã khuyến mãi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="background: #f5f5f5; max-height: 500px; overflow-y: auto;">
        
        <?php 
            $listCoupons = $listCoupons ?? []; // Tránh lỗi undefined
        ?>

        <?php if (empty($listCoupons)): ?>
            <div class="text-center py-5">
                <i class="fa fa-ticket-alt text-muted" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p class="text-muted">Hiện không có mã giảm giá nào khả dụng.</p>
            </div>
        <?php else: ?>
            <?php foreach ($listCoupons as $coupon): ?>
                <?php 
                    $isEligible = true;
                    $conditionMsg = "";
                    
                    // 1. CHECK SỐ TIỀN TỐI THIỂU
                    if ($totalMoney < $coupon['min_order_amount']) {
                        $isEligible = false;
                        $missing = number_format($coupon['min_order_amount'] - $totalMoney, 0, ',', '.');
                        $conditionMsg = "Mua thêm $missing ₫ để sử dụng";
                    } 
                    // 2. CHECK GIỚI HẠN SỬ DỤNG CỦA USER (Nếu limit > 0)
                    elseif ($coupon['usage_limit_per_user'] > 0 && $coupon['user_used_count'] >= $coupon['usage_limit_per_user']) {
                        $isEligible = false;
                        $conditionMsg = "Bạn đã hết lượt sử dụng mã này";
                    }
                    else {
                        // Thỏa mãn hết
                        $minFmt = number_format($coupon['min_order_amount'], 0, ',', '.');
                        $conditionMsg = "Đơn tối thiểu $minFmt ₫";
                    }

                    // Format Text hiển thị giá trị
                    $valueText = "";
                    if($coupon['type'] == 'percent') {
                        $valueText = "Giảm " . $coupon['value'] . "%";
                        if($coupon['max_discount_amount'] > 0) {
                            $valueText .= " (Tối đa " . number_format($coupon['max_discount_amount']/1000) . "k)";
                        }
                    } else {
                        $valueText = "Giảm " . number_format($coupon['value'], 0, ',', '.') . "₫";
                    }
                ?>

                <div class="coupon-item <?= !$isEligible ? 'coupon-disabled' : '' ?>" 
                     onclick="<?= $isEligible ? "selectCoupon('{$coupon['code']}')" : "" ?>">
                    
                    <div style="background: <?= $isEligible ? '#cd1818' : '#999' ?>; color: white; width: 80px; height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 4px; margin-right: 15px;">
                        <span style="font-weight: bold; font-size: 16px;">SALE</span>
                    </div>

                    <div style="flex: 1;">
                        <div class="coupon-code-text" style="color: <?= $isEligible ? '#cd1818' : '#666' ?>">
                            <?= $coupon['code'] ?>
                        </div>
                        <div style="font-weight: 600; color: #333; font-size: 15px;"><?= $valueText ?></div>
                        <div class="coupon-desc"><?= !empty($coupon['description']) ? $coupon['description'] : 'Áp dụng cho toàn bộ sản phẩm' ?></div>
                        
                        <small class="<?= $isEligible ? 'text-success' : 'text-danger fw-bold' ?>">
                            <i class="fa <?= $isEligible ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-1"></i><?= $conditionMsg ?>
                        </small>
                        
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">
                            HSD: <?= date('d/m/Y', strtotime($coupon['end_date'])) ?>
                        </div>
                    </div>

                    <div>
                        <?php if ($isEligible): ?>
                            <button class="btn-select-coupon">Dùng ngay</button>
                        <?php else: ?>
                            <button class="btn-select-coupon disabled" disabled>
                                <?= ($coupon['usage_limit_per_user'] > 0 && $coupon['user_used_count'] >= $coupon['usage_limit_per_user']) ? 'Hết lượt' : 'Chưa đủ ĐK' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Hàm xử lý khi chọn mã từ Modal
function selectCoupon(code) {
    document.getElementById('input-coupon-code').value = code;
    document.getElementById('form-apply-coupon').submit();
}

$(document).ready(function() {
    // Xử lý AJAX khi thay đổi số lượng
    $('.qty-input').on('change keyup', function() {
        var inputEl = $(this);
        var productId = inputEl.data('id');
        var newQty = inputEl.val();

        // Validate
        if (newQty < 1 || newQty == '') {
            newQty = 1;
            // inputEl.val(1); // (Optional) Có thể force set lại số 1 trên UI
        }

        // Gọi AJAX
        $.ajax({
            url: 'index.php?controller=cart&action=updateAjax', 
            method: 'POST',
            data: { id: productId, qty: newQty },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Cập nhật các con số
                    $('#subtotal-' + productId).text(response.item_subtotal);
                    $('#cart-total-money').text(response.total_money);
                    $('#cart-final-total').text(response.final_total);

                    // Xử lý Coupon logic
                    if (response.coupon_valid) {
                        $('#coupon-row').show();
                        $('#cart-discount').text('-' + response.discount_amount);
                        $('#coupon-error-msg').text('');
                        
                        // Nếu cần cập nhật mã trên UI
                        // $('#coupon-code-display').text(response.coupon_code); 
                    } else {
                        // Nếu coupon bị hủy
                        if ($('#coupon-row').is(':visible')) {
                            $('#coupon-row').hide();
                            $('#coupon-error-msg').text(response.coupon_msg);
                        }
                    }
                }
            },
            error: function() {
                console.log('Lỗi kết nối cập nhật giỏ hàng');
            }
        });
    });
});
</script>