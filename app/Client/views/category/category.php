<style>
    /* 1. CSS LAYOUT CHUNG */
    .main-content { display: flex; gap: 20px; margin-top: 20px; padding-bottom: 50px; }
    .sidebar { width: 260px; flex-shrink: 0; }
    .product-list { flex: 1; min-height: 500px; position: relative; }

    /* 2. FILTER BOX STYLE (GIỐNG FPT) */
    .filter-box { background: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; border: 1px solid #e5e5e5; }
    .filter-title { font-weight: 700; font-size: 14px; margin-bottom: 12px; display: block; color: #333; }
    
    /* Ẩn checkbox gốc đi */
    .filter-option input[type="checkbox"], 
    .filter-option input[type="radio"] { display: none; }

    /* Style cho Label giả làm nút */
    .filter-option label {
        display: block; cursor: pointer; font-size: 13px; color: #444;
        padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 4px;
        background: #fff; text-align: center; transition: all 0.2s;
        user-select: none;
    }

    /* Hover */
    .filter-option label:hover { border-color: #cd1818; color: #cd1818; }

    /* Khi được chọn (Checked) */
    .filter-option input:checked + label {
        border-color: #cd1818; background: #fff0f0; color: #cd1818; font-weight: 600;
        position: relative;
    }
    /* Thêm dấu tích nhỏ ở góc (Option) */
    .filter-option input:checked + label::after {
        content: '✓'; position: absolute; top: -5px; right: -5px;
        background: #cd1818; color: white; font-size: 9px;
        width: 14px; height: 14px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }

    /* GRID LAYOUT CHO CÁC BỘ LỌC */
    /* Hãng sản xuất: 2 cột */
    .brand-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
    
    /* Thuộc tính text ngắn (RAM, ROM): 3 cột */
    .attr-grid-text { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }

    /* Mức giá: 1 cột (dạng list dọc nhưng style nút) */
    .price-list { display: flex; flex-direction: column; gap: 8px; }
    .price-list .filter-option label { text-align: left; padding-left: 15px; }

    /* 3. LOGIC "XEM THÊM" */
    .collapse-content { 
        max-height: 160px; /* Chiều cao hiển thị mặc định (khoảng 3-4 hàng) */
        overflow: hidden; 
        transition: max-height 0.3s ease;
    }
    .collapse-content.expanded { max-height: 1000px; } /* Mở rộng */
    
    .btn-view-more {
        display: block; width: 100%; text-align: center; margin-top: 10px;
        font-size: 12px; color: #288ad6; cursor: pointer; background: none; border: none;
    }
    .btn-view-more:hover { text-decoration: underline; }

    /* 4. LOADING OVERLAY (AJAX) */
    .loading-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.7); z-index: 10;
        display: none; justify-content: center; padding-top: 100px;
    }
    .spinner {
        width: 40px; height: 40px; border: 4px solid #f3f3f3;
        border-top: 4px solid #cd1818; border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Copy lại Product CSS cũ */
    .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }
    .product-card { background: white; border-radius: 4px; padding: 15px; border: 1px solid transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .prod-img { width: 100%; height: 180px; object-fit: contain; margin-bottom: 10px; }
    .prod-price { color: #cd1818; font-weight: bold; font-size: 16px; }
    .prod-old-price { text-decoration: line-through; color: #999; font-size: 13px; margin-left: 5px; }
    .empty-state { text-align: center; padding: 50px; width: 100%; background: #fff; border-radius: 8px; }
</style>

<div class="container">
    <div class="breadcrumb" style="margin: 15px 0;">
        <a href="index.php">Trang chủ</a> / <strong><?= htmlspecialchars($category['name']) ?></strong>
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

            <div class="filter-box">
                <span class="filter-title">Mức giá</span>
                <div class="price-list">
                    <div class="filter-option">
                        <input type="radio" id="p_all" name="price" value="" checked onchange="filterData()">
                        <label for="p_all">Tất cả</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" id="p_1" name="price" value="0-2000000" onchange="filterData()">
                        <label for="p_1">Dưới 2 triệu</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" id="p_2" name="price" value="2000000-4000000" onchange="filterData()">
                        <label for="p_2">Từ 2 - 4 triệu</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" id="p_3" name="price" value="4000000-7000000" onchange="filterData()">
                        <label for="p_3">Từ 4 - 7 triệu</label>
                    </div>
                    <div class="filter-option">
                        <input type="radio" id="p_4" name="price" value="13000000-max" onchange="filterData()">
                        <label for="p_4">Trên 13 triệu</label>
                    </div>
                </div>
            </div>

            <?php if(!empty($filterAttrs)): ?>
                <?php foreach($filterAttrs as $attr): ?>
                    <?php if(!empty($attr['filter_options'])): ?>
                        <div class="filter-box">
                            <span class="filter-title"><?= $attr['name'] ?></span>
                            <div class="collapse-container">
                                <div class="collapse-content attr-grid-text">
                                    <?php foreach($attr['filter_options'] as $optVal): ?>
                                        <?php $uniqueId = $attr['id'] . '_' . md5($optVal); ?>
                                        <div class="filter-option">
                                            <input type="checkbox" id="attr_<?= $uniqueId ?>" 
                                                   name="attr[<?= $attr['name'] ?>]" 
                                                   value="<?= htmlspecialchars($optVal) ?>" 
                                                   onchange="filterData()">
                                            <label for="attr_<?= $uniqueId ?>">
                                                <?= htmlspecialchars($optVal) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if(count($attr['filter_options']) > 9): ?>
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
    // 1. Hàm xử lý nút "Xem thêm"
    function toggleExpand(btn) {
        const content = btn.previousElementSibling; // Tìm div .collapse-content ngay trước nút
        content.classList.toggle('expanded');
        
        if (content.classList.contains('expanded')) {
            btn.innerText = 'Thu gọn ▴';
        } else {
            btn.innerText = 'Xem thêm ▾';
        }
    }

    // 2. Hàm AJAX Filter chính
    function filterData() {
        const cateId = <?= $cateId ?>; // Lấy ID danh mục từ PHP
        
        // A. Thu thập Brands đã chọn
        const selectedBrands = [];
        document.querySelectorAll('input[name="brand"]:checked').forEach((el) => {
            selectedBrands.push(el.value);
        });

        // B. Thu thập Price
        let selectedPrice = '';
        const priceEl = document.querySelector('input[name="price"]:checked');
        if (priceEl) selectedPrice = priceEl.value;

        // C. Thu thập Thuộc tính (Phức tạp hơn chút)
        // Tạo URL params: &attrs[RAM]=8GB,16GB&attrs[ROM]=128GB
        let attrParams = '';
        // Lấy tất cả tên thuộc tính khác nhau
        const attrNames = new Set();
        document.querySelectorAll('input[name^="attr["]').forEach(el => {
             // Lấy tên từ name="attr[RAM]" -> RAM
             const nameMatch = el.name.match(/attr\[(.*?)\]/);
             if(nameMatch) attrNames.add(nameMatch[1]);
        });

        attrNames.forEach(name => {
            const vals = [];
            document.querySelectorAll(`input[name="attr[${name}]"]:checked`).forEach(el => {
                vals.push(el.value);
            });
            if (vals.length > 0) {
                // Encode URI component để tránh lỗi ký tự đặc biệt
                attrParams += `&attrs[${encodeURIComponent(name)}]=${encodeURIComponent(vals.join(','))}`;
            }
        });

        // D. Hiển thị Loading
        document.getElementById('loading').style.display = 'flex';
        document.getElementById('ajax-result').style.opacity = '0.3';

        // E. Gửi Request AJAX
        // URL đích: index.php?module=client&controller=category&action=filter&...
        const url = `index.php?module=client&controller=category&action=filter&id=${cateId}&brands=${selectedBrands.join(',')}&price=${selectedPrice}${attrParams}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                // F. Cập nhật giao diện
                document.getElementById('ajax-result').innerHTML = html;
                
                // Tắt loading
                document.getElementById('loading').style.display = 'none';
                document.getElementById('ajax-result').style.opacity = '1';
            })
            .catch(err => console.error('Lỗi filter:', err));
    }
</script>