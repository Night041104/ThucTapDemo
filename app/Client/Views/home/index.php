<div class="container mt-3">
    <div id="mainBanner" class="carousel slide shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.fpt.shop/unsafe/fit-in/1200x300/filters:quality(90):fill(white)/fptshop.com.vn/Uploads/Originals/2024/1/31/638423256191764669_F-C1_1200x300.png" class="d-block w-100" alt="Banner 1" style="object-fit:cover; height: 350px;">
            </div>
            <div class="carousel-item">
                <img src="https://images.fpt.shop/unsafe/fit-in/1200x300/filters:quality(90):fill(white)/fptshop.com.vn/Uploads/Originals/2024/2/1/638424169542037703_F-C1_1200x300.png" class="d-block w-100" alt="Banner 2" style="object-fit:cover; height: 350px;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainBanner" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</div>

<div class="container mt-4">
    <div class="bg-white p-3 rounded shadow-sm">
        <h6 class="fw-bold mb-3 text-uppercase border-start border-4 border-danger ps-2">Danh mục nổi bật</h6>
        
        <div class="d-flex flex-wrap justify-content-center gap-4 text-center">
            <?php 
            // Kiểm tra xem biến $categories có tồn tại không (được truyền từ header hoặc controller)
            if (isset($categories) && is_array($categories)): 
                foreach ($categories as $cate):
                    // 1. Tự động lấy Icon bằng hàm đã định nghĩa bên Header
                    // Lưu ý: Hàm getIconBySlug() nằm ở header.php, file này load sau nên dùng được.
                    // Nếu lỗi hàm không tồn tại, ta dùng icon mặc định.
                    if (function_exists('getIconBySlug')) {
                        $iconClass = getIconBySlug($cate['slug']);
                    } else {
                        $iconClass = 'fa-circle-notch'; // Icon dự phòng
                    }

                    // 2. Tạo link (Format giống Header)
                    $link = "index.php?module=client&controller=category&id=" . $cate['id'];
            ?>
                <a href="<?= $link ?>" class="d-block text-dark hover-scale text-decoration-none" style="min-width: 100px;">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 transition-icon" style="width:60px; height:60px;">
                        <i class="fa <?= $iconClass ?> fs-3 text-secondary"></i>
                    </div>
                    <small class="fw-bold"><?= htmlspecialchars($cate['name']) ?></small>
                </a>
            <?php 
                endforeach; 
            else:
            ?>
                <p class="text-muted">Đang cập nhật danh mục...</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(!empty($hotProducts)): ?>
<div class="container mt-4">
    <div class="rounded p-4 shadow-sm" style="background: linear-gradient(to right, #cd1818, #ff5252);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-white fst-italic m-0"><i class="fa fa-bolt text-warning"></i> GIỜ VÀNG GIÁ SỐC</h3>
            <div class="text-white">Kết thúc trong: <span class="badge bg-dark">02</span> : <span class="badge bg-dark">45</span> : <span class="badge bg-dark">12</span></div>
        </div>
        
        <div class="row row-cols-2 row-cols-md-5 g-3">
            <?php foreach($hotProducts as $p): ?>
            <div class="col">
                <div class="card h-100 border-0 p-2 product-card">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">-15%</span>
                    <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>">
                        <img src="<?= htmlspecialchars($p['thumbnail']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>" style="height: 160px; object-fit: contain;">
                    </a>
                    <div class="card-body p-2 text-center d-flex flex-column">
                        <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="text-dark fw-bold text-decoration-none text-truncate mb-1 d-block">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                        <div class="mt-auto">
                            <div class="text-danger fw-bold"><?= number_format($p['price']) ?>₫</div>
                            <div class="text-muted small text-decoration-line-through"><?= number_format($p['market_price']) ?>₫</div>
                            
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 75%"></div>
                            </div>
                            <small class="text-danger d-block mt-1">Đã bán 75</small>
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
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-uppercase border-start border-4 border-danger ps-2 m-0">Điện thoại nổi bật</h4>
        <a href="index.php?controller=product&cate_id=1" class="btn btn-outline-danger btn-sm rounded-pill px-3">Xem tất cả</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach($phoneProducts as $p): ?>
        <div class="col">
            <div class="card h-100 border shadow-sm product-card">
                <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="text-center p-3">
                    <img src="<?= htmlspecialchars($p['thumbnail']) ?>" class="img-fluid" style="height: 180px; object-fit: contain;">
                </a>
                <div class="card-body p-3 pt-0">
                    <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>" class="fw-bold text-dark text-decoration-none product-title d-block mb-2">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                    
                    <div class="d-flex gap-1 mb-2 justify-content-center">
                        <span class="badge bg-light text-secondary border">Chính hãng</span>
                        <span class="badge bg-light text-secondary border">Trả góp 0%</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-danger fw-bold fs-5"><?= number_format($p['price']) ?>₫</span>
                        <?php if($p['market_price'] > $p['price']): ?>
                            <span class="text-muted small text-decoration-line-through"><?= number_format($p['market_price']) ?>₫</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<style>
    /* CSS Bổ sung hiệu ứng */
    .hover-scale { transition: 0.3s; }
    .hover-scale:hover { transform: translateY(-5px); color: #cd1818 !important; }
    
    .hover-scale:hover .transition-icon { 
        background-color: #cd1818 !important; 
        color: white !important;
        box-shadow: 0 4px 10px rgba(205, 24, 24, 0.3);
    }
    .hover-scale:hover .transition-icon i { color: white !important; }

    .product-card { transition: all 0.3s; }
    .product-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: #cd1818 !important; transform: translateY(-3px); }
    .product-title { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 48px; }
</style>