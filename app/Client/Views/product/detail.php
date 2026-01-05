<style>
    /* =========================================
       CORE VARIABLES & RESET
       ========================================= */
    :root {
        --primary-red: #cb1c22;
        --dark-red: #a61419;
        --bg-gray: #f8f9fa;
        --text-main: #333;
        --text-gray: #666;
        --border-color: #e5e7eb;
    }

    .detail-wrapper {
        background-color: #fff;
        font-family: 'Roboto', 'Segoe UI', sans-serif;
        color: var(--text-main);
    }

    .container-detail {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    /* BREADCRUMB */
    .breadcrumb-custom {
        padding: 10px 0;
        margin-bottom: 20px;
        font-size: 14px;
        color: var(--text-gray);
        background: transparent;
    }
    .breadcrumb-custom a { color: #007bff; text-decoration: none; font-weight: 500; }
    .breadcrumb-custom a:hover { text-decoration: underline; }

    /* =========================================
       LAYOUT GRID
       ========================================= */
    .product-layout { display: flex; gap: 40px; margin-bottom: 40px; align-items: flex-start; }
    .gallery-box { width: 42%; position: relative; }
    .info-box { width: 58%; }

    /* =========================================
       1. GALLERY CAROUSEL (FPT STYLE)
       ========================================= */
    .main-img-stage {
        border-radius: 8px;
        border: 1px solid #f0f0f0;
        padding: 20px;
        text-align: center;
        background: #fff;
        margin-bottom: 15px;
        position: relative;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .main-img-stage img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .main-img-stage:hover img { transform: scale(1.05); }

    /* Nút điều hướng đè lên ảnh */
    .img-nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 40px; height: 40px; background: rgba(0, 0, 0, 0.15);
        color: #fff; border: none; border-radius: 50%; font-size: 18px;
        cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; opacity: 0;
    }
    .main-img-stage:hover .img-nav-btn { opacity: 1; }
    .img-nav-btn:hover { background: rgba(203, 28, 34, 0.8); box-shadow: 0 0 10px rgba(0,0,0,0.2); }
    .nav-prev { left: 10px; }
    .nav-next { right: 10px; }

    /* Thumbnails Scroll */
    .thumb-list-container {
        overflow-x: auto; width: 100%; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth; margin-top: 10px;
    }
    .thumb-list-container::-webkit-scrollbar { display: none; }
    .thumb-list { display: flex; gap: 10px; width: max-content; padding: 2px; }
    
    .thumb-item {
        width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 4px; padding: 2px;
        cursor: pointer; object-fit: contain; opacity: 0.6; transition: all 0.2s; background: #fff;
    }
    .thumb-item:hover, .thumb-item.active { border-color: var(--primary-red); opacity: 1; box-shadow: 0 0 0 1px var(--primary-red); }

    /* Mini Specs */
    .mini-specs-box { margin-top: 25px; background: #f8f9fa; border-radius: 8px; padding: 15px; border: 1px solid #eee; }
    .mini-specs-list li { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #eee; }
    .btn-show-full-specs { width: 100%; margin-top: 10px; padding: 8px; background: #fff; border: 1px solid var(--primary-red); color: var(--primary-red); border-radius: 4px; cursor: pointer; }
    .btn-show-full-specs:hover { background: var(--primary-red); color: white; }

    /* =========================================
       2. PRODUCT INFO
       ========================================= */
    .prod-title { font-size: 26px; font-weight: 700; color: #222; margin-bottom: 8px; }
    .prod-meta { font-size: 13px; color: #666; display: flex; gap: 15px; margin-bottom: 15px; }
    
    .price-area { display: flex; align-items: baseline; gap: 15px; margin-bottom: 20px; }
    .price-current { font-size: 32px; font-weight: bold; color: var(--primary-red); }
    .price-old { font-size: 16px; color: #999; text-decoration: line-through; }

    /* VARIANTS Buttons */
    .variant-group { margin-bottom: 15px; }
    .variant-label { font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block; }
    .variant-list { display: flex; flex-wrap: wrap; gap: 10px; }
    .variant-btn {
        min-width: 80px; padding: 6px 15px; border: 1px solid #d1d5db; border-radius: 6px;
        background: #fff; color: #444; font-size: 13px; text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        position: relative; transition: all 0.2s;
    }
    .variant-thumb { width: 25px; height: 25px; object-fit: contain; border-radius: 2px; }
    .variant-btn:hover { border-color: var(--primary-red); color: var(--primary-red); }
    .variant-btn.active {
        border-color: var(--primary-red); background: #fffafa; color: var(--primary-red); font-weight: 600; box-shadow: 0 0 0 1px inset var(--primary-red);
    }
    .variant-btn.active::after {
        content: ''; position: absolute; top: 0; right: 0; border-style: solid; border-width: 0 16px 16px 0;
        border-color: transparent var(--primary-red) transparent transparent; border-top-right-radius: 5px;
    }
    .variant-btn.active::before {
        content: '✓'; position: absolute; top: -2px; right: 1px; font-size: 9px; color: #fff; z-index: 2; font-weight: bold;
    }

    /* Promo & Actions */
    .promo-container { border: 1px solid #fee2e2; border-radius: 8px; margin-bottom: 25px; overflow: hidden; }
    .promo-header { background: #fee2e2; padding: 10px 15px; color: #991b1b; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; }
    .promo-content { padding: 15px; background: #fff; }
    .promo-item { display: flex; gap: 10px; margin-bottom: 10px; font-size: 13px; color: #333; }
    .promo-icon { color: #fff; background: #28a745; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0; margin-top: 2px; }

    .action-group { display: flex; gap: 15px; margin-top: 20px; }
    .btn-buy-now { flex: 1; background: linear-gradient(180deg, #f52f32 0%, #cb1c22 100%); color: white; border: none; border-radius: 8px; padding: 12px; cursor: pointer; text-align: center; box-shadow: 0 4px 6px rgba(203, 28, 34, 0.3); }
    .btn-buy-now:hover { background: linear-gradient(180deg, #cb1c22 0%, #a61419 100%); transform: translateY(-2px); }
    .btn-cart { width: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px solid var(--primary-red); background: white; color: var(--primary-red); border-radius: 8px; cursor: pointer; }
    .btn-cart:hover { background: #fff1f1; }

    /* Modal & Review Box */
    .specs-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; opacity: 0; visibility: hidden; transition: 0.3s; backdrop-filter: blur(2px); }
    .specs-overlay.active { opacity: 1; visibility: visible; }
    .specs-panel { position: absolute; top: 0; right: 0; bottom: 0; width: 550px; max-width: 90%; background: #fff; transform: translateX(100%); transition: transform 0.4s; display: flex; flex-direction: column; }
    .specs-overlay.active .specs-panel { transform: translateX(0); }
    .sp-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .sp-body { flex: 1; overflow-y: auto; padding: 20px; }

    .review-box { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 30px; }
    
    /* Filter Review Styles */
    .filter-review-container { display: flex; align-items: center; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
    .btn-filter-rating { display: inline-block; padding: 6px 20px; border-radius: 25px; border: 1px solid #ddd; background: #fff; color: #333; text-decoration: none; font-size: 14px; transition: all 0.3s; }
    .btn-filter-rating:hover { border-color: var(--primary-red); color: var(--primary-red); }
    .btn-filter-rating.active { background-color: #fff; border-color: var(--primary-red); color: var(--primary-red); font-weight: bold; box-shadow: inset 0 0 0 1px var(--primary-red); }


    @media (max-width: 992px) { .product-layout { flex-direction: column; } .gallery-box, .info-box { width: 100%; } }
    .btn-disabled-state {
        width: 100%;
        padding: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        color: white;
        border-radius: 8px;
        cursor: not-allowed;
        box-shadow: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .state-out-stock {
        background-color: #6c757d; /* Màu xám */
        border: 1px solid #5a6268;
    }

    .state-stopped {
        background-color: #212529; /* Màu đen */
        border: 1px solid #000;
    }
</style>

<div class="detail-wrapper">
    <div class="container-detail">
        <div class="breadcrumb-custom">
            <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a> / 
            <a href="danh-muc/<?= $category['slug'] ?>"><?= $category['name'] ?? 'Danh mục' ?></a> / 
            <span><?= $product['name'] ?></span>
        </div>

        <div class="product-layout">
            <div class="gallery-box">
                <div class="main-img-stage">
                    <button class="img-nav-btn nav-prev" onclick="changeImageByStep(-1)"><i class="fa fa-chevron-left"></i></button>
                    <button class="img-nav-btn nav-next" onclick="changeImageByStep(1)"><i class="fa fa-chevron-right"></i></button>
                    <img id="main-img" src="<?= $product['thumbnail'] ?>" alt="<?= $product['name'] ?>">
                    <?php if($product['market_price'] > $product['price']): ?>
                        <?php $percent = round((($product['market_price'] - $product['price']) / $product['market_price']) * 100); ?>
                        <div style="position: absolute; top: 15px; left: 15px; background: #cb1c22; color: #fff; padding: 5px 10px; border-radius: 4px; font-weight: 700; font-size: 13px;">Giảm <?= $percent ?>%</div>
                    <?php endif; ?>
                </div>
                
                <div class="thumb-list-container" id="thumb-container">
                    <div class="thumb-list" id="thumb-track">
                        <img src="<?= $product['thumbnail'] ?>" class="thumb-item active" onclick="changeImage(this)">
                        <?php if(!empty($gallery)): foreach($gallery as $img): ?>
                            <img src="<?= $img['image_url'] ?>" class="thumb-item" onclick="changeImage(this)">
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <?php if(!empty($specs)): ?>
                <div class="mini-specs-box">
                    <div style="font-weight: 700; margin-bottom: 10px; text-transform: uppercase;"><i class="fa fa-cogs"></i> Thông số tóm tắt</div>
                    <ul class="mini-specs-list">
                        <?php $count = 0; foreach($specs as $group) { if(isset($group['items'])) { foreach($group['items'] as $item) { if($count++ >= 5) break 2; echo "<li><span>{$item['name']}</span> <span>{$item['value']}</span></li>"; }}} ?>
                    </ul>
                    <button class="btn-show-full-specs" onclick="openSpecs()">Xem cấu hình chi tiết</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <h1 class="prod-title"><?= $product['name'] ?></h1>
                <div class="prod-meta">
                    <span>SKU: <?= $product['sku'] ?></span>
                    <span style="color: #f59e0b; font-weight: 600;"><?= $reviewStats['avg'] ?? 0 ?> <i class="fa fa-star"></i></span>
                </div>
                <div class="price-area">
                    <div class="price-current"><?= number_format($product['price']) ?>₫</div>
                    <?php if($product['market_price'] > $product['price']): ?>
                        <div class="price-old"><?= number_format($product['market_price']) ?>₫</div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($variantGroups)): ?>
                    <div style="margin-bottom: 25px;">
                        <?php foreach($variantGroups as $groupName => $options): 
                            $isColorGroup = (mb_stripos($groupName, 'màu') !== false || mb_stripos($groupName, 'color') !== false);
                        ?>
                            <div class="variant-group">
                                <span class="variant-label"><?= htmlspecialchars($groupName) ?></span>
                                <div class="variant-list">
                                    <?php foreach($options as $valText => $info): ?>
                                        <a href="san-pham/<?= $info['slug'] ?>.html" class="variant-btn <?= $info['active'] ? 'active' : '' ?>">
                                            <?php if ($isColorGroup): 
                                                // Ưu tiên lấy ảnh variant nếu có, không thì lấy ảnh chính
                                                $variantImg = isset($info['image']) && !empty($info['image']) ? $info['image'] : $product['thumbnail'];
                                            ?>
                                                 <img src="<?= $variantImg ?>" class="variant-thumb">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($valText) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="promo-container">
                    <div class="promo-header"><i class="fa fa-gift"></i> ƯU ĐÃI ĐẶC QUYỀN</div>
                    <div class="promo-content">
                        <div class="promo-item"><div class="promo-icon"><i class="fa fa-check"></i></div><span>Giảm ngay <strong>500.000đ</strong> qua VNPAY/ZaloPay.</span></div>
                        <div class="promo-item"><div class="promo-icon"><i class="fa fa-check"></i></div><span>Tặng Combo phụ kiện trị giá <strong>450.000đ</strong>.</span></div>
                        <div class="promo-item"><div class="promo-icon"><i class="fa fa-shield"></i></div><span>Bảo hành chính hãng 12 tháng - Lỗi 1 đổi 1.</span></div>
                    </div>
                </div>

                <?php 
                    // Status -1: Ngừng kinh doanh
                    $isStopped = (isset($product['status']) && $product['status'] == -1); 
                    // Status 1 nhưng quantity = 0: Hết hàng
                    $isOutStock = (isset($product['status']) && $product['status'] == 1 && $product['quantity'] <= 0); 
                ?>

                <?php if ($isStopped): ?>
                    <div class="mt-4">
                        <div class="btn-disabled-state state-stopped">
                            <i class="fa fa-ban"></i> SẢN PHẨM NGỪNG KINH DOANH
                        </div>
                        <p class="text-center mt-2 text-muted small">Sản phẩm này hiện đã ngừng bán tại hệ thống FPT Shop Clone.</p>
                    </div>

                <?php elseif ($isOutStock): ?>
                    <div class="mt-4">
                        <div class="btn-disabled-state state-out-stock">
                            <i class="fa fa-box-open"></i> TẠM HẾT HÀNG
                        </div>
                        <p class="text-center mt-2 text-muted small">Sản phẩm đang tạm hết. Vui lòng quay lại sau.</p>
                    </div>

                <?php else: ?>
                    <form method="POST" action="them-gio-hang">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1"> 
                        
                        <div class="action-group">
                            <button type="submit" name="buy_now" value="1" class="btn-buy-now">
                                <span style="display:block; font-size:18px; font-weight:800;">MUA NGAY</span>
                                <span style="display:block; font-size:12px; font-weight:normal;">(Giao tận nơi hoặc nhận tại cửa hàng)</span>
                            </button>

                            <button type="button" class="btn-cart btn-add-cart">
                                <i class="fa fa-cart-plus" style="font-size:20px;"></i>
                                <span style="font-size:9px; font-weight:700;">THÊM GIỎ</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="review-box">
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-left: 5px solid var(--primary-red); padding-left: 10px;">
                Đánh giá & Nhận xét <?= $product['name'] ?>
            </h3>
            
            <div style="display: flex; gap: 40px; margin-bottom: 40px; flex-wrap: wrap;">
                <div style="text-align: center; width: 150px;">
                    <div style="font-size: 48px; font-weight: bold; color: var(--primary-red); line-height: 1;"><?= $reviewStats['avg'] ?>/5</div>
                    <div style="color: #f59e0b; margin: 5px 0;">
                        <?php $avg = $reviewStats['avg'] ?? 0; for($i=1; $i<=5; $i++) echo ($i <= $avg) ? '★' : '☆'; ?>
                    </div>
                    <div style="font-size: 13px; color: #666;"><?= $reviewStats['total'] ?> đánh giá</div>
                </div>
                
                <div style="flex: 1; min-width: 300px;">
                     <?php for($i=5; $i>=1; $i--): $percent = ($reviewStats['total'] > 0) ? ($reviewStats[$i] / $reviewStats['total']) * 100 : 0; ?>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px; font-size: 13px;">
                        <span style="width: 30px; font-weight: bold;"><?= $i ?> ★</span>
                        <div style="flex: 1; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                            <div style="width: <?= $percent ?>%; background: var(--primary-red); height: 100%;"></div>
                        </div>
                        <span style="width: 30px; text-align: right; color: #999;"><?= $reviewStats[$i] ?></span>
                    </div>
                    <?php endfor; ?>
                </div>

                <div style="display: flex; align-items: center;">
                    <?php if(isset($_SESSION['user'])): ?>
                        <?php if(!$userReview): ?>
                            <button onclick="openReviewForm('add')" style="background: var(--primary-red); color: white; border: none; padding: 10px 30px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                                <i class="fa fa-pencil-alt"></i> Viết đánh giá
                            </button>
                        <?php else: ?>
                            <div style="text-align: center;">
                                <div style="margin-bottom: 8px; color: #28a745; font-size: 13px; font-weight: bold;">
                                    <i class="fa fa-check-circle"></i> Bạn đã đánh giá sản phẩm này
                                </div>
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <button onclick="openReviewForm('edit', <?= htmlspecialchars(json_encode($userReview)) ?>)" 
                                            style="background: white; border: 1px solid #28a745; color: var(--primary-red); padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.2s;">
                                        <i class="fa fa-edit"></i> Sửa
                                    </button>

                                    <a href="index.php?module=client&controller=review&action=delete&id=<?= $userReview['id'] ?>" 
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?')"
                                    style="background: white; border: 1px solid #dc3545; color: #dc3545; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: 0.2s;">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="dang-nhap" style="background: var(--primary-red); color: white; text-decoration: none; padding: 10px 30px; border-radius: 5px; font-weight: bold;">Đăng nhập để đánh giá</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="filter-review-container">
                <a href="javascript:void(0)" onclick="filterReviews(this, 0)" class="btn-filter-rating active">Tất cả</a>
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <a href="javascript:void(0)" onclick="filterReviews(this, <?= $i ?>)" class="btn-filter-rating"><?= $i ?> Sao</a>
                <?php endfor; ?>
            </div>

            <div id="formReview" style="display:none; margin-bottom: 30px; background: #fffcfc; padding: 20px; border: 1px solid #fcebeb; border-radius: 8px;">
                <?php if(isset($_SESSION['user'])): ?>
                <form id="review-form" action="index.php?module=client&controller=review&action=submit" method="POST">
                    <input type="hidden" name="review_id" id="input_review_id" value="">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; color: #333;">Đánh giá của bạn:</label>
                        <select name="rating" id="input_rating" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ddd; margin-left: 10px;">
                            <option value="5">5 Sao (Tuyệt vời)</option>
                            <option value="4">4 Sao (Tốt)</option>
                            <option value="3">3 Sao (Bình thường)</option>
                            <option value="2">2 Sao (Tệ)</option>
                            <option value="1">1 Sao (Rất tệ)</option>
                        </select>
                    </div>
                    <textarea name="comment" id="input_comment" required style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" placeholder="Mời bạn chia sẻ cảm nhận..."></textarea>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" id="btn-submit-review" style="background: var(--primary-red); color: white; border: none; padding: 8px 25px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            Gửi đánh giá
                        </button>
                        <button type="button" onclick="$('#formReview').slideUp()" style="background: #eee; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer;">Hủy</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <div class="review-list">
                <?php if(empty($reviews)): ?>
                    <p style="text-align:center; color:#999;" id="no-review-text">Chưa có đánh giá nào.</p>
                <?php endif; ?>
                
                <div id="review-list-container">
                    <?php foreach($reviews as $rev): ?>
                        <div style="border-bottom: 1px solid #eee; padding: 20px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <strong style="font-size: 15px;"><?= htmlspecialchars($rev['fname'] . ' ' . $rev['lname']) ?></strong>
                                    <span style="color: #f59e0b; margin-left: 10px; font-size: 13px;"><?= str_repeat('★', $rev['rating']) ?></span>
                                </div>
                                <small style="color: #999;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                            </div>
                            <p style="margin-top: 8px; color: #444; line-height: 1.5;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            
                            <?php 
                                $isLoggedIn = isset($_SESSION['user']);
                                $currentUserId = $isLoggedIn ? $_SESSION['user']['id'] : null;
                                $isAdmin = ($isLoggedIn && $_SESSION['user']['role_id'] == 1);
                                
                                // Kiểm tra xem bài review này có phải của người đang xem hay không
                                $isOwner = ($isLoggedIn && ($currentUserId == $rev['user_id']));
                            ?>

                            <div style="display: flex; gap: 15px; font-size: 12px; margin-top: 8px; align-items: center;">
                                <?php if($isAdmin): ?>
                                    <a href="javascript:void(0)" onclick="$('#reply-form-<?= $rev['id'] ?>').toggle()" style="color: #007bff; text-decoration: none; font-weight: bold;">
                                        <i class="fa fa-reply"></i> Trả lời
                                    </a>

                                    <?php if($isOwner): ?>
                                        <a href="javascript:void(0)" onclick="openReviewForm('edit', <?= htmlspecialchars(json_encode($rev)) ?>)" style="color: #28a745; text-decoration: none; font-weight: bold;">
                                            <i class="fa fa-edit"></i> Sửa
                                        </a>
                                    <?php endif; ?>

                                    <a href="index.php?module=admin&controller=review&action=delete&id=<?= $rev['id'] ?>" 
                                    onclick="return confirm('Xóa bài đánh giá này?')" style="color: #cd1818; text-decoration: none; font-weight: bold;">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>

                                <?php elseif($isOwner): ?>
                                    <a href="javascript:void(0)" onclick="openReviewForm('edit', <?= htmlspecialchars(json_encode($rev)) ?>)" style="color: #28a745; text-decoration: none;">
                                        <i class="fa fa-edit"></i> Sửa
                                    </a>
                                    <a href="index.php?module=client&controller=review&action=delete&id=<?= $rev['id'] ?>" 
                                    onclick="return confirm('Xóa đánh giá của bạn?')" style="color: #cd1818; text-decoration: none; margin-left: 10px;">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($rev['replies'])): foreach ($rev['replies'] as $reply): ?>
                                <div style="margin-left: 30px; background: #f9f9f9; padding: 10px; border-left: 3px solid #cd1818; margin-top: 10px; border-radius: 4px;">
                                    <div style="font-weight:bold; color:#cd1818; font-size:13px; margin-bottom:4px;"><i class="fa fa-user-shield"></i> FPT Shop Clone System đã trả lời:</div>
                                    <div style="font-size:13px; color:#333;"><?= nl2br(htmlspecialchars($reply['reply_content'])) ?></div>
                                </div>
                            <?php endforeach; endif; ?>

                            <?php if($isAdmin): ?>
                                <div id="reply-form-<?= $rev['id'] ?>" style="display:none; margin-top: 10px; margin-left: 30px;">
                                    <form action="index.php?module=admin&controller=review&action=reply" method="POST">
                                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                        <textarea name="reply_text" style="width:100%; height:60px; padding:8px; border:1px solid #ddd; border-radius:4px;" placeholder="Nội dung trả lời với tư cách Admin..."></textarea>
                                        <button type="submit" style="margin-top:5px; padding:5px 15px; background: #333; color: #fff; border: none; border-radius: 4px; cursor:pointer;">Gửi phản hồi</button>
                                    </form>
                                </div>
                            <?php endif; ?>  
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="specs-modal" class="specs-overlay" onclick="closeSpecs()">
    <div class="specs-panel" onclick="event.stopPropagation()">
        <div class="sp-header"><h3>Thông số kỹ thuật</h3><button onclick="closeSpecs()" style="border:none;background:none;font-size:24px;cursor:pointer;">&times;</button></div>
        <div class="sp-body"><?php foreach($specs as $group): ?><div class="sp-group"><h4 style="background:#f5f5f5;padding:5px;"><?= $group['group_name'] ?></h4><?php if(isset($group['items'])): foreach($group['items'] as $item): ?><div style="display:flex;padding:5px 0;border-bottom:1px solid #eee;"><span style="width:140px;color:#666;"><?= $item['name'] ?></span><span><?= $item['value'] ?></span></div><?php endforeach; endif; ?></div><?php endforeach; ?></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    
    // --- JS CAROUSEL ---
    function changeImage(el) {
        document.getElementById('main-img').src = el.src;
        document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
    function changeImageByStep(step) {
        const thumbs = document.querySelectorAll('.thumb-item');
        let activeIndex = -1;
        thumbs.forEach((img, index) => { if (img.classList.contains('active')) activeIndex = index; });
        let newIndex = activeIndex + step;
        if (newIndex < 0) newIndex = thumbs.length - 1;
        if (newIndex >= thumbs.length) newIndex = 0;
        if (thumbs[newIndex]) thumbs[newIndex].click();
    }
    
    // --- JS MODAL ---
    function openSpecs() { document.getElementById('specs-modal').classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSpecs() { document.getElementById('specs-modal').classList.remove('active'); document.body.style.overflow = ''; }

    // --- JS REVIEW FUNCTIONS ---
    // Tìm đoạn script ở cuối file detail.php

    function openReviewForm(mode, data = null) {
        $('#formReview').slideDown(); // Hiện khung form ra
        
        const form = $('#review-form');
        const btn = $('#btn-submit-review');
        
        // Reset form cho sạch sẽ trước khi điền
        form[0].reset(); 

        if (mode === 'edit' && data) {
            // === CHẾ ĐỘ SỬA ===
            // 1. Quan trọng nhất: Đổi đường dẫn action sang hàm edit
            form.attr('action', 'index.php?module=client&controller=review&action=edit');
            
            // 2. Điền dữ liệu cũ vào các ô input
            $('#input_review_id').val(data.id);       // ID bài review (để biết sửa bài nào)
            $('#input_rating').val(data.rating);      // Điền số sao cũ
            $('#input_comment').val(data.comment);    // Điền nội dung cũ
            
            // 3. Đổi màu nút bấm sang màu xanh cho dễ nhận biết
            btn.html('<i class="fa fa-save"></i> Cập nhật').css('background', '#28a745');
            
            // Cuộn màn hình xuống chỗ form
            $('html, body').animate({
                scrollTop: $("#formReview").offset().top - 150
            }, 500);

        } else {
            // === CHẾ ĐỘ THÊM MỚI ===
            // 1. Đổi đường dẫn action về hàm submit (thêm mới)
            form.attr('action', 'index.php?module=client&controller=review&action=submit');
            
            // 2. Xóa trắng các ID ẩn
            $('#input_review_id').val('');
            $('#input_rating').val('5'); // Mặc định 5 sao
            $('#input_comment').val('');
            
            // 3. Đổi nút bấm về màu đỏ mặc định
            btn.html('Gửi đánh giá').css('background', '#cd1818');
        }
    }

    function filterReviews(element, rating) {
        $('.btn-filter-rating').removeClass('active');
        $(element).addClass('active');
        var container = $('#review-list-container');
        container.css('opacity', '0.5');

        $.ajax({
            url: 'index.php?module=client&controller=product&action=detail',
            type: 'GET',
            data: { id: '<?= $product['id'] ?>', rating: rating, is_ajax: 1 },
            success: function(response) {
                if(response.trim() == "") container.html('<p style="text-align:center; color:#999; padding: 20px;">Chưa có đánh giá nào cho mức sao này.</p>');
                else container.html(response);
                container.css('opacity', '1');
                $('#no-review-text').hide();
            }
        });
    }

    $(document).ready(function() {
        // [1] CART AJAX - DÙNG URL ROUTER MỚI "them-gio-hang"
       // [1] CART AJAX - DÙNG URL ROUTER MỚI "them-gio-hang"
    $('.btn-add-cart').click(function(e) {
        e.preventDefault(); 
        var form = $(this).closest('form');
        var productId = form.find('input[name="product_id"]').val();
        
        // Hiệu ứng loading
        var btn = $(this);
        var originalHtml = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: 'them-gio-hang', 
            type: 'POST',
            dataType: 'json',
            data: { product_id: productId, quantity: 1, is_ajax: 1, add_to_cart: 1 },
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);
                
                if (response.status === 'success') {
                    // [SỬA LỖI TẠI ĐÂY] Đổi #cart-count thành #cart-total-count cho khớp header.php
                    var cartBadge = $('#cart-total-count');
                    
                    if(cartBadge.length) {
                        // Cập nhật số lượng mới
                        cartBadge.text(response.total_items);
                        // Nếu đang ẩn (do = 0) thì hiện lên
                        cartBadge.show();
                        // Hiệu ứng nhún nhảy cho vui mắt
                        cartBadge.addClass('fa-bounce'); 
                        setTimeout(() => cartBadge.removeClass('fa-bounce'), 1000);
                    }
                    
                    showToast('✅ Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert('❌ ' + (response.message || 'Có lỗi xảy ra'));
                }
            },
            error: function(xhr) {
                btn.html(originalHtml).prop('disabled', false);
                console.error("Lỗi:", xhr.responseText);
                alert('Lỗi kết nối đến giỏ hàng.');
            }
        });
    });

        // [2] REVIEW SUBMIT AJAX
        $('#review-form').on('submit', function(e) {
            e.preventDefault(); 
            var btn = $('#btn-submit-review');
            var originalText = btn.text();
            btn.text('Đang xử lý...').prop('disabled', true);
            var formData = $(this).serialize();
            var formAction = $(this).attr('action');

            $.ajax({
                url: formAction,
                type: 'POST',
                data: formData,
                success: function(response) {
                    showToast('✅ Thao tác thành công! Đang tải lại...');
                    $('#formReview').slideUp();
                    setTimeout(function() { location.reload(); }, 1500);
                },
                error: function() {
                    // Fallback reload
                    showToast('✅ Đã gửi dữ liệu!');
                    setTimeout(function() { location.reload(); }, 1500);
                }
            });
        });

        function showToast(msg) {
            var t = $('<div style="position:fixed; top:80px; right:20px; background:#28a745; color:#fff; padding:15px 20px; border-radius:5px; z-index:9999; box-shadow:0 5px 15px rgba(0,0,0,0.2); font-weight:bold;">'+msg+'</div>');
            $('body').append(t);
            setTimeout(() => t.fadeOut(500, () => t.remove()), 2500);
        }
    });
</script>