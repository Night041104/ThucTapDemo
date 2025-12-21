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
       6. MINI SPECS & MODAL (SLIDING DRAWER)
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
</style>

<div class="container breadcrumb">
    <a href="index.php">Trang chủ</a> / 
    <a href="index.php?module=client&controller=category&action=index&id=<?= $product['category_id'] ?>"><?= $category['name'] ?? 'Danh mục' ?></a> / 
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
                            if($count++ > 4) break 2; // Chỉ hiện 5 dòng đầu
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
            <span class="rating-box">★★★★★ (15 đánh giá)</span>
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
                                <a href="index.php?module=client&controller=product&action=detail&id=<?= $info['product_id'] ?>" 
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

<script>
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
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script>
    $(document).ready(function() {
        // Bắt sự kiện click nút "Thêm giỏ" (Class .btn-add-cart)
        $('.btn-add-cart').click(function(e) {
            e.preventDefault(); // 1. Chặn việc load lại trang

            // 2. Lấy dữ liệu từ form
            var form = $(this).closest('form');
            var productId = form.find('input[name="product_id"]').val();
            var quantity = 1; // Mặc định là 1 (vì nút thêm giỏ ko có ô nhập số lượng riêng ở layout này)
            
            // Nếu muốn lấy số lượng chính xác thì dùng dòng dưới (nếu layout có ô input quantity)
            // var quantity = form.find('input[name="quantity"]').val();

            // 3. Gửi Ajax
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
                        // A. Cập nhật số lượng trên Header
                        var cartBadge = $('#cart-total-count');
                        cartBadge.text(response.totalQty);
                        cartBadge.show(); // Hiện lên nếu đang bị ẩn

                        // B. Hiệu ứng bay hoặc thông báo nhỏ (Toast)
                        showToast('✅ Đã thêm vào giỏ hàng thành công!');
                        
                        // C. Hiệu ứng rung nhẹ icon giỏ hàng cho đẹp
                        $('.fa-shopping-cart').addClass('fa-bounce');
                        setTimeout(function(){ 
                            $('.fa-shopping-cart').removeClass('fa-bounce'); 
                        }, 1000);

                    } else {
                        alert('❌ ' + response.message);
                    }
                },
                error: function() {
                    alert('Lỗi hệ thống! Vui lòng thử lại.');
                }
            });
        });

        // Hàm hiển thị thông báo góc màn hình (Toast Message)
        function showToast(message) {
            // Tạo thẻ div thông báo
            var toast = $('<div style="position:fixed; top:80px; right:20px; background:#28a745; color:white; padding:15px 25px; border-radius:5px; box-shadow:0 5px 15px rgba(0,0,0,0.2); z-index:9999; animation: fadeIn 0.5s, fadeOut 0.5s 2.5s forwards;">' + message + '</div>');
            
            $('body').append(toast);
            
            // Tự xóa sau 3 giây
            setTimeout(function() {
                toast.remove();
            }, 3000);
        }
        
        // CSS Animation cho Toast
        $('<style>@keyframes fadeIn {from {opacity:0; transform:translateX(20px);} to {opacity:1; transform:translateX(0);}} @keyframes fadeOut {from {opacity:1;} to {opacity:0;}}</style>').appendTo('head');
    });
</script>