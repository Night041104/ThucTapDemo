<?php
// FILE: Views/layouts/header.php

// 1. KHỞI TẠO CÁC MODEL CẦN THIẾT
$rootPath = dirname(__DIR__, 3); 
require_once $rootPath . '/models/CategoryModel.php';
require_once $rootPath . '/models/ProductModel.php';
// Không cần BrandModel nữa vì đã bỏ phần hãng sản xuất
// require_once $rootPath . '/models/BrandModel.php'; 

$cateModel = new CategoryModel();
$prodModel = new ProductModel();
// $brandModel = new BrandModel(); 

// 2. LOGIC GIỎ HÀNG & USER
$totalQty = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? ($_SESSION['user']['lname'] ?? 'Bạn') : '';
$userAvatar = isset($_SESSION['user']['avatar']) && $_SESSION['user']['avatar'] != 'default_avt.png' 
              ? $_SESSION['user']['avatar'] 
              : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

// 3. LẤY DỮ LIỆU
$categories = $cateModel->getAll();

// 4. HÀM HELPER ICON
function getIconBySlug($slug) {
    if (strpos($slug, 'dien-thoai') !== false) return 'fa-mobile-screen-button';
    if (strpos($slug, 'laptop') !== false) return 'fa-laptop';
    if (strpos($slug, 'may-tinh-bang') !== false) return 'fa-tablet-screen-button';
    if (strpos($slug, 'apple') !== false) return 'fa-apple';
    if (strpos($slug, 'dong-ho') !== false) return 'fa-clock';
    if (strpos($slug, 'tai-nghe') !== false) return 'fa-headphones';
    if (strpos($slug, 'phu-kien') !== false) return 'fa-charging-station';
    if (strpos($slug, 'man-hinh') !== false) return 'fa-desktop';
    return 'fa-layer-group';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FPT Shop Clone System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* CSS GLOBAL */
        body { background-color: #f4f4f4; font-family: 'Roboto', sans-serif; color: #333; font-size: 14px; }
        a { text-decoration: none; color: inherit; transition: all 0.2s ease; }
        ul { padding: 0; margin: 0; list-style: none; }

        /* 1. TOP BAR */
        .top-promo { 
            background: #212529; 
            color: white; 
            font-size: 12px; 
            height: 36px; 
            display: flex; 
            align-items: center; 
        }
        .top-promo a { color: #bbb; margin-left: 20px; font-weight: 400; }
        .top-promo a:hover { color: #fff; text-decoration: underline; }

        /* 2. MAIN HEADER */
        header { 
            background-color: #cd1818; 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }

        .logo { 
            font-size: 28px; 
            font-weight: 800; 
            color: white; 
            letter-spacing: -0.5px; 
            display: flex; 
            align-items: center; 
        }
        .logo span { 
            background: white; 
            color: #cd1818; 
            padding: 2px 8px; 
            border-radius: 4px; 
            margin-right: 6px; 
            font-size: 24px; 
        }

        .search-container { position: relative; max-width: 600px; width: 100%; }
        .search-input { 
            width: 100%; 
            height: 42px; 
            border: none; 
            border-radius: 4px; 
            padding: 0 55px 0 16px; 
            font-size: 15px; 
            outline: none; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-btn { 
            position: absolute; 
            right: 2px; 
            top: 2px; 
            height: 38px; 
            width: 50px; 
            background: #333; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
        }
        .search-btn:hover { background: #000; }

        .header-actions { display: flex; gap: 25px; margin-left: 30px; }
        .action-item { 
            color: white; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            font-size: 12px; 
            font-weight: 500;
            position: relative; 
            min-width: 65px; 
            cursor: pointer; 
            opacity: 0.9;
        }
        .action-item:hover { opacity: 1; transform: translateY(-1px); }
        .action-item i { font-size: 22px; margin-bottom: 5px; } 
        .cart-badge { 
            position: absolute; 
            top: -6px; 
            right: 12px; 
            background: #ffc107; 
            color: #000; 
            font-size: 11px; 
            font-weight: 800; 
            padding: 2px 6px; 
            border-radius: 50%; 
            border: 2px solid #cd1818;
        }

        /* 3. NAVIGATION BAR */
        .nav-bar { 
            background: #b41616; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            height: 50px; 
        }
        
        .menu-wrapper { position: relative; height: 100%; }
        .cat-btn { 
            background: #cd1818; 
            color: white; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 15px; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            width: 270px; 
            height: 100%; 
            padding: 0 20px; 
            transition: background 0.3s;
        }
        .cat-btn:hover { background: #a50e0e; }
        
        .sidebar-menu { 
            position: absolute; 
            top: 100%; 
            left: 0; 
            width: 270px; 
            background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            display: none; 
            border-radius: 0 0 6px 6px; 
            z-index: 1100; 
            padding: 8px 0; 
        }
        .menu-wrapper:hover .sidebar-menu { display: block; }

        .sidebar-item { padding: 0; }
        .sidebar-link { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 10px 20px; 
            color: #444; 
            font-size: 14px; 
            font-weight: 500;
        }
        .sidebar-link:hover { background: #f8f9fa; color: #cd1818; padding-left: 25px; }
        
        /* MEGA MENU UPDATED (Full Width Product) */
        .mega-content { 
            position: absolute; 
            left: 100%; 
            top: 0; 
            width: 950px; 
            min-height: 100%; 
            background: white; 
            box-shadow: 5px 5px 20px rgba(0,0,0,0.1); 
            display: none; 
            z-index: 1200; 
            border-left: 1px solid #eee; 
            padding: 25px; 
            border-radius: 0 0 6px 0;
        }
        .sidebar-item:hover .mega-content { display: block; }

        .hot-product-item { 
            width: 19%; /* Chia 5 cột (100% / 5 = 20% trừ đi gap) */
            padding: 10px; 
            border: 1px solid #eee; 
            border-radius: 8px; 
            text-align: center; 
            transition: 0.3s; 
            background: #fff; 
        }
        .hot-product-item:hover { 
            border-color: #cd1818; 
            transform: translateY(-5px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        .hot-product-item img { height: 120px; object-fit: contain; margin-bottom: 10px; }
        .hot-product-name { 
            font-size: 13px; 
            font-weight: 600; 
            display: -webkit-box; 
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical; 
            overflow: hidden; 
            height: 38px; 
            color: #333; 
            margin-bottom: 5px; 
        }
        .hot-product-price { color: #cd1818; font-weight: 700; font-size: 14px; }

        .quick-links { display: flex; align-items: center; height: 100%; padding-left: 25px; }
        .quick-links a { 
            color: rgba(255,255,255,0.9); 
            font-size: 13px; 
            font-weight: 500; 
            margin-right: 25px; 
            text-transform: uppercase; 
        }
        .quick-links a:hover { color: #fff; text-shadow: 0 0 5px rgba(255,255,255,0.5); }
    </style>
</head>
<body>

<div class="top-promo d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <span class="fw-bold"><i class="fa fa-star text-warning me-1"></i> FPT Shop Clone System - Uy tín, Chất lượng</span>
        <div>
            <a href="#">Giới thiệu</a>
            <a href="#">Trung tâm bảo hành</a>
            <a href="#">Tuyển dụng</a>
        </div>
    </div>
</div>

<header>
    <div class="container py-2">
        <div class="d-flex align-items-center justify-content-between">
            
            <a href="index.php?module=client&controller=home" class="logo me-4"><span>FPT</span>Shop</a>

            <form action="index.php" method="GET" class="search-container d-none d-lg-block">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                <input type="text" name="keyword" class="search-input" placeholder="Bạn muốn tìm gì hôm nay? (iPhone 16, Laptop Gaming...)">
                <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
            </form>

            <div class="header-actions">
                <a href="#" class="action-item d-none d-xl-flex">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Thông tin</span>
                </a>
                
                <a href="index.php?controller=order&action=history" class="action-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Tra cứu</span>
                </a>

                <a href="index.php?controller=cart" class="action-item">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Giỏ hàng</span>
                    <span id="cart-total-count" class="cart-badge" style="<?= $totalQty > 0 ? '' : 'display:none;' ?>">
                        <?= $totalQty > 0 ? $totalQty : 0 ?>
                    </span>
                </a>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
        <a href="index.php?module=admin&controller=dashboard" class="action-item text-warning">
            <i class="fa-solid fa-user-shield"></i>
            <span>Quản trị</span>
        </a>
    <?php endif; ?>

                <?php if($isLoggedIn): ?>
                    <a href="index.php?controller=account&action=profile" class="action-item">
                        <img src="<?= $userAvatar ?>" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-bottom: 2px; border: 1px solid #fff;">
                        <span><?= htmlspecialchars($userName) ?></span>
                    </a>
                <?php else: ?>
                    <a href="index.php?module=client&controller=auth&action=login" class="action-item">
                        <i class="fa-regular fa-user"></i>
                        <span>Đăng nhập</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="nav-bar">
        <div class="container d-flex h-100">
            
            <div class="menu-wrapper">
                <div class="cat-btn">
                    <i class="fa fa-bars me-2" style="font-size: 18px;"></i> DANH MỤC
                </div>

                <div class="sidebar-menu">
                    <ul>
                        <?php foreach($categories as $cate): 
                            $cId = $cate['id'];
                            $cName = $cate['name'];
                            $cSlug = $cate['slug'];
                            
                            // Lấy 5 sản phẩm nổi bật thay vì 3
                            $menuProducts = $prodModel->getProductsByCateForClient($cId);
                            $menuProducts = array_slice($menuProducts, 0, 5); 
                        ?>
                        <li class="sidebar-item">
                            <a href="index.php?controller=category&id=<?= $cId ?>" class="sidebar-link">
                                <span><i class="fa <?= getIconBySlug($cSlug) ?> me-3 text-secondary w-25px text-center"></i><?= htmlspecialchars($cName) ?></span>
                                <i class="fa fa-angle-right text-muted" style="font-size: 12px;"></i>
                            </a>

                            <?php if(!empty($menuProducts)): ?>
                            <div class="mega-content">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3 text-dark text-uppercase border-bottom pb-2">Sản phẩm nổi bật</h6>
                                        <div class="d-flex gap-3">
                                            <?php foreach($menuProducts as $p): ?>
                                            <div class="hot-product-item">
                                                <a href="index.php?controller=product&action=detail&id=<?= $p['id'] ?>">
                                                    <img src="<?= htmlspecialchars($p['thumbnail']) ?>" alt="">
                                                    <div class="hot-product-name"><?= htmlspecialchars($p['name']) ?></div>
                                                    <div class="hot-product-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                                                </a>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="quick-links d-none d-lg-flex">
                <a href="index.php?controller=product&keyword=iPhone"><i class="fab fa-apple me-1" style="font-size: 16px;"></i> iPhone</a>
                <a href="index.php?controller=product&keyword=Samsung">Samsung</a>
                <a href="index.php?controller=product&keyword=Xiaomi">Xiaomi</a>
                <a href="index.php?controller=product&keyword=Oppo">Oppo</a>
                <a href="index.php?controller=category&id=4">Tai nghe</a>
                <a href="#">Máy cũ giá rẻ</a>
            </div>

        </div>
    </div>
</header>