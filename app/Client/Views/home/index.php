<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Demo</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; }
        .product-card img { width: 100%; height: 180px; object-fit: contain; margin-bottom: 10px; }
        .price { color: #d32f2f; font-weight: bold; font-size: 1.1em; }
        .btn-view { display: inline-block; margin-top: 10px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .btn-view:hover { background: #0056b3; }
        .cart-float { position: fixed; bottom: 20px; right: 20px; background: #cb1c22; color: white; padding: 15px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <h1>🛒 DEMO DANH SÁCH SẢN PHẨM</h1>
    <p><i>(Giao diện dùng tạm để test chức năng thêm giỏ hàng)</i></p>

    <?php 
        $count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>
    <a href="index.php?controller=cart&action=index" class="cart-float">
        Xem Giỏ Hàng (<?= $count ?>) ➝
    </a>

    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <?php $img = !empty($p['thumbnail']) ? $p['thumbnail'] : 'https://via.placeholder.com/200'; ?>
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                
                <h3 style="font-size: 16px; min-height: 40px;"><?= htmlspecialchars($p['name']) ?></h3>
                
                <div class="price"><?= number_format($p['price'], 0, ',', '.') ?>₫</div>
                
                <div style="margin: 10px 0; font-size: 13px; color: #666;">
                    Kho: <?= $p['quantity'] ?>
                </div>

                <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="btn-view">
                    Chọn Mua ➜
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>