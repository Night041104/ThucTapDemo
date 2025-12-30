

<?php if(!empty($products)): ?>
    <div class="products-grid">
        <?php foreach($products as $p): ?>
            <?php 
                // Logic tính % giảm giá
                $discountPercent = 0;
                if (!empty($p['market_price']) && $p['market_price'] > $p['price']) {
                    $discountPercent = round((($p['market_price'] - $p['price']) / $p['market_price']) * 100);
                }
            ?>

            <a href="index.php?module=client&controller=product&action=detail&id=<?= $p['id'] ?>" class="product-card" title="<?= htmlspecialchars($p['name']) ?>">
                
                <?php if($discountPercent > 0): ?>
                    <div class="discount-badge">Giảm <?= $discountPercent ?>%</div>
                <?php endif; ?>

                <div class="prod-img-box">
                    <img src="<?= $p['thumbnail'] ?>" class="prod-img" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                </div>
                
                <div class="prod-info">
                    <div class="prod-brand-tag"><?= htmlspecialchars($p['brand_name'] ?? '') ?></div>
                    
                    <h3 class="prod-name"><?= htmlspecialchars($p['name']) ?></h3>
                    
                    <div class="prod-price-box">
                        <div class="prod-price">
                            <?= number_format($p['price'], 0, ',', '.') ?>₫
                            <?php if($discountPercent > 0): ?>
                                <span class="prod-old-price"><?= number_format($p['market_price'], 0, ',', '.') ?>₫</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" style="opacity:0.5; margin-bottom: 15px;">
        <p style="color:#666; font-size: 16px;">Không tìm thấy sản phẩm nào phù hợp tiêu chí lọc.</p>
    </div>
<?php endif; ?>