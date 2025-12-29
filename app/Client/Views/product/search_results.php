<div class="container">
     <?php if (!empty($keyword)): ?>
        <h3 class="search-title">Kết quả tìm kiếm cho: "<strong><?= htmlspecialchars($keyword) ?></strong>"</h3>
    <?php endif; ?>
    
    <div class="category-tabs" style="display: flex; gap: 10px; margin: 20px 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
        <a href="index.php?controller=product&action=search&q=<?= urlencode($keyword) ?>&cate_id=0" 
           style="padding: 10px 20px; border: 1px solid #ccc; border-radius: 5px; text-decoration: none; color: #333; <?= $selectedCate == 0 ? 'background: #cb1c22; color: #fff; border-color: #cb1c22;' : '' ?>">
            Tất cả (<?= count($allResults) ?>)
        </a>

        <?php foreach ($categoryTabs as $tab): ?>
            <a href="index.php?controller=product&action=search&q=<?= urlencode($keyword) ?>&cate_id=<?= $tab['id'] ?>" 
               style="padding: 10px 20px; border: 1px solid #ccc; border-radius: 5px; text-decoration: none; color: #333; <?= $selectedCate == $tab['id'] ? 'background: #cb1c22; color: #fff; border-color: #cb1c22;' : '' ?>">
                <?= $tab['name'] ?> (<?= $tab['count'] ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        <?php if (empty($displayProducts)): ?>
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
        <?php else: ?>
            <?php foreach ($displayProducts as $p): ?>
                <div class="product-card" style="border: 1px solid #eee; padding: 10px; text-align: center;">
                    <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>">
                        <img src="<?= $p['thumbnail'] ?>" style="max-width: 100%; height: 180px; object-fit: contain;">
                        <h4 style="font-size: 14px; margin: 10px 0; color: #333;"><?= $p['name'] ?></h4>
                        <p style="color: #cb1c22; font-weight: bold;"><?= number_format($p['price']) ?>đ</p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>