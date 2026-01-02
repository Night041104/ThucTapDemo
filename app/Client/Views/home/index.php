<style>
    /* 1. Thiết lập nền chung cho body (Giả lập nếu header chưa set) */
    body {
        background-color: #f3f3f3 !important; /* Màu nền xám nhạt chuẩn FPT */
    }

    /* 2. Slider & Banner Effects */
    .carousel-inner img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px; /* Bo góc banner chính */
    }
    
    .carousel-indicators button {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50%;
        margin: 0 4px !important;
        background-color: rgba(255,255,255,0.7) !important;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .carousel-indicators .active {
        background-color: #cd1818 !important;
        transform: scale(1.2);
    }
    .custom-control {
        width: 40px;
        opacity: 0;
        transition: 0.3s;
    }
    #mainCarousel:hover .custom-control { opacity: 1; }
    
    .banner-hover-effect {
        overflow: hidden;
        border-radius: 8px; /* Bo góc banner phụ */
    }
    .banner-hover-effect img {
        transition: transform 0.5s ease;
        border-radius: 8px;
    }
    .banner-hover-effect:hover img {
        transform: scale(1.05);
    }

    /* 3. Product Card Style (Giống FPT) */
    .product-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid transparent; /* Viền trong suốt */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* Bóng nhẹ mặc định */
    }
    
    .product-card:hover {
        border-color: #cd1818; /* Viền đỏ khi hover */
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Bóng đậm khi hover */
        transform: translateY(-2px);
    }

    .card-img-top {
        padding: 15px;
        transition: transform 0.3s;
    }
    .product-card:hover .card-img-top {
        transform: translateY(-5px); /* Ảnh nảy lên nhẹ */
    }

    .discount-badge {
        background-color: #cd1818;
        color: white;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: bold;
        border-radius: 4px;
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .product-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 40px; /* Cố định chiều cao tên 2 dòng */
        margin-bottom: 8px;
    }
    .product-name:hover { color: #cd1818; }

    .price-show { color: #cd1818; font-weight: bold; font-size: 16px; }
    .price-through { color: #999; font-size: 12px; text-decoration: line-through; }

    /* 4. Section Styling */
    .section-title {
        background-color: #cd1818;
        color: white;
        padding: 10px 15px;
        border-radius: 8px 8px 0 0; /* Bo góc trên */
        text-transform: uppercase;
        font-weight: 700;
        font-size: 16px;
    }
    
    .white-block {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .flash-sale-bg {
        background: linear-gradient(90deg, #d70018 0%, #ff6b6b 100%);
        border-radius: 8px;
        padding: 20px;
    }

    .cate-item {
        transition: 0.3s;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 10px;
        background: #fff;
    }
    .cate-item:hover {
        border-color: #cd1818;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .cate-icon-box {
        width: 50px; height: 50px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 5px auto;
    }
</style>

<div class="container mt-3">
    
    <div class="row mb-3">
        <div class="col-12">
            <div id="mainCarousel" class="carousel slide carousel-fade shadow-sm rounded-3 overflow-hidden" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-indicators custom-indicators">
                    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="uploads/banners/slider_1.png" alt="Banner 1">
                    </div>
                    <div class="carousel-item">
                        <img src="uploads/banners/slider_2.png" alt="Banner 2">
                    </div>
                    <div class="carousel-item">
                        <img src="uploads/banners/slider_3.jpg" alt="Banner 3" onerror="this.src='uploads/banners/slider_1.png'">
                    </div>
                </div>
                <button class="carousel-control-prev custom-control" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next custom-control" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-6">
            <div class="banner-hover-effect shadow-sm">
                <a href="#"><img src="uploads/banners/sub_banner_1.webp" class="w-100" alt="Quảng cáo 1"></a>
            </div>
        </div>
        <div class="col-6">
            <div class="banner-hover-effect shadow-sm">
                <a href="#"><img src="uploads/banners/sub_banner_2.webp" class="w-100" alt="Quảng cáo 2"></a>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="white-block">
        <h6 class="fw-bold mb-3 text-uppercase" style="border-left: 4px solid #cd1818; padding-left: 10px;">Danh mục nổi bật</h6>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <?php 
            if(isset($categories) && is_array($categories)):
                foreach($categories as $cate): 
                    $icon = 'fa-circle-notch';
                    if(strpos($cate['slug'], 'dien-thoai')!==false) $icon='fa-mobile-screen';
                    elseif(strpos($cate['slug'], 'laptop')!==false) $icon='fa-laptop';
                    elseif(strpos($cate['slug'], 'tai-nghe')!==false) $icon='fa-headphones';
                    elseif(strpos($cate['slug'], 'dong-ho')!==false) $icon='fa-clock';
                    elseif(strpos($cate['slug'], 'pc')!==false) $icon='fa-desktop';
            ?>
            <a href="index.php?controller=category&id=<?= $cate['id'] ?>" class="text-decoration-none text-dark cate-item" style="width: 100px; text-align: center;">
                <div class="cate-icon-box">
                    <i class="fa <?= $icon ?> fs-4 text-secondary"></i>
                </div>
                <div class="small fw-bold" style="font-size: 12px; line-height: 1.2;"><?= htmlspecialchars($cate['name']) ?></div>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php if(!empty($hotProducts)): ?>
<div class="container mt-3">
    <div class="flash-sale-bg shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-white fst-italic m-0 text-uppercase" style="font-size: 20px;">
                <i class="fa fa-bolt text-warning me-2"></i>Giờ vàng giá sốc
            </h3>
            <div class="text-white fw-bold d-flex align-items-center gap-2">
                <span class="small">Kết thúc trong:</span>
                <span class="badge bg-dark rounded px-2">02</span>:
                <span class="badge bg-dark rounded px-2">45</span>:
                <span class="badge bg-dark rounded px-2">12</span>
            </div>
        </div>
        
        <div class="row row-cols-2 row-cols-md-5 g-2">
            <?php foreach($hotProducts as $p): ?>
            <div class="col">
                <div class="product-card h-100 position-relative">
                    <span class="discount-badge">Giảm 15%</span>
                    <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>">
                        <img src="<?= htmlspecialchars($p['thumbnail']) ?>" class="card-img-top" style="height: 160px; object-fit: contain;">
                    </a>
                    <div class="card-body p-2 text-center d-flex flex-column">
                        <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="product-name text-decoration-none">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                        <div class="mt-auto">
                            <div class="price-show"><?= number_format($p['price']) ?>₫</div>
                            <div class="price-through"><?= number_format($p['market_price']) ?>₫</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!empty($phoneProducts)): ?>
<div class="container mt-4 mb-5">
    <div class="white-block p-0 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="fw-bold text-uppercase m-0">Điện thoại nổi bật</h5>
            <a href="index.php?controller=category&id=3" class="btn btn-outline-danger btn-sm rounded-pill px-3">Xem tất cả</a>
        </div>
        
        <div class="p-3">
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <?php foreach($phoneProducts as $p): ?>
                <div class="col">
                    <div class="product-card h-100 position-relative">
                        <span class="discount-badge">Trả góp 0%</span>
                        <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="d-block text-center pt-3">
                            <img src="<?= htmlspecialchars($p['thumbnail']) ?>" class="img-fluid" style="height: 180px; object-fit: contain;">
                        </a>
                        <div class="card-body p-3">
                            <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="product-name text-decoration-none">
                                <?= htmlspecialchars($p['name']) ?>
                            </a>
                            <div class="d-flex align-items-center gap-2 mt-auto">
                                <span class="price-show fs-5"><?= number_format($p['price']) ?>₫</span>
                                <?php if($p['market_price'] > $p['price']): ?>
                                    <span class="price-through"><?= number_format($p['market_price']) ?>₫</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>