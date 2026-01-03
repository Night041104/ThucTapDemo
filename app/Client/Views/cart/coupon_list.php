<?php 
// File: app/Client/Views/cart/coupon_list.php
// Đảm bảo biến $listCoupons và $totalMoney đã được truyền vào từ bên ngoài
$listCoupons = $listCoupons ?? [];
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
            // 2. CHECK GIỚI HẠN USER
            elseif ($coupon['usage_limit_per_user'] > 0 && $coupon['user_used_count'] >= $coupon['usage_limit_per_user']) {
                $isEligible = false;
                $conditionMsg = "Bạn đã hết lượt sử dụng mã này";
            }
            else {
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