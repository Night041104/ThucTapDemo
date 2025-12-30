<?php if(!empty($products)): ?>
    <div class="products-grid">
        <?php foreach($products as $p): ?>
            <a href="index.php?module=client&controller=product&action=detail&id=<?= $p['id'] ?>" class="product-card">
                <img src="<?= $p['thumbnail'] ?>" class="prod-img" alt="<?= htmlspecialchars($p['name']) ?>">
                
                <div class="prod-brand-tag"><?= htmlspecialchars($p['brand_name'] ?? '') ?></div>
                
                <h3 class="prod-name"><?= htmlspecialchars($p['name']) ?></h3>
                
                <div class="prod-price">
                    <?= number_format($p['price'], 0, ',', '.') ?>₫
                    <?php if($p['market_price'] > $p['price']): ?>
                        <span class="prod-old-price"><?= number_format($p['market_price'], 0, ',', '.') ?>₫</span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" style="opacity:0.5">
        <p>Không tìm thấy sản phẩm nào phù hợp tiêu chí lọc.</p>
    </div>
<?php endif; ?>