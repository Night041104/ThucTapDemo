<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .cart-container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; color: #555; }
        
        .img-thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .qty-input { width: 60px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 4px; }
        
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
        .btn-update { background: #17a2b8; color: white; border: none; cursor: pointer; font-size: 14px; padding: 5px 10px; margin-left: 10px; }
        .btn-delete { color: #dc3545; font-size: 20px; text-decoration: none; font-weight: bold; }
        .btn-continue { background: #6c757d; color: white; }
        .btn-checkout { background: #28a745; color: white; float: right; }
        .btn-checkout:hover { background: #218838; }

        .empty-cart { text-align: center; padding: 50px; color: #777; }
        .total-row { font-size: 18px; font-weight: bold; color: #333; }
        .total-price { color: #cb1c22; font-size: 24px; }
    </style>
</head>
<body>

    <div class="cart-container">
        <h1>🛒 GIỎ HÀNG CỦA BẠN</h1>

        <?php if (empty($products)): ?>
            <div class="empty-cart">
                <p>Giỏ hàng đang trống!</p>
                <a href="index.php" class="btn btn-continue">⬅ Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <form action="index.php?controller=cart&action=update" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th width="100">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th width="50">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($item['thumbnail']) ?>" class="img-thumb" alt="Ảnh SP">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                    <small style="color:#777">Mã: <?= $item['sku'] ?></small>
                                </td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                                <td>
                                    <input type="number" name="qty[<?= $item['id'] ?>]" 
                                           value="<?= $item['buy_qty'] ?>" 
                                           min="1" max="<?= $item['quantity'] ?>" 
                                           class="qty-input">
                                </td>
                                <td style="color:#cb1c22; font-weight:bold;">
                                    <?= number_format($item['subtotal'], 0, ',', '.') ?>₫
                                </td>
                                <td>
                                    <a href="index.php?controller=cart&action=delete&id=<?= $item['id'] ?>" 
                                       class="btn-delete" onclick="return confirm('Xóa sản phẩm này?')">&times;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right; padding-top:30px;">TỔNG THANH TOÁN:</td>
                            <td colspan="2" style="padding-top:30px;">
                                <span class="total-price"><?= number_format($totalMoney, 0, ',', '.') ?>₫</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 30px; overflow: hidden;">
                    <a href="index.php" class="btn btn-continue">⬅ Tiếp tục mua hàng</a>
                    
                    <button type="submit" class="btn" style="background:#17a2b8; color:white; border:none; cursor:pointer;">
                        ↻ Cập nhật giỏ hàng
                    </button>

                    <a href="index.php?controller=checkout&action=index" class="btn btn-checkout">
                        TIẾN HÀNH THANH TOÁN ➡
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>