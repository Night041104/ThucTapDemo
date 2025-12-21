<style>
    /* LAYOUT 2 CỘT */
    .main-content { display: flex; gap: 20px; margin-top: 20px; padding-bottom: 50px; }
    .sidebar { width: 250px; flex-shrink: 0; }
    .product-list { flex: 1; }

    /* SIDEBAR */
    .filter-box { background: white; border-radius: 4px; padding: 15px; margin-bottom: 15px; border: 1px solid #eee; }
    .filter-title { font-weight: 700; font-size: 14px; margin-bottom: 10px; display: block; color: #333; }
    
    .brand-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; }
    .brand-item { border: 1px solid #eee; border-radius: 4px; padding: 5px; text-align: center; cursor: pointer; transition: 0.2s; font-size: 12px; height: 35px; display: flex; align-items: center; justify-content: center; background: #fff;}
    .brand-item:hover { border-color: #cd1818; }
    .brand-item img { max-width: 100%; max-height: 100%; object-fit: contain; }

    .checkbox-list label { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; font-size: 13px; color: #444; }
    .checkbox-list input { width: 16px; height: 16px; accent-color: #cd1818; }

    /* PRODUCT GRID */
    .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

    .product-card { background: white; border-radius: 4px; padding: 15px; transition: 0.2s; position: relative; border: 1px solid transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .product-card:hover { border-color: #e0e0e0; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transform: translateY(-2px); }
    
    .prod-img { width: 100%; height: 200px; object-fit: contain; margin-bottom: 15px; transition: 0.3s; }
    .product-card:hover .prod-img { margin-top: -5px; margin-bottom: 20px; }
    
    .prod-name { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; line-height: 1.4; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    
    .prod-price { font-size: 16px; font-weight: 700; color: #cd1818; display: flex; align-items: center; gap: 10px; }
    .prod-old-price { font-size: 13px; text-decoration: line-through; color: #999; font-weight: 400; }
    
    .prod-brand-tag { font-size: 11px; color: #666; background: #f0f0f0; padding: 2px 6px; border-radius: 2px; display: inline-block; margin-bottom: 5px; }
    
    .breadcrumb { padding: 15px 0; font-size: 13px; color: #666; }
    .breadcrumb a { color: #333; }
</style>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> / <strong style="color:#333"><?= htmlspecialchars($category['name']) ?></strong>
    </div>

    <div class="main-content">
        <aside class="sidebar">
            <?php if(!empty($filterBrands)): ?>
            <div class="filter-box">
                <span class="filter-title">Hãng sản xuất</span>
                <div class="brand-grid">
                    <?php foreach($filterBrands as $b): ?>
                        <div class="brand-item" title="<?= $b['name'] ?>">
                            <?php if(!empty($b['logo_url'])): ?>
                                <img src="<?= $b['logo_url'] ?>" alt="<?= $b['name'] ?>">
                            <?php else: ?>
                                <?= $b['name'] ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="filter-box">
                <span class="filter-title">Mức giá</span>
                <div class="checkbox-list">
                    <label><input type="checkbox"> Dưới 2 triệu</label>
                    <label><input type="checkbox"> Từ 2 - 4 triệu</label>
                    <label><input type="checkbox"> Từ 4 - 7 triệu</label>
                    <label><input type="checkbox"> Trên 20 triệu</label>
                </div>
            </div>

            <?php if(!empty($filterAttrs)): ?>
                <?php foreach($filterAttrs as $attr): ?>
                    <?php if(!empty($attr['filter_options'])): ?>
                        <div class="filter-box">
                            <span class="filter-title"><?= $attr['name'] ?></span>
                            <div class="checkbox-list">
                                <?php foreach($attr['filter_options'] as $optVal): ?>
                                    <label>
                                        <input type="checkbox"> <?= htmlspecialchars($optVal) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <section class="product-list">
            <div style="margin-bottom: 15px;">
                <h1 style="font-size: 22px; font-weight: 700; color: #333; display: inline-block; margin-right: 10px;"><?= htmlspecialchars($category['name']) ?></h1>
                <span style="color:#666; font-size:14px;">(Tìm thấy <?= count($products) ?> sản phẩm)</span>
            </div>

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
                <div style="background:white; padding:40px; text-align:center; border-radius:4px; border:1px solid #eee;">
                    <p style="color:#666; font-size:16px;">Hiện chưa có sản phẩm nào trong danh mục này.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

</body>
</html>