<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Quản trị hệ thống</title>
    
    <?php 
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); 
        $baseUrl = $protocol . $domainName . $path;
    ?>
    <base href="<?= $baseUrl ?>">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root { --sidebar-width: 260px; --sidebar-collapsed-width: 70px; --primary-color: #4e73df; --dark-bg: #111827; --dark-secondary: #1f2937; --light-bg: #f3f4f6; --text-gray: #9ca3af; --active-glow: 0 0 15px rgba(56, 189, 248, 0.3); }
        body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); margin: 0; display: flex; min-height: 100vh; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); background-color: var(--dark-bg); color: white; position: fixed; top: 0; left: 0; bottom: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000; display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.2); }
        .sidebar-content { flex: 1; overflow-y: auto; overflow-x: hidden; padding-bottom: 20px; padding-top: 10px; }
        .sidebar-content::-webkit-scrollbar { width: 5px; }
        .sidebar-content::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
        .sidebar-brand { height: 70px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 800; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.05); color: white; text-decoration: none; background: linear-gradient(to right, var(--dark-bg), var(--dark-secondary)); }
        .sidebar-brand span { color: #38bdf8; text-shadow: 0 0 10px rgba(56, 189, 248, 0.5); }
        .sidebar-brand i { display: none; font-size: 1.5rem; color: #38bdf8; }
        .nav-link { color: var(--text-gray); padding: 14px 20px; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s; border-left: 3px solid transparent; margin: 4px 10px; border-radius: 8px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-link-content { display: flex; align-items: center; }
        .nav-link i.icon-main { width: 24px; font-size: 1.1rem; margin-right: 12px; text-align: center; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.08); }
        .nav-link[aria-expanded="true"] { color: white; background: rgba(255,255,255,0.05); }
        .arrow-icon { font-size: 0.8rem; transition: transform 0.3s ease; }
        .nav-link[aria-expanded="true"] .arrow-icon { transform: rotate(90deg); }
        .collapse-inner { background: rgba(0,0,0,0.3); margin: 0 10px; border-radius: 0 0 8px 8px; padding: 5px 0; border-top: none; }
        .collapse-item { color: #9ca3af; padding: 10px 15px 10px 50px; display: flex; align-items: center; text-decoration: none; font-size: 0.9rem; transition: 0.2s; position: relative; }
        .collapse-item:hover { color: white; padding-left: 55px; }
        .collapse-item.active { color: #38bdf8; font-weight: 600; background: rgba(56, 189, 248, 0.1); }
        .collapse-item.active::before { content: ''; position: absolute; left: 25px; top: 50%; width: 6px; height: 6px; background: #38bdf8; border-radius: 50%; transform: translateY(-50%); box-shadow: 0 0 8px #38bdf8; }
        body.sidebar-toggled .sidebar { width: var(--sidebar-collapsed-width); }
        body.sidebar-toggled .sidebar-brand span, body.sidebar-toggled .nav-link span, body.sidebar-toggled .arrow-icon { display: none; }
        body.sidebar-toggled .sidebar-brand i { display: block; }
        body.sidebar-toggled .nav-link { justify-content: center; padding: 15px 0; margin: 5px; }
        body.sidebar-toggled .nav-link i.icon-main { margin-right: 0; font-size: 1.3rem; }
        body.sidebar-toggled .main-wrapper { margin-left: var(--sidebar-collapsed-width); }
        body.sidebar-toggled .collapse { display: none !important; }
        .main-wrapper { flex: 1; margin-left: var(--sidebar-width); display: flex; flex-direction: column; transition: all 0.3s; width: 100%; }
        .topbar { height: 70px; background: white; box-shadow: 0 2px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; padding: 0 25px; position: sticky; top: 0; z-index: 999; }
        .btn-toggle { background: transparent; border: none; font-size: 1.2rem; color: #4b5563; width: 40px; height: 40px; border-radius: 50%; transition: 0.2s; }
        .btn-toggle:hover { background: #f3f4f6; color: var(--primary-color); }
        .admin-profile { display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 5px 10px; border-radius: 30px; transition: 0.2s; }
        .admin-profile:hover { background: #f9fafb; }
        .admin-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
        .content-container { padding: 30px; animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-wrapper { margin-left: 0; } body.sidebar-mobile-open .sidebar { transform: translateX(0); } body.sidebar-toggled .sidebar { width: var(--sidebar-width); transform: translateX(-100%); } }
    </style>
</head>
<body>

    <?php 
        $ctrl = $_GET['controller'] ?? 'dashboard';
        $act  = $_GET['action'] ?? 'index';
    ?>

    <nav class="sidebar" id="sidebar">
        <a href="<?= $baseUrl ?>admin/dashboard" class="sidebar-brand">
            <i class="fa-solid fa-bolt"></i>
            <span>FPT<span style="font-weight:300">ADMIN</span></span>
        </a>

        <div class="sidebar-content">
            
            <?php $isOverview = ($ctrl == 'dashboard'); ?>
            <a class="nav-link <?= $isOverview ? '' : 'collapsed' ?>" 
               data-bs-toggle="collapse" href="#collapseOverview" 
               aria-expanded="<?= $isOverview ? 'true' : 'false' ?>">
                <div class="nav-link-content">
                    <i class="fa-solid fa-gauge-high icon-main"></i>
                    <span>TỔNG QUAN</span>
                </div>
                <i class="fa-solid fa-chevron-right arrow-icon"></i>
            </a>
            <div class="collapse <?= $isOverview ? 'show' : '' ?>" id="collapseOverview">
                <div class="collapse-inner">
                    <a class="collapse-item <?= ($ctrl == 'dashboard') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/dashboard">
                       Dashboard
                    </a>
                </div>
            </div>

            <?php 
                // [QUAN TRỌNG] Đã thêm 'coupon' vào mảng này để menu không bị đóng
                $mgmtControllers = ['category', 'attribute', 'brand', 'product', 'order', 'user', 'coupon'];
                $isMgmt = in_array($ctrl, $mgmtControllers);
            ?>
            <div style="margin-top: 5px;"></div>
            <a class="nav-link <?= $isMgmt ? '' : 'collapsed' ?>" 
               data-bs-toggle="collapse" href="#collapseMgmt" 
               aria-expanded="<?= $isMgmt ? 'true' : 'false' ?>">
                <div class="nav-link-content">
                    <i class="fa-solid fa-layer-group icon-main"></i>
                    <span>QUẢN LÝ</span>
                </div>
                <i class="fa-solid fa-chevron-right arrow-icon"></i>
            </a>
            <div class="collapse <?= $isMgmt ? 'show' : '' ?>" id="collapseMgmt">
                <div class="collapse-inner">
                    <a class="collapse-item <?= ($ctrl == 'category') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/category">
                       <i class="fa-solid fa-folder-tree me-2"></i> Danh mục
                    </a>

                    <a class="collapse-item <?= ($ctrl == 'attribute') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/attribute">
                       <i class="fa-solid fa-sliders me-2"></i> Thuộc tính
                    </a>

                    <a class="collapse-item <?= ($ctrl == 'brand') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/brand">
                       <i class="fa-solid fa-tag me-2"></i> Thương hiệu
                    </a>

                    <a class="collapse-item <?= ($ctrl == 'product') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/product">
                       <i class="fa-solid fa-box-open me-2"></i> Sản phẩm
                    </a>
                    
                    <a class="collapse-item <?= ($ctrl == 'order') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/order">
                       <i class="fa-solid fa-file-invoice-dollar me-2"></i> Đơn hàng
                    </a>
                    
                    <a class="collapse-item <?= ($ctrl == 'user') ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?>admin/user">
                       <i class="fa-solid fa-users me-2"></i> Thành viên
                    </a>
                    
                    <a class="collapse-item <?= ($ctrl == 'coupon') ? 'active' : '' ?>" 
                        href="<?= $baseUrl ?>admin/coupon">
                        <i class="fa-solid fa-ticket me-2"></i> Mã giảm giá
                    </a>
                </div>
            </div>

            <?php 
                $sysControllers = ['setting', 'banner']; 
                $isSys = in_array($ctrl, $sysControllers);
            ?>
            <div style="margin-top: 5px;"></div>
            <a class="nav-link <?= $isSys ? '' : 'collapsed' ?>" 
               data-bs-toggle="collapse" href="#collapseSystem" 
               aria-expanded="<?= $isSys ? 'true' : 'false' ?>">
                <div class="nav-link-content">
                    <i class="fa-solid fa-gears icon-main"></i>
                    <span>HỆ THỐNG</span>
                </div>
                <i class="fa-solid fa-chevron-right arrow-icon"></i>
            </a>
            <div class="collapse <?= $isSys ? 'show' : '' ?>" id="collapseSystem">
                <div class="collapse-inner">
                    <a class="collapse-item" href="<?= $baseUrl ?>trang-chu">
                        <i class="fa-solid fa-globe me-2"></i> Xem Website
                    </a>
                    <a class="collapse-item text-danger" href="<?= $baseUrl ?>dang-xuat">
                        <i class="fa-solid fa-power-off me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>

        </div>
    </nav>

    <div class="main-wrapper">
        
        <header class="topbar">
            <button class="btn-toggle" id="sidebarToggle">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>

            <?php 
                $admin = $_SESSION['user'] ?? ['lname'=>'Admin', 'fname'=>'System', 'avatar'=>''];
                $adminAvt = !empty($admin['avatar']) ? $admin['avatar'] : 'public/uploads/default/default_avt.png';
            ?>
            <div class="dropdown">
                <div class="admin-profile" data-bs-toggle="dropdown">
                    <div class="text-end me-1 d-none d-sm-block">
                        <div style="font-weight: 600; font-size: 0.9rem; color:#374151;">
                            <?= htmlspecialchars($admin['lname'] . ' ' . $admin['fname']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: #9ca3af;">Administrator</div>
                    </div>
                    <img src="<?= htmlspecialchars($adminAvt) ?>" class="admin-avatar" onerror="this.src='public/uploads/default/default_avt.png'">
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 animate__animated animate__fadeIn">
                    <li><a class="dropdown-item py-2 text-danger" href="<?= $baseUrl ?>dang-xuat"><i class="fa fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                </ul>
            </div>
        </header>

        <div class="content-container">

<script>
document.addEventListener("DOMContentLoaded", function() {
    const currentUrl = window.location.href;
    const sidebarLinks = document.querySelectorAll('.collapse-item');
    
    sidebarLinks.forEach(link => {
        // Kiểm tra nếu link này trùng với URL hiện tại
        if (link.href === currentUrl || currentUrl.startsWith(link.href)) {
            
            // 1. Active link con
            link.classList.add('active');
            
            // 2. Mở menu cha
            const parentCollapse = link.closest('.collapse');
            if (parentCollapse) {
                parentCollapse.classList.add('show');
                
                // 3. Highlight tab cha (QUẢN LÝ / HỆ THỐNG)
                const parentToggle = document.querySelector(`a[href="#${parentCollapse.id}"]`);
                if (parentToggle) {
                    parentToggle.classList.remove('collapsed');
                    parentToggle.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
});
</script>