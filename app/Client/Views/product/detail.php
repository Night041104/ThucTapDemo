<style>
    /* =========================================
       1. CẤU TRÚC LAYOUT CHÍNH
       ========================================= */
    .detail-container { max-width: 1200px; margin: 20px auto; display: flex; gap: 30px; padding: 0 15px; font-family: 'Segoe UI', sans-serif; }
    .col-left { width: 45%; }
    .col-right { width: 55%; }

    /* Breadcrumb */
    .breadcrumb { margin-top: 15px; font-size: 13px; color: #666; margin-bottom: 10px;}
    .breadcrumb a { text-decoration: none; color: #666; }
    .breadcrumb a:hover { color: #cd1818; }

    /* =========================================
       2. ẢNH SẢN PHẨM & THUMBNAILS
       ========================================= */
    .main-image-box { border: 1px solid #eee; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 15px; background: white; position: relative; overflow: hidden; }
    .main-image-box img { max-width: 100%; height: 400px; object-fit: contain; transition: 0.3s; }
    
    .thumb-list { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; justify-content: center; }
    .thumb-item { width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; object-fit: cover; opacity: 0.6; transition: 0.2s; }
    .thumb-item:hover, .thumb-item.active { border-color: #cd1818; opacity: 1; }

    /* =========================================
       3. THÔNG TIN & GIÁ
       ========================================= */
    .prod-title { font-size: 24px; font-weight: 700; color: #333; margin-bottom: 5px; line-height: 1.3; }
    .prod-sku { font-size: 13px; color: #777; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;}
    .rating-box { color: #ff9f00; font-size: 13px; }

    /* Giá bán */
    .price-box { display: flex; align-items: flex-end; gap: 15px; margin-bottom: 20px; }
    .current-price { font-size: 28px; font-weight: bold; color: #cd1818; line-height: 1; }
    .market-price { font-size: 16px; color: #666; text-decoration: line-through; }

    /* =========================================
       4. BIẾN THỂ GOM NHÓM (NEW STYLE)
       ========================================= */
    .variant-section { margin-bottom: 20px; }
    .variant-row { margin-bottom: 15px; }
    .variant-name { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; display: block; }
    
    .variant-options { display: flex; flex-wrap: wrap; gap: 10px; }
    
    .opt-btn {
        border: 1px solid #ddd; background: #fff; color: #333;
        padding: 8px 15px; border-radius: 4px; cursor: pointer;
        text-decoration: none; font-size: 13px; min-width: 80px; text-align: center;
        transition: 0.2s; position: relative; display: inline-block;
    }
    .opt-btn:hover { border-color: #cd1818; color: #cd1818; }
    
    .opt-btn.active {
        border-color: #cd1818; color: #cd1818; background: #fff5f5; font-weight: bold;
    }
    /* Dấu tick nhỏ ở góc giống FPT */
    .opt-btn.active::after {
        content: ''; position: absolute; top: 0; right: 0;
        width: 0; height: 0;
        border-style: solid; border-width: 0 12px 12px 0;
        border-color: transparent #cd1818 transparent transparent;
    }
    .opt-btn.active::before {
        content: '✓'; position: absolute; top: -1px; right: 0px;
        color: white; font-size: 7px; z-index: 1; font-weight: bold;
    }

    /* =========================================
       5. KHUYẾN MÃI & NÚT MUA
       ========================================= */
    .promo-box { border:1px solid #fee2e2; background:#fff1f2; padding:15px; border-radius:8px; margin-bottom:20px; }
    .promo-title { color:#be123c; font-weight:bold; font-size:14px; margin-bottom:10px; display:flex; align-items:center; gap:5px; }
    .promo-list { list-style: none; padding: 0; margin: 0; }
    .promo-list li { font-size: 13px; color: #333; margin-bottom: 5px; padding-left: 15px; position: relative; }
    .promo-list li::before { content: "•"; color: #be123c; font-weight: bold; position: absolute; left: 0; }

    .action-box { display: flex; gap: 10px; margin-top: 20px; }
    .btn-buy-now { 
        flex: 1; background: #cd1818; color: white; border: none; border-radius: 4px; padding: 12px; 
        font-size: 16px; font-weight: bold; text-transform: uppercase; cursor: pointer; text-align: center; transition: 0.2s;
    }
    .btn-buy-now span { display: block; font-size: 11px; font-weight: normal; text-transform: none; margin-top: 2px; opacity: 0.9; }
    .btn-buy-now:hover { background: #b71c1c; box-shadow: 0 4px 8px rgba(205, 24, 24, 0.2); }

    .btn-add-cart {
        width: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center;
        border: 1px solid #cd1818; border-radius: 4px; background: white; color: #cd1818; cursor: pointer; font-size: 9px; transition: 0.2s;
    }
    .btn-add-cart i { font-size: 22px; margin-bottom: 2px; }
    .btn-add-cart:hover { background: #fff5f5; }

    /* =========================================
       6. MINI SPECS & MODAL
       ========================================= */
    .mini-specs { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #eee; margin-top: 20px; }
    .mini-specs ul { padding: 0; margin: 0; list-style: none; }
    .mini-specs li { display: flex; margin-bottom: 8px; font-size: 13px; border-bottom: 1px solid #f1f1f1; padding-bottom: 8px; }
    .mini-specs li:last-child { border-bottom: none; }
    .mini-specs li strong { width: 130px; min-width: 130px; color: #555; font-weight: 500; }
    .mini-specs li span { color: #333; }

    .btn-show-specs {
        display: block; width: 100%; padding: 10px; margin-top: 10px;
        background: white; border: 1px solid #ddd; border-radius: 4px;
        color: #333; font-weight: 500; cursor: pointer; transition: 0.2s;
        text-align: center; font-size: 13px;
    }
    .btn-show-specs:hover { background: #f9f9f9; border-color: #999; color: #cd1818; }

    /* Modal CSS */
    .specs-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999;
        opacity: 0; visibility: hidden; transition: 0.3s;
    }
    .specs-overlay.active { opacity: 1; visibility: visible; }

    .specs-panel {
        position: absolute; top: 0; right: 0; bottom: 0;
        width: 600px; max-width: 90%;
        background: white; box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        transform: translateX(100%); transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex; flex-direction: column;
    }
    .specs-overlay.active .specs-panel { transform: translateX(0); }

    .sp-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; background: #fff; }
    .sp-title { font-weight: bold; font-size: 18px; color: #333; }
    .btn-close-specs { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; line-height: 1; }
    
    .sp-tabs {
        display: flex; gap: 20px; overflow-x: auto; padding: 0 20px;
        border-bottom: 1px solid #eee; background: #fff; scroll-behavior: smooth;
        white-space: nowrap; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .sp-tabs::-webkit-scrollbar { height: 0px; }
    .sp-tab-item {
        font-size: 14px; color: #666; cursor: pointer; padding: 12px 0; border-bottom: 3px solid transparent; font-weight: 500;
    }
    .sp-tab-item.active { color: #cd1818; border-color: #cd1818; }

    .sp-body { flex: 1; overflow-y: auto; padding: 20px; scroll-behavior: smooth; background: #fff; }
    .sp-group { margin-bottom: 30px; scroll-margin-top: 10px; }
    .sp-group-title { font-size: 16px; font-weight: bold; color: #333; margin-bottom: 15px; padding-left: 10px; border-left: 4px solid #cd1818; }
    
    .sp-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .sp-row:last-child { border-bottom: none; }
    .sp-label { width: 40%; color: #666; padding-right: 15px; }
    .sp-val { width: 60%; color: #333; font-weight: 500; }

    /* CSS ĐÁNH GIÁ */
    .review-section { border-top: 1px solid #eee; padding: 30px 20px; background: #fff; margin-top: 20px; border-radius: 8px; }
    .rating-summary { display: flex; flex-direction: column; align-items: center; gap: 25px; margin-bottom: 40px; background: #fdfdfd; padding: 25px; border-radius: 12px;}
    .avg-score { text-align: center; }
    .avg-score h2 { font-size: 56px; color: #fe2c6a; margin: 0; line-height: 1; }
    .stars { color: #ffbe00; font-size: 24px; margin: 10px 0; }
    .progress-bars { width: 100%; max-width: 450px; }
    .bar-item { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
    .bar-bg { background: #eee; height: 10px; flex-grow: 1; border-radius: 5px; overflow: hidden; }
    .bar-fill { background: #fe2c6a; height: 100%; border-radius: 5px; }
    .btn-review-action { background: #cd1818; color: white; border: none; padding: 12px 35px; border-radius: 25px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-review-action:hover { background: #b71c1c; transform: scale(1.05); }
    .review-item { border-bottom: 1px solid #f1f1f1; padding: 20px 0; text-align: left; }
    .user-info { font-weight: bold; display: flex; align-items: center; gap: 10px; }
</style>

<div class="container breadcrumb">
    <a href="trang-chu">Trang chủ</a> / 
    
    <a href="danh-muc/<?= $category['slug'] ?>"><?= $category['name'] ?? 'Danh mục' ?></a> / 
    
    <span style="color: #333;"><?= $product['name'] ?></span>
</div>

<div class="detail-container">
    <div class="col-left">
        <div class="main-image-box">
            <img id="main-img" src="<?= $product['thumbnail'] ?>" alt="<?= $product['name'] ?>">
        </div>
        
        <div class="thumb-list">
            <img src="<?= $product['thumbnail'] ?>" class="thumb-item active" onclick="changeImage(this.src)">
            <?php if(!empty($gallery)): ?>
                <?php foreach($gallery as $img): ?>
                    <img src="<?= $img['image_url'] ?>" class="thumb-item" onclick="changeImage(this.src)">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if(!empty($specs)): ?>
        <div class="mini-specs">
            <h4 style="margin: 0 0 15px 0; font-size: 16px;">Thông số kỹ thuật</h4>
            <ul>
                <?php 
                $count = 0;
                foreach($specs as $group) {
                    if(isset($group['items'])) {
                        foreach($group['items'] as $item) {
                            if($count++ > 4) break 2; 
                            echo "<li><strong>{$item['name']}:</strong> <span>{$item['value']}</span></li>";
                        }
                    }
                }
                ?>
            </ul>
            <button type="button" class="btn-show-specs" onclick="openSpecs()">
                Xem cấu hình chi tiết <i class="fa fa-chevron-right" style="font-size: 10px; margin-left: 5px;"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-right">
        <h1 class="prod-title"><?= $product['name'] ?></h1>
        <div class="prod-sku">
            <span>SKU: <?= $product['sku'] ?></span> | 
            <span class="rating-box">
                <?php 
                    $avg = $reviewStats['avg'] ?? 0;
                    for($i=1; $i<=5; $i++) echo ($i <= $avg) ? '★' : '☆';
                ?>
                (<?= $reviewStats['total'] ?? 0 ?> đánh giá)
            </span>
        </div>

        <div class="price-box">
            <div class="current-price"><?= number_format($product['price']) ?>₫</div>
            <?php if($product['market_price'] > $product['price']): ?>
                <div class="market-price"><?= number_format($product['market_price']) ?>₫</div>
            <?php endif; ?>
        </div>

        <?php if(!empty($variantGroups)): ?>
            <div class="variant-section">
                <?php foreach($variantGroups as $groupName => $options): ?>
                    <div class="variant-row">
                        <span class="variant-name"><?= htmlspecialchars($groupName) ?></span>
                        <div class="variant-options">
                            <?php foreach($options as $valText => $info): ?>
                                <a href="san-pham/<?= $info['slug'] ?>.html" 
                                   class="opt-btn <?= $info['active'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($valText) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="promo-box">
            <div class="promo-title"><i class="fa fa-gift"></i> ƯU ĐÃI THÊM</div>
            <ul class="promo-list">
                <li>Giảm ngay 500.000đ khi thanh toán qua ZaloPay/VNPAY.</li>
                <li>Thu cũ đổi mới: Trợ giá đến 2 triệu đồng.</li>
                <li>Tặng kèm ốp lưng chính hãng trị giá 300.000đ.</li>
                <li>Bảo hành chính hãng 12 tháng, lỗi 1 đổi 1 trong 30 ngày.</li>
            </ul>
        </div>

        <form method="POST" action="index.php?module=client&controller=cart&action=add">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            
            <div class="action-box">
                <button type="submit" name="buy_now" value="1" class="btn-buy-now">
                    MUA NGAY
                    <span>(Giao tận nơi hoặc nhận tại cửa hàng)</span>
                </button>

                <button type="submit" name="add_to_cart" value="1" class="btn-add-cart" title="Thêm vào giỏ hàng">
                    <i class="fa fa-cart-plus"></i>
                    <span>Thêm giỏ</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="specs-modal" class="specs-overlay" onclick="closeSpecs(event)">
    <div class="specs-panel" onclick="event.stopPropagation()">
        
        <div class="sp-header">
            <div style="display:flex; align-items:center; gap:15px;">
                <img src="<?= $product['thumbnail'] ?>" style="width:40px; height:40px; object-fit:contain;">
                <span class="sp-title">Thông số kỹ thuật</span>
            </div>
            <button class="btn-close-specs" onclick="closeSpecs()">×</button>
        </div>

        <div class="sp-tabs">
            <?php foreach($specs as $index => $group): ?>
                <div class="sp-tab-item" onclick="scrollToGroup('group-<?= $index ?>', this)">
                    <?= $group['group_name'] ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sp-body" id="sp-body-container">
            <?php foreach($specs as $index => $group): ?>
                <div id="group-<?= $index ?>" class="sp-group">
                    <div class="sp-group-title"><?= $group['group_name'] ?></div>
                    <?php if(isset($group['items'])): ?>
                        <?php foreach($group['items'] as $item): ?>
                            <div class="sp-row">
                                <div class="sp-label"><?= $item['name'] ?></div>
                                <div class="sp-val"><?= $item['value'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="review-section container">
    <h3 style="text-align: center; margin-bottom: 30px; font-size: 22px;">Đánh giá sản phẩm <?= $product['name'] ?></h3>
    
    <div class="rating-summary">
        <div class="avg-score">
            <h2><?= $reviewStats['avg'] ?>/5</h2>
            <div class="stars">
                <?php 
                $avg = $reviewStats['avg'] ?? 0;
                for($i=1; $i<=5; $i++) echo ($i <= $avg) ? '★' : '☆'; 
                ?>
            </div>
            <p><?= $reviewStats['total'] ?> đánh giá</p>
        </div>
        <div class="progress-bars">
            <?php for($i=5; $i>=1; $i--): 
                $percent = ($reviewStats['total'] > 0) ? ($reviewStats[$i] / $reviewStats['total']) * 100 : 0;
            ?>
            <div class="bar-item">
                <span style="min-width: 25px;"><?= $i ?>★</span>
                <div class="bar-bg"><div class="bar-fill" style="width: <?= $percent ?>%"></div></div>
                <span style="min-width: 25px; text-align: right;"><?= $reviewStats[$i] ?></span>
            </div>
            <?php endfor; ?>
        </div>
        <?php if(!$userReview): ?>
        <button onclick="<?= isset($_SESSION['user']) ? "$('#formReview').toggle()" : "window.location.href='index.php?module=client&controller=auth&action=login'" ?>" class="btn-review-action">
            Viết đánh giá
        </button>
        <?php else: ?>
        <div style="flex: 1; text-align: center; color: #28a745; font-weight: 500;">
            <i class="fa fa-check-circle"></i> Bạn đã đánh giá sản phẩm này
        </div>
        <?php endif; ?>
    </div>

    <div id="formReview" style="display:none; margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #fdfdfd;">
        <?php if(isset($_SESSION['user'])): ?>
        <form action="index.php?module=client&controller=review&action=submit" method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <label style="font-weight: bold; display: block; margin-bottom: 10px;">
                <?= $userReview ? 'Chỉnh sửa đánh giá của bạn:' : 'Gửi đánh giá mới:' ?>
            </label>
            <select name="rating" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 15px;">
                <?php for($i=5; $i>=1; $i--): ?>
                    <option value="<?= $i ?>" <?= (isset($userReview) && $userReview['rating'] == $i) ? 'selected' : '' ?>>
                        <?= $i ?> sao <?= $i==5 ? '(Rất tốt)' : '' ?>
                    </option>
                <?php endfor; ?>
            </select>
            <textarea name="comment" required style="width: 100%; height: 100px; padding: 10px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 15px;" placeholder="Cảm nhận của bạn..."><?= $userReview['comment'] ?? '' ?></textarea>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-review-action" style="padding: 10px 20px;">
                    <?= $userReview ? 'Cập nhật' : 'Hoàn tất' ?>
                </button>
                <button type="button" onclick="$('#formReview').hide()" style="background: #eee; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer;">Hủy</button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <div class="review-list">
    <?php if(empty($reviews)): ?>
        <p style="text-align:center; color:#999;">Chưa có đánh giá nào cho sản phẩm này.</p>
    <?php endif; ?>

    <?php foreach($reviews as $rev): ?>
        <div class="review-item">
            <div class="user-info">
                <?= htmlspecialchars($rev['fname'] . ' ' . $rev['lname']) ?>
                <span style="color:#ffbe00; margin-left:10px;">
                    <?= str_repeat('★', $rev['rating']) ?>
                </span>
            </div>
            
            <p style="margin:8px 0;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <small style="color:#999"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>

                <?php if(isset($_SESSION['user']) && $rev['user_id'] == $_SESSION['user']['id']): ?>
                    <div class="action-links">
                        <a href="javascript:void(0)" onclick="$('#formReview').show(); window.scrollTo(0, $('#formReview').offset().top - 100);" style="color:#007bff; font-size:12px;">Sửa</a>
                        <span style="color:#ddd">|</span>
                        <a href="index.php?controller=review&action=delete&id=<?= $rev['id'] ?>" style="color:red; font-size:12px;" onclick="return confirm('Xóa đánh giá này?')">Xóa</a>
                    </div>
                <?php elseif(isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
                    <a href="index.php?controller=review&action=delete&id=<?= $rev['id'] ?>" style="color:red; font-size:12px;" onclick="return confirm('Xóa đánh giá này (Admin)?')">Xóa</a>
                    <span style="color:#ddd">|</span>
                    <a href="javascript:void(0)" onclick="toggleReplyForm(<?= $rev['id'] ?>)" style="color:#28a745; font-size:12px; font-weight:bold;">Phản hồi</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($rev['replies'])): ?>
                <?php foreach ($rev['replies'] as $reply): ?>
                    <div class="admin-reply-box" style="margin-left: 40px; background: #f9f9f9; padding: 12px; border-left: 3px solid #cd1818; border-radius: 4px; margin-top: 10px;">
                        <div style="margin-bottom: 5px;">
                            <strong style="color: #cd1818;"><i class="fa fa-check-circle"></i> FPT Shop Trả lời:</strong>
                        </div>
                        <div style="font-size: 14px; color: #333; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($reply['reply_content'])) ?>
                        </div>
                        <div style="margin-top: 5px;">
                            <small style="color: #999; font-size: 11px;"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
                <div id="reply-form-<?= $rev['id'] ?>" style="display:none; margin-left: 40px; margin-top: 15px; background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                    <form action="index.php?module=admin&controller=review&action=reply" method="POST">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <textarea name="reply_text" required style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" placeholder="Nhập nội dung phản hồi khách hàng..."></textarea>
                        <div style="margin-top: 10px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-review-action" style="padding: 6px 15px; font-size: 13px; border-radius: 4px;">Gửi phản hồi</button>
                            <button type="button" onclick="toggleReplyForm(<?= $rev['id'] ?>)" style="background: #eee; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-size: 13px;">Hủy</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleReplyForm(id) {
        var form = document.getElementById('reply-form-' + id);
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function changeImage(src) {
        document.getElementById('main-img').src = src;
        document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
    }

    function openSpecs() {
        document.getElementById('specs-modal').classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }

    function closeSpecs() {
        document.getElementById('specs-modal').classList.remove('active');
        document.body.style.overflow = ''; 
    }

    function scrollToGroup(id, tabElement) {
        const element = document.getElementById(id);
        const container = document.getElementById('sp-body-container');
        if(container && element) {
            document.querySelectorAll('.sp-tab-item').forEach(el => el.classList.remove('active'));
            tabElement.classList.add('active');
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    $(document).ready(function() {
        $('.btn-add-cart').click(function(e) {
            e.preventDefault(); 
            var form = $(this).closest('form');
            var productId = form.find('input[name="product_id"]').val();
            var quantity = 1;

            $.ajax({
                url: 'index.php?module=client&controller=cart&action=addAjax',
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.status === 'success') {
                        var cartBadge = $('#cart-total-count');
                        cartBadge.text(response.totalQty);
                        cartBadge.show(); 
                        showToast('✅ Đã thêm vào giỏ hàng thành công!');
                        $('.fa-shopping-cart').addClass('fa-bounce');
                        setTimeout(function(){ $('.fa-shopping-cart').removeClass('fa-bounce'); }, 1000);
                    } else {
                        alert('❌ ' + response.message);
                    }
                },
                error: function() {
                    alert('Lỗi hệ thống! Vui lòng thử lại.');
                }
            });
        });

        function showToast(message) {
            var toast = $('<div style="position:fixed; top:80px; right:20px; background:#28a745; color:white; padding:15px 25px; border-radius:5px; box-shadow:0 5px 15px rgba(0,0,0,0.2); z-index:9999; animation: fadeIn 0.5s, fadeOut 0.5s 2.5s forwards;">' + message + '</div>');
            $('body').append(toast);
            setTimeout(function() { toast.remove(); }, 3000);
        }
        $('<style>@keyframes fadeIn {from {opacity:0; transform:translateX(20px);} to {opacity:1; transform:translateX(0);}} @keyframes fadeOut {from {opacity:1;} to {opacity:0;}}</style>').appendTo('head');
    });
</script>