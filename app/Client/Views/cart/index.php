<style>
    /* CSS RIÊNG CHO TRANG GIỎ HÀNG */
    /* Xóa style body cũ để ăn theo style của Header */
    
    .cart-container { 
        max-width: 1000px; 
        margin: 30px auto 50px auto; /* Căn giữa và cách header/footer */
        background: white; 
        padding: 30px; 
        border-radius: 8px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    }
    
    h2.cart-title { 
        margin-top: 0; 
        border-bottom: 2px solid #eee; 
        padding-bottom: 15px; 
        font-size: 24px;
        color: #333;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Table Style */
    .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
    .cart-table th { background: #f8f9fa; text-align: left; font-weight: 600; color: #555; text-transform: uppercase; font-size: 13px; }
    .cart-table td { vertical-align: middle; }
    .product-name { text-align: left; font-weight: 600; color: #333; font-size: 15px; }
    
    /* Buttons */
    .btn-update { padding: 8px 15px; background: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; transition: 0.2s; }
    .btn-update:hover { background: #138496; }
    
    .btn-delete { color: #999; text-decoration: none; font-size: 24px; transition: 0.2s; }
    .btn-delete:hover { color: #dc3545; }
    
    .btn-continue { display: inline-block; margin-top: 20px; text-decoration: none; color: #555; font-weight: 600; }
    .btn-continue:hover { color: #cd1818; }
    
    .btn-confirm { 
        display: block; width: 100%; text-align: center; 
        background: #cd1818; color: white; /* Màu đỏ FPT */
        padding: 15px; font-weight: bold; text-decoration: none; 
        border-radius: 4px; text-transform: uppercase; 
        margin-top: 15px;
        transition: 0.2s;
    }
    .btn-confirm:hover { background: #a50e0e; }

    /* Summary Box */
    .summary-box { background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #eee; }
    .row-total { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
    .final-price { font-size: 24px; color: #cd1818; font-weight: 800; }

    /* Form Coupon */
    .coupon-form { display: flex; gap: 10px; margin-bottom: 10px; }
    .input-coupon { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; outline: none; }
    .input-coupon:focus { border-color: #cd1818; }
    .btn-apply { background: #333; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; }
    .btn-apply:hover { background: #000; }
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

        <form action="index.php?controller=cart&action=update" method="POST">
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
                                <input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $qty ?>" min="1" style="width: 60px; text-align: center; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                            </td>
                            <td style="font-weight: 700; color: #cd1818;">
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
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn-update"><i class="fa fa-sync-alt me-1"></i> Cập nhật giỏ hàng</button>
            </div>
        </form>

        <div class="row mt-5">
            <div class="col-md-6">
                <h3 style="font-size: 16px; margin-bottom: 15px; font-weight: 700; text-transform: uppercase;">🎫 Mã giảm giá / Quà tặng</h3>
                
                <form action="index.php?controller=cart&action=applyCoupon" method="POST" class="coupon-form">
                    <input type="text" name="code" class="input-coupon" placeholder="Nhập mã giảm giá (VD: SALE10)" required>
                    <button type="submit" class="btn-apply">Áp dụng</button>
                </form>

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
                        <strong><?= number_format($totalMoney, 0, ',', '.') ?>₫</strong>
                    </div>

                    <?php if (isset($_SESSION['coupon'])): ?>
                        <div class="row-total text-success">
                            <span>
                                Mã giảm <strong><?= $_SESSION['coupon']['code'] ?></strong> 
                                <a href="index.php?controller=cart&action=removeCoupon" class="text-danger ms-2" title="Gỡ mã" style="font-size: 12px;"><i class="fa fa-times"></i></a>
                            </span>
                            <span>-<?= number_format($discountAmount, 0, ',', '.') ?>₫</span>
                        </div>
                    <?php endif; ?>

                    <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>

                    <div class="row-total align-items-center">
                        <span style="font-size: 16px; font-weight: 700;">TỔNG CỘNG:</span>
                        <span class="final-price"><?= number_format($finalTotal, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="text-end small text-muted mb-3">(Đã bao gồm VAT nếu có)</div>

                    <a href="index.php?controller=checkout" class="btn-confirm">TIẾN HÀNH THANH TOÁN <i class="fa fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>