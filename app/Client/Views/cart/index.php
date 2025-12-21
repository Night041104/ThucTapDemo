<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px; }

        /* Table Style */
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .cart-table th, .cart-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        .cart-table th { background: #f9f9f9; text-align: left; }
        .cart-table td { vertical-align: middle; }
        .product-name { text-align: left; font-weight: bold; color: #333; }
        
        /* Buttons */
        .btn-update { padding: 5px 10px; background: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .btn-delete { color: #dc3545; text-decoration: none; font-size: 20px; font-weight: bold; }
        .btn-continue { display: inline-block; margin-top: 20px; text-decoration: none; color: #666; font-weight: bold; }
        
        .btn-confirm { 
            display: block; width: 100%; text-align: center; 
            background: #cb1c22; color: white; 
            padding: 15px; font-weight: bold; text-decoration: none; 
            border-radius: 4px; text-transform: uppercase; 
            margin-top: 15px;
        }
        .btn-confirm:hover { background: #b0181d; }

        /* Summary Box */
        .summary-box { background: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #eee; }
        .row-total { display: flex; justify-content: space-between; padding: 8px 0; }
        .final-price { font-size: 22px; color: #cb1c22; font-weight: bold; }

        /* Form Coupon */
        .coupon-form { display: flex; gap: 10px; margin-bottom: 10px; }
        .input-coupon { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-apply { background: #333; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .btn-apply:hover { background: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2>🛒 GIỎ HÀNG CỦA BẠN</h2>

    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 50px;">
            <p style="font-size: 18px; color: #666;">Giỏ hàng của bạn đang trống!</p>
            <a href="index.php" class="btn-confirm" style="width: 200px; margin: 0 auto;">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>

        <form action="index.php?controller=cart&action=update" method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th width="40%">Sản phẩm</th>
                        <th width="15%">Đơn giá</th>
                        <th width="15%">Số lượng</th>
                        <th width="20%">Thành tiền</th>
                        <th width="10%">Xóa</th>
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
                                <?= htmlspecialchars($p['name']) ?><br>
                                <small style="color:#888; font-weight: normal;">ID: <?= $p['id'] ?></small>
                            </td>
                            <td><?= number_format($p['price'], 0, ',', '.') ?>₫</td>
                            <td>
                                <input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $qty ?>" min="1" style="width: 50px; text-align: center; padding: 5px;">
                            </td>
                            <td style="font-weight: bold; color: #333;">
                                <?= number_format($subtotal, 0, ',', '.') ?>₫
                            </td>
                            <td>
                                <a href="index.php?controller=cart&action=delete&id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Xóa sản phẩm này?')">&times;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 15px; text-align: right;">
                <button type="submit" class="btn-update">🔄 Cập nhật số lượng</button>
            </div>
        </form>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 30px;">
            
            <div style="flex: 1;">
                <h3 style="font-size: 16px; margin-bottom: 15px;">🎫 Mã giảm giá</h3>
                
                <form action="index.php?controller=cart&action=applyCoupon" method="POST" class="coupon-form">
                    <input type="text" name="code" class="input-coupon" placeholder="Nhập mã (VD: SALE10)" required>
                    <button type="submit" class="btn-apply">Áp dụng</button>
                </form>

                <?php if (isset($_SESSION['error'])): ?>
                    <div style="color: #dc3545; font-size: 14px; background: #ffe6e6; padding: 10px; border-radius: 4px;">
                        ⚠️ <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div style="color: #155724; font-size: 14px; background: #d4edda; padding: 10px; border-radius: 4px;">
                        ✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="flex: 1;" class="summary-box">
                <div class="row-total">
                    <span>Tạm tính:</span>
                    <strong><?= number_format($totalMoney, 0, ',', '.') ?>₫</strong>
                </div>

                <?php if (isset($_SESSION['coupon'])): ?>
                    <div class="row-total" style="color: #28a745;">
                        <span>
                            Mã giảm <strong><?= $_SESSION['coupon']['code'] ?></strong>:
                            <a href="index.php?controller=cart&action=removeCoupon" style="color: #dc3545; font-size: 12px; margin-left: 5px;">[Hủy]</a>
                        </span>
                        <span>-<?= number_format($discountAmount, 0, ',', '.') ?>₫</span>
                    </div>
                <?php endif; ?>

                <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>

                <div class="row-total">
                    <span style="font-size: 18px; font-weight: bold; padding-top: 5px;">TỔNG CỘNG:</span>
                    <span class="final-price"><?= number_format($finalTotal, 0, ',', '.') ?>₫</span>
                </div>

                <a href="index.php?controller=checkout" class="btn-confirm">TIẾN HÀNH THANH TOÁN ➝</a>
            </div>
        </div>
        
        <a href="index.php" class="btn-continue">← Tiếp tục mua sắm</a>

    <?php endif; ?>
</div>

</body>
</html>