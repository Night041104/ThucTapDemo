<?php if(!empty($products)): ?>
    <div class="products-grid">
        <?php foreach($products as $p): ?>
            <?php 
                // 1. Tính toán giảm giá
                $discountPercent = 0;
                if (!empty($p['market_price']) && $p['market_price'] > $p['price']) {
                    $discountPercent = round((($p['market_price'] - $p['price']) / $p['market_price']) * 100);
                }

                // 2. Kiểm tra trạng thái
                $isStopped = ($p['status'] == -1); 
                $isOutStock = ($p['status'] == 1 && $p['quantity'] <= 0);
                
                // Link: Luôn luôn trỏ về chi tiết
                $link = "san-pham/" . $p['slug'] . ".html";
            ?>

            <a href="<?= $link ?>" class="product-card" title="<?= htmlspecialchars($p['name']) ?>">
                
                <?php if($discountPercent > 0 && !$isStopped && !$isOutStock): ?>
                    <div class="discount-badge">Giảm <?= $discountPercent ?>%</div>
                <?php endif; ?>

                <div class="prod-img-box">
                    <img src="<?= $p['thumbnail'] ?>" class="prod-img" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                    
                    <?php if($isStopped): ?>
                        <div class="status-label label-stopped">Ngừng kinh doanh</div>
                    <?php elseif($isOutStock): ?>
                        <div class="status-label label-out-stock">Tạm hết hàng</div>
                    <?php endif; ?>
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

    <?php $total = isset($total) ? $total : count($products); ?>
    <input type="hidden" id="meta-total-count" value="<?= $total ?>">

<?php else: ?>
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" style="opacity:0.5; margin-bottom: 15px;">
        <p style="color:#666; font-size: 16px;">Không tìm thấy sản phẩm nào phù hợp tiêu chí lọc.</p>
        <input type="hidden" id="meta-total-count" value="0">
    </div>
<?php endif; ?>