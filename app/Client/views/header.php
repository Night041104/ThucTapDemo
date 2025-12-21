<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($category['name']) ? $category['name'] : 'Cửa hàng' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #f8f9fa; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

        /* HEADER */
        header { background-color: #cd1818; color: white; padding: 10px 0; position: sticky; top:0; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-top { display: flex; align-items: center; justify-content: space-between; gap: 20px; height: 50px; }
        
        .logo { font-size: 24px; font-weight: 800; color: white; display:flex; align-items:center; }
        .logo span { background: white; color: #cd1818; padding: 0 5px; border-radius: 4px; margin-right: 5px; }
        
        .search-box { flex: 1; position: relative; max-width: 500px; }
        .search-box input { width: 100%; height: 38px; padding: 0 15px 0 40px; border-radius: 4px; border: none; outline: none; font-size: 14px; }
        .search-box button { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #555; cursor: pointer; }
        
        .header-actions { display: flex; gap: 20px; align-items: center; font-size: 12px; }
        .action-item { display: flex; flex-direction: column; align-items: center; cursor: pointer; min-width: 60px; text-align: center; }
        .action-item i { font-size: 18px; margin-bottom: 4px; }
        
        .header-bottom { font-size: 12px; color: rgba(255,255,255,0.9); margin-top: 8px; padding-left: 170px; display: flex; gap: 15px;}
        .header-bottom a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <div class="header-top">
            <a href="index.php?module=client&controller=home" class="logo">
                <span>FPT</span>Shop
            </a>

            <div class="search-box">
                <button><i class="fa fa-search"></i></button>
                <input type="text" placeholder="Bạn muốn tìm gì hôm nay?">
            </div>

            <div class="header-actions">
                <div class="action-item"><i class="fa-solid fa-file-invoice"></i><span>Thông tin hay</span></div>
                <div class="action-item"><i class="fa-solid fa-file-invoice-dollar"></i><span>Thanh toán</span></div>
                <div class="action-item"><i class="fa fa-user"></i><span>Tài khoản</span></div>
                <div class="action-item"><i class="fa fa-shopping-cart"></i><span>Giỏ hàng</span></div>
            </div>
        </div>
        <div class="header-bottom container">
            <a href="#">Điện thoại</a>
            <a href="#">Laptop</a>
            <a href="#">Máy tính bảng</a>
            <a href="#">Phụ kiện</a>
        </div>
    </div>
</header>