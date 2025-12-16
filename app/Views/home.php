<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Quản Lý (MVC)</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f8; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #333; margin-bottom: 40px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        
        /* Style cho các nút menu */
        .menu-item { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; transition: transform 0.2s, box-shadow 0.2s; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .menu-item span { font-size: 30px; margin-bottom: 10px; display: block; }
        
        /* Màu sắc từng chức năng */
        .bg-blue { background: linear-gradient(135deg, #007bff, #0056b3); }   /* Sinh biến thể */
        .bg-green { background: linear-gradient(135deg, #28a745, #1e7e34); }   /* Tạo sản phẩm cha */
        .bg-orange { background: linear-gradient(135deg, #fd7e14, #d35400); }  /* Danh mục */
        .bg-purple { background: linear-gradient(135deg, #6f42c1, #553093); }  /* Thuộc tính */
        .bg-teal { background: linear-gradient(135deg, #20c997, #17a2b8); }    /* Hãng */

        .section-title { grid-column: span 2; text-align: left; margin-top: 20px; margin-bottom: 10px; color: #666; font-size: 14px; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; }
    </style>
</head>
<body>

    <div class="container">
        <h1>QUẢN LÝ SẢN PHẨM & BIẾN THỂ</h1>
        
        <div class="menu-grid">
            
            <div class="section-title">1. Cấu hình hệ thống (Làm trước)</div>
            
            <a href="index.php?act=attributes" class="menu-item bg-purple">
                <span>🎨</span> Quản Lý Thuộc Tính
            </a>

            <a href="index.php?act=category_list" class="menu-item bg-orange">
                <span>📂</span> Quản Lý Danh Mục
            </a>

            <a href="index.php?act=brand_setup" class="menu-item bg-teal">
                <span>🏢</span> Quản Lý Hãng
            </a>

            <div style="grid-column: span 2;"></div> <div class="section-title">2. Quản lý Sản phẩm (Quy trình chính)</div>

            <a href="index.php?act=create_product" class="menu-item bg-green">
                <span>➕</span> 1. Tạo Sản Phẩm Cha
            </a>

            <a href="index.php?act=generate_variants" class="menu-item bg-blue">
                <span>🚀</span> 2. Sinh Biến Thể (Generator)
            </a>

        </div>
    </div>

</body>
</html>