<?php
// =========================================================================
// 1. KHỞI TẠO DỮ LIỆU HEADER
// =========================================================================

require_once __DIR__ . '/../../../models/CategoryModel.php';
require_once __DIR__ . '/../../../models/ProductModel.php';

$cateModel = new CategoryModel();
$prodModel = new ProductModel();

// A. Logic Giỏ hàng & User
$totalQty = 0;
if (isset($_SESSION['cart'])) {
    $totalQty = array_sum($_SESSION['cart']);
}
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? ($_SESSION['user']['lname'] ?? 'Bạn') : '';

// B. Lấy tất cả danh mục
$categories = $cateModel->getAll();

// C. Hàm helper lấy icon
function getIconBySlug($slug) {
    $rules = [
        'dien-thoai' => 'fa-mobile-alt', 'smartphone' => 'fa-mobile-alt',
        'laptop' => 'fa-laptop', 'macbook' => 'fa-laptop',
        'tablet' => 'fa-tablet-alt', 'ipad' => 'fa-tablet-alt',
        'am-thanh' => 'fa-headphones', 'tai-nghe' => 'fa-headphones', 'phu-kien' => 'fa-headphones',
        'dong-ho' => 'fa-clock', 'smartwatch' => 'fa-clock',
        'pc' => 'fa-desktop', 'man-hinh' => 'fa-tv',
        'may-cu' => 'fa-recycle'
    ];
    foreach ($rules as $key => $icon) {
        if (strpos($slug, $key) !== false) return $icon;
    }
    return 'fa-circle-notch'; 
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FPT Shop Clone</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* GIỮ NGUYÊN CSS CŨ CỦA BẠN */
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        a { text-decoration: none; color: inherit; }
        header { background-color: #cd1818; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 24px; font-weight: 800; color: white; }
        .logo span { background: white; color: #cd1818; padding: 2px 6px; border-radius: 4px; margin-right: 2px; }
        .search-form { position: relative; max-width: 500px; width: 100%; }
        .search-form input { border-radius: 4px; border: none; padding-right: 40px; height: 38px; }
        .search-form button { position: absolute; right: 0; top: 0; height: 100%; width: 40px; background: #333; color: white; border: none; border-radius: 0 4px 4px 0; }
        .header-actions .btn-icon { color: white; display: flex; flex-direction: column; align-items: center; font-size: 12px; min-width: 70px; position: relative; }
        .header-actions .btn-icon:hover { color: rgba(255,255,255,0.8); }
        .cart-badge { position: absolute; top: -5px; right: 15px; background: #ffc107; color: #333; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 50%; border: 1px solid #cd1818;}
        .sub-nav { background: #b81414; padding: 0; border-top: 1px solid rgba(255,255,255,0.1); position: relative; }
        .category-btn { background: #cd1818; color: white; padding: 10px 15px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; width: 240px; text-transform: uppercase; font-size: 14px;}
        .menu-wrapper { position: relative; display: inline-block; }
        .sidebar-menu { position: absolute; top: 100%; left: 0; width: 240px; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 1100; display: none; border-radius: 0 0 4px 4px; padding: 5px 0;}
        .menu-wrapper:hover .sidebar-menu { display: block; }
        .sidebar-menu ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu > ul > li > a { display: flex; align-items: center; padding: 8px 15px; color: #333; font-size: 14px; transition: 0.1s; justify-content: space-between;}
        .sidebar-menu > ul > li > a:hover { background-color: #f1f1f1; color: #cd1818; font-weight: 500; }
        .sidebar-menu i.icon-left { width: 25px; text-align: center; color: #555; }
        .mega-content { position: absolute; left: 100%; top: 0; width: 850px; min-height: 100%; background: #fff; box-shadow: 5px 5px 15px rgba(0,0,0,0.1); display: none; padding: 15px; z-index: 1200; border-left: 1px solid #eee;}
        .sidebar-menu > ul > li:hover .mega-content { display: block; }
        .mega-title { font-size: 15px; font-weight: bold; margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 8px; }
        .menu-product-grid { display: flex; gap: 15px; }
        .menu-product-item { width: 25%; border: 1px solid #eee; border-radius: 4px; padding: 10px; text-align: center; transition: 0.2s; }
        .menu-product-item:hover { border-color: #cd1818; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .menu-product-item img { width: 100%; height: 120px; object-fit: contain; margin-bottom: 8px; }
        .menu-product-name { font-size: 13px; font-weight: 600; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 5px; color: #333;}
        .menu-product-price { color: #cd1818; font-weight: bold; font-size: 14px; }
        .quick-links a { color: white; font-size: 13px; font-weight: 500; margin-right: 20px; text-transform: uppercase; }
        .quick-links a:hover { color: #ffc107; }
    </style>
</head>
<body>

<header>
    <div class="container py-2">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <a href="index.php?module=client&controller=home" class="logo me-3"><span>FPT</span>Shop</a>

            <form action="index.php" method="GET" class="search-form d-none d-md-block flex-grow-1 mx-4">
                <input type="hidden" name="module" value="client">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                <input type="text" name="keyword" class="form-control" placeholder="Bạn muốn tìm gì hôm nay?">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            <div class="header-actions d-flex">
                <a href="#" class="btn-icon d-none d-lg-flex"><i class="fa-solid fa-file-invoice"></i><span>Thông tin</span></a>
                <a href="index.php?controller=order&action=history" class="btn-icon"><i class="fa-solid fa-truck-fast"></i><span>Đơn hàng</span></a>
                
                <a href="index.php?controller=cart" class="btn-icon">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Giỏ hàng</span>
                    <span id="cart-total-count" class="cart-badge" style="<?= $totalQty > 0 ? '' : 'display:none;' ?>">
                        <?= $totalQty ?>
                    </span>
                </a>

                <?php if ($isLoggedIn): ?>
                    <a href="index.php?controller=account&action=profile" class="btn-icon"><i class="fa fa-user-circle"></i><span><?= htmlspecialchars($userName) ?></span></a>
                <?php else: ?>
                    <a href="index.php?module=client&controller=auth&action=login" class="btn-icon"><i class="fa fa-user"></i><span>Đăng nhập</span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="sub-nav">
        <div class="container d-flex align-items-center">
            
            <div class="menu-wrapper">
                <div class="category-btn">
                    <i class="fa fa-bars me-2"></i> Danh mục sản phẩm
                </div>

                <div class="sidebar-menu">
                    <ul>
                        <?php foreach ($categories as $cate): 
                            $cId = $cate['id'];
                            $cName = $cate['name'];
                            $cSlug = $cate['slug'];
                            
                            $iconClass = getIconBySlug($cSlug);

                            // --- [SỬA LỖI TẠI ĐÂY] ---
                            // Đổi tên biến $products thành $menuProducts để không ghi đè biến của Controller
                            $menuProducts = $prodModel->getProductsByCateForClient($cId);
                            $hotProducts = array_slice($menuProducts, 0, 4);
                        ?>
                        <li>
                            <a href="index.php?module=client&controller=category&id=<?= $cId ?>">
                                <span><i class="fa <?= $iconClass ?> icon-left"></i> <?= htmlspecialchars($cName) ?></span>
                                <i class="fa fa-chevron-right icon-right"></i>
                            </a>

                            <?php if (!empty($hotProducts)): ?>
                            <div class="mega-content">
                                <h3 class="mega-title">Sản phẩm nổi bật - <?= htmlspecialchars($cName) ?></h3>
                                <div class="menu-product-grid">
                                    <?php foreach ($hotProducts as $p): ?>
                                    <div class="menu-product-item">
                                        <a href="index.php?module=client&controller=product&action=detail&id=<?= $p['id'] ?>">
                                            <?php $img = !empty($p['thumbnail']) ? $p['thumbnail'] : 'https://via.placeholder.com/150'; ?>
                                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                            <div class="menu-product-name"><?= htmlspecialchars($p['name']) ?></div>
                                            <div class="menu-product-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="quick-links ms-4 d-flex">
                <?php 
                $count = 0;
                foreach ($categories as $cate): 
                    if ($count >= 4) break; 
                ?>
                    <a href="index.php?module=client&controller=category&id=<?= $cate['id'] ?>">
                        <?= htmlspecialchars($cate['name']) ?>
                    </a>
                <?php $count++; endforeach; ?>
                
                <a href="#"><i class="fa fa-fire me-1"></i> Khuyến mãi</a>
                <a href="#"><i class="fa fa-newspaper me-1"></i> Tin tức</a>
            </div>

        </div>
    </div>
</header>