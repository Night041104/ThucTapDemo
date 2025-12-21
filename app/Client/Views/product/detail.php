<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f9f9f9; display: flex; justify-content: center; }
        .detail-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; gap: 30px; max-width: 800px; width: 100%; }
        .left-col img { width: 300px; height: 300px; object-fit: contain; border: 1px solid #eee; }
        .right-col { flex: 1; }
        .price { color: #d32f2f; font-size: 28px; font-weight: bold; margin: 15px 0; }
        input[type=number] { padding: 10px; width: 60px; text-align: center; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
        .btn-add { background: #d32f2f; color: white; border: none; padding: 12px 25px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; text-transform: uppercase; }
        .btn-add:hover { background: #b71c1c; }
        .btn-add:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="detail-box">
        <div class="left-col">
            <?php $img = !empty($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/300'; ?>
            <img src="<?= $img ?>">
        </div>
        
        <div class="right-col">
            <a href="index.php" style="text-decoration: none; color: #666;">← Quay lại trang chủ</a>
            
            <h1 style="margin-top: 10px;"><?= htmlspecialchars($product['name']) ?></h1>
            <p>Mã SP: <?= $product['sku'] ?></p>
            
            <div class="price"><?= number_format($product['price'], 0, ',', '.') ?>₫</div>
            
            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

            <form action="index.php?controller=cart&action=add" method="POST">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                
                <div style="margin-bottom: 20px;">
                    <strong>Trạng thái: </strong>
                    <?php if ($product['quantity'] > 0): ?>
                        <span style="color: green;">Còn hàng (<?= $product['quantity'] ?>)</span>
                    <?php else: ?>
                        <span style="color: red;">Hết hàng</span>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <label>Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>">
                    
                    <button type="submit" class="btn-add" <?= ($product['quantity'] <= 0) ? 'disabled' : '' ?>>
                        Thêm vào giỏ
                    </button>
                </div>
            </form>

            <?php if ($product['quantity'] <= 0): ?>
                <p style="color: red; margin-top: 10px;">Sản phẩm này tạm thời hết hàng.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>