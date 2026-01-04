<?php
// BẢNG MÀU CỐ ĐỊNH
$fixedColorMap = [
    'Tất cả'   => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
    'Trắng'    => '#ffffff',
    'Đen'      => '#000000',
    'Xám'      => '#6b7280',
    'Bạc'      => '#c0c0c0',
    'Đỏ'       => '#dc2626',
    'Hồng'     => '#f9a8d4',
    'Cam'      => '#f97316',
    'Vàng'     => '#facc15',
    'Xanh lá'  => '#4ade80',
    'Xanh'     => '#60a5fa', 
    'Tím'      => '#c084fc',
    'Nâu'      => '#78350f',
    'Kem'      => '#fef3c7',
    'Xanh dương' => '#2563eb',
    'Vàng đồng' => '#c5a059',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category['name']) ?></title>
    <style>
        /* 1. LAYOUT CHUNG */
        .main-content { display: flex; gap: 20px; margin-top: 20px; padding-bottom: 50px; }
        .sidebar { 
            width: 260px; 
            flex-shrink: 0;
            
            /* Giúp sidebar dính chặt khi cuộn trang */
            position: -webkit-sticky; /* Cho Safari */
            position: sticky;
            top: 20px; /* Cách mép trên màn hình 20px */
            
            /* Thiết lập chiều cao tối đa bằng chiều cao màn hình */
            height: calc(100vh - 40px); 
            
            /* Nếu nội dung dài hơn chiều cao màn hình -> Hiện thanh cuộn riêng */
            overflow-y: auto; 
            
            /* Tinh chỉnh nhỏ để thanh cuộn không đè nội dung */
            padding-right: 5px; 
        }

        /* [THÊM MỚI] Làm đẹp thanh cuộn cho Sidebar (Chrome/Safari/Edge) */
        .sidebar::-webkit-scrollbar {
            width: 6px; /* Thanh cuộn nhỏ gọn */
        }
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #ccc; 
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #aaa; /* Đậm hơn khi di chuột vào */
        }
        .product-list { flex: 1; min-height: 500px; position: relative; }

        /* 2. FILTER BOX STYLE */
        .filter-box { background: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; border: 1px solid #e5e5e5; }
        .filter-title { font-weight: 700; font-size: 14px; margin-bottom: 12px; display: block; color: #333; }
        
        /* ẨN INPUT TUYỆT ĐỐI */
        .sidebar input[type="checkbox"], 
        .sidebar input[type="radio"] {
            position: absolute !important; width: 0 !important; height: 0 !important;
            opacity: 0 !important; overflow: hidden !important; margin: 0 !important; padding: 0 !important; z-index: -1;
        }

        /* 3. STYLE CHO NÚT CHỮ (RAM, ROM...) - [ĐÃ SỬA LỖI BẤT ĐỐI XỨNG] */
        .filter-option { 
            margin-bottom: 0; 
            height: 100%; /* Chiếm hết chiều cao grid cell */
        }
        
        .filter-option label {
            cursor: pointer; 
            font-size: 12px; 
            color: #444;
            padding: 5px 8px; 
            border: 1px solid #e0e0e0; 
            border-radius: 4px;
            background: #fff; 
            transition: all 0.2s;
            user-select: none; 
            position: relative;
            
            /* Flexbox để căn giữa nội dung bất kể dài ngắn */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            height: 100%;       
            min-height: 40px;   /* Chiều cao tối thiểu đồng bộ */
            line-height: 1.2;
        }

        .filter-option label:hover { border-color: #cd1818; color: #cd1818; }
        
        .filter-option input:checked + label {
            border-color: #cd1818; background: #fff0f0; color: #cd1818; font-weight: 600;
        }
        .filter-option input:checked + label::after {
            content: '✓'; position: absolute; top: -5px; right: -5px;
            background: #cd1818; color: white; font-size: 8px;
            width: 14px; height: 14px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #fff;
        }

        /* 4. STYLE CHO GRID MÀU SẮC */
        .color-grid-layout {
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 10px 5px; 
            justify-items: center;
            padding-bottom: 5px;
        }

        .color-item {
            display: flex; flex-direction: column; align-items: center;
            cursor: pointer; width: 100%; position: relative;
            margin: 0; padding: 0; border: none; background: none;
        }

        .color-circle {
            width: 34px; height: 34px; border-radius: 50%;
            margin-bottom: 5px; position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        .color-circle[style*="#ffffff"] { border: 1px solid #ddd; }

        .color-item:hover .color-circle { transform: translateY(-3px); }

        .color-item input:checked + .color-circle {
            box-shadow: 0 0 0 2px white, 0 0 0 4px #2f80ed; 
            border-color: transparent;
        }
        
        .color-label {
            font-size: 10px; color: #666; text-align: center;
            width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .color-item input:checked ~ .color-label { color: #2f80ed; font-weight: 700; }

        /* 5. CÁC GRID KHÁC */
        .brand-grid { 
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; 
        }
        
        /* Grid cho chữ: Tự động căng đều chiều cao */
        .attr-grid-text { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
            align-items: stretch; 
        }

        /* List giá: Căn trái, không center */
        .price-list { display: flex; flex-direction: column; gap: 8px; }
        .price-list .filter-option label { 
            justify-content: flex-start; padding-left: 15px; min-height: 36px; 
        }

        /* 6. LOGIC XEM THÊM - [ĐÃ SỬA LOGIC] */
        .collapse-container { position: relative; }
        
        /* 6. LOGIC XEM THÊM - [FIX LẦN 2] */
       /* 6. LOGIC XEM THÊM - [FIX HOÀN CHỈNH] */
        .collapse-content { 
            /* Chiều cao 3 hàng (~135px) + Padding đệm (~30px) = 165px.
               Đặt 165px để hiển thị trọn vẹn 3 hàng và lớp mờ.
            */
            max-height: 165px; 
            
            /* [QUAN TRỌNG] Tạo khoảng trống ở đáy để chứa lớp mờ, tránh đè lên chữ */
            padding-bottom: 30px; 
            
            overflow: hidden; 
            transition: max-height 0.4s ease-in-out, padding 0.4s; 
            position: relative;
        }
        
        /* Khi mở rộng thì xóa khoảng đệm đi cho đẹp */
        .collapse-content.expanded { 
            max-height: 1000px; 
            padding-bottom: 0; 
        }

        /* Hiệu ứng mờ đáy */
        .collapse-content:not(.expanded)::after {
            content: ''; 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            
            /* Chiều cao bằng đúng padding-bottom để lấp đầy khoảng trống */
            height: 30px; 
            
            /* Gradient trắng mờ */
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1) 80%);
            pointer-events: none;
        }
        
        /* Ẩn lớp mờ khi đã mở rộng */
        .collapse-content.expanded::after {
            display: none;
        }

        .btn-view-more {
            display: block; width: 100%; text-align: center; margin-top: 5px;
            font-size: 12px; color: #288ad6; cursor: pointer; 
            background: #f8f9fa; border: 1px solid #eee; padding: 6px 0; border-radius: 4px;
        }
        .btn-view-more:hover { background: #f0f0f0; text-decoration: none; color: #cd1818; }

        /* 7. LOADING & PRODUCT */
        .loading-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.7); z-index: 10; display: none; justify-content: center; padding-top: 100px; }
        .spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #cd1818; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* === CSS SẢN PHẨM === */
        .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

        .product-card { 
            background: white; border-radius: 10px; padding: 15px; 
            border: 1px solid #f0f0f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            text-decoration: none; color: inherit; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative; overflow: hidden; display: flex; flex-direction: column;
        }
        .product-card:hover { border-color: #fff; box-shadow: 0 12px 20px rgba(0,0,0,0.1); transform: translateY(-5px); z-index: 2; }

        .prod-img-box { width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; transition: transform 0.5s ease; }
        .prod-img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease; }
        .product-card:hover .prod-img { transform: scale(1.05); }

        .discount-badge {
            position: absolute; top: 10px; left: -4px; background: #cd1818; color: white;
            font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 0 4px 4px 0;
            box-shadow: 2px 2px 4px rgba(0,0,0,0.2); z-index: 5;
        }
        .discount-badge::before {
            content: ''; position: absolute; bottom: -4px; left: 0;
            border-top: 4px solid #8a0e0e; border-left: 4px solid transparent;
        }

        .prod-info { flex: 1; display: flex; flex-direction: column; }
        .prod-brand-tag { font-size: 10px; color: #666; text-transform: uppercase; font-weight: 600; margin-bottom: 5px; letter-spacing: 0.5px; }
        .prod-name { 
            font-size: 14px; font-weight: 600; line-height: 1.4; margin: 0 0 10px 0; 
            height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            color: #333; transition: color 0.2s;
        }
        .product-card:hover .prod-name { color: #2f80ed; }

        .prod-price-box { margin-top: auto; }
        .prod-price { color: #cd1818; font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; }
        .prod-old-price { text-decoration: line-through; color: #999; font-size: 13px; font-weight: 400; }

        .empty-state { text-align: center; padding: 50px; width: 100%; background: #fff; border-radius: 8px; border: 1px solid #eee; }
    </style>
</head>
<body>

<div class="container">
    <div class="breadcrumb" style="margin: 15px 0;">
        <a href="index.php" style="text-decoration:none; color:#666;">Trang chủ</a> / <strong><?= htmlspecialchars($category['name']) ?></strong>
    </div>

    <div class="main-content">
        <aside class="sidebar">
            
            <?php if(!empty($filterBrands)): ?>
            <div class="filter-box">
                <span class="filter-title">Hãng sản xuất</span>
                <div class="collapse-container">
                    <div class="collapse-content brand-grid">
                        <?php foreach($filterBrands as $b): ?>
                            <div class="filter-option">
                                <input type="checkbox" id="brand_<?= $b['id'] ?>" name="brand" value="<?= $b['id'] ?>" onchange="filterData()">
                                <label for="brand_<?= $b['id'] ?>">
                                    <?php if(!empty($b['logo_url'])): ?>
                                        <img src="<?= $b['logo_url'] ?>" alt="<?= $b['name'] ?>" style="height:15px; object-fit:contain;">
                                    <?php else: ?>
                                        <?= $b['name'] ?>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if(count($filterBrands) > 8): ?>
                        <button class="btn-view-more" onclick="toggleExpand(this)">Xem thêm ▾</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($priceRanges)): ?>
            <div class="filter-box">
                <span class="filter-title">Mức giá</span>
                <div class="price-list">
                    <div class="filter-option">
                        <input type="radio" id="p_all" name="price" value="" checked onchange="filterData()">
                        <label for="p_all">Tất cả</label>
                    </div>
                    <?php foreach($priceRanges as $val => $label): ?>
                        <div class="filter-option">
                            <input type="radio" id="p_<?= $val ?>" name="price" value="<?= $val ?>" onchange="filterData()">
                            <label for="p_<?= $val ?>"><?= htmlspecialchars($label) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($filterAttrs)): ?>
                <?php foreach($filterAttrs as $attr): ?>
                    <?php if(!empty($attr['filter_options'])): ?>
                        
                         <?php 
                            $attrNameLower = mb_strtolower($attr['name']); 
                            $isColorAttr = (mb_strpos($attrNameLower, 'màu') !== false) || (mb_strpos($attrNameLower, 'color') !== false); 
                        ?>

                        <div class="filter-box">
                            <span class="filter-title"><?= htmlspecialchars($attr['name']) ?></span>
                            
                            <div class="collapse-container">
                                
                                <?php if ($isColorAttr): ?>
                                    <div class="collapse-content color-grid-layout">
                                        <label class="color-item">
                                            <input type="radio" name="attr[<?= $attr['name'] ?>]" value="" checked onchange="filterData()">
                                            <div class="color-circle" style="background: <?= $fixedColorMap['Tất cả'] ?>;"></div>
                                            <span class="color-label">Tất cả</span>
                                        </label>

                                        <?php foreach($attr['filter_options'] as $optVal): ?>
                                            <?php $bgStyle = isset($fixedColorMap[$optVal]) ? $fixedColorMap[$optVal] : '#eee'; ?>
                                            <label class="color-item" title="<?= htmlspecialchars($optVal) ?>">
                                                <input type="radio" name="attr[<?= $attr['name'] ?>]" value="<?= htmlspecialchars($optVal) ?>" onchange="filterData()">
                                                <div class="color-circle" style="background: <?= $bgStyle ?>;"></div>
                                                <span class="color-label"><?= htmlspecialchars($optVal) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>

                                <?php else: ?>
                                    <div class="collapse-content attr-grid-text">
                                        <?php foreach($attr['filter_options'] as $optVal): ?>
                                            <?php $uniqueId = $attr['id'] . '_' . md5($optVal); ?>
                                            <div class="filter-option">
                                                <input type="checkbox" id="attr_<?= $uniqueId ?>" name="attr[<?= $attr['name'] ?>][]" value="<?= htmlspecialchars($optVal) ?>" onchange="filterData()">
                                                <label for="attr_<?= $uniqueId ?>">
                                                    <?= htmlspecialchars($optVal) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php 
                                    // Với màu sắc: > 12 item thì hiện
                                    // Với chữ: > 9 item thì hiện (khoảng 3 hàng)
                                    $limit = $isColorAttr ? 12 : 9;
                                    if(count($attr['filter_options']) > $limit): 
                                ?>
                                    <button class="btn-view-more" onclick="toggleExpand(this)">Xem thêm ▾</button>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <section class="product-list" id="product-container">
            <div class="loading-overlay" id="loading">
                <div class="spinner"></div>
            </div>
            
            <div id="ajax-result">
                <?php require __DIR__ . '/product_list.php'; ?>
            </div>
        </section>
    </div>
</div>

<script>
    function toggleExpand(btn) {
        const content = btn.previousElementSibling;
        content.classList.toggle('expanded');
        
        if (content.classList.contains('expanded')) {
            btn.innerText = 'Thu gọn ▴';
        } else {
            btn.innerText = 'Xem thêm ▾';
        }
    }

    function filterData() {
        const cateId = <?= $cateId ?>;
        
        const selectedBrands = [];
        document.querySelectorAll('input[name="brand"]:checked').forEach((el) => {
            selectedBrands.push(el.value);
        });

        let selectedPrice = '';
        const priceEl = document.querySelector('input[name="price"]:checked');
        if (priceEl) selectedPrice = priceEl.value;

        let attrParams = '';
        const attrNames = new Set();
        document.querySelectorAll('input[name^="attr["]').forEach(el => {
             const nameMatch = el.name.match(/attr\[(.*?)\]/);
             if(nameMatch) attrNames.add(nameMatch[1]);
        });

        attrNames.forEach(name => {
            const vals = [];
            document.querySelectorAll(`input[name^="attr[${name}]"]:checked`).forEach(el => {
                if(el.value !== "") { 
                    vals.push(el.value);
                }
            });
            
            if (vals.length > 0) {
                attrParams += `&attrs[${encodeURIComponent(name)}]=${encodeURIComponent(vals.join(','))}`;
            }
        });

        document.getElementById('loading').style.display = 'flex';
        document.getElementById('ajax-result').style.opacity = '0.3';

        const url = `index.php?module=client&controller=category&action=filter&id=${cateId}&brands=${selectedBrands.join(',')}&price=${selectedPrice}${attrParams}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('ajax-result').innerHTML = html;
                document.getElementById('loading').style.display = 'none';
                document.getElementById('ajax-result').style.opacity = '1';
            })
            .catch(err => console.error('Lỗi filter:', err));
    }
</script>

</body>
</html>