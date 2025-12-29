<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm (Master-Slave)</title>
    <style>
        /* GIỮ NGUYÊN CSS CŨ CỦA BẠN */
        body{font-family:'Segoe UI', sans-serif; padding:20px; background:#f4f6f8; color:#333;}
        .table-box{background:white; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05);}
        table{width:100%; border-collapse:collapse; margin-top:15px;}
        th,td{padding:12px 10px; border-bottom:1px solid #eee; text-align:left; vertical-align: middle;}
        th{background:#e3f2fd; color:#0d47a1; font-weight:600;}
        tr:hover {background-color: #f9f9f9;}
        
        .is-child{background-color: #fafafa;}
        .is-child .name-cell{padding-left: 40px; position:relative;}
        .is-child .name-cell:before{content:'↳'; position:absolute; left:15px; font-weight:bold; color:#ff9800; font-size:18px;}
        
        .badge-master{background:#2e7d32; color:white; padding:3px 8px; border-radius:4px; font-size:10px; font-weight:bold; text-transform: uppercase;}
        
        /* Button Styles */
        .btn{text-decoration:none; padding:6px 12px; border-radius:4px; font-size:13px; display:inline-block; margin-right:5px; font-weight:500; border:none; cursor:pointer;}
        
        .btn-clone{background:#fff3e0; color:#ef6c00; border:1px solid #ffe0b2;} .btn-clone:hover{background:#ffe0b2;}
        .btn-edit{background:#e3f2fd; color:#1565c0; border:1px solid #bbdefb;} .btn-edit:hover{background:#bbdefb;}
        .btn-del{background:#ffebee; color:#c62828; border:1px solid #ffcdd2;} .btn-del:hover{background:#ffcdd2;}
        .btn-create{background:#2e7d32; color:white; padding:10px 20px; font-size:14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);}

        /* [MỚI] CSS CHO NÚT LỊCH SỬ (MÀU TÍM) */
        .btn-history { background: #f3e5f5; color: #7b1fa2; border: 1px solid #e1bee7; } 
        .btn-history:hover { background: #e1bee7; color: #4a148c; }

        /* Status Badges */
        .st-active { color: #2e7d32; font-weight: bold; background: #e8f5e9; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
        .st-hidden { color: #616161; background: #eeeeee; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
        .st-stop   { color: #fff; background: #424242; padding: 2px 6px; border-radius: 4px; font-size: 12px; }

        .filter-box { background:#f1f8e9; padding:15px; border-radius:5px; display:flex; gap:10px; align-items:center; border:1px solid #c8e6c9; flex-wrap: wrap; }
        .input-search { padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 250px; }
        .input-select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 250px; }

        .badge-variant { 
            display: inline-block; 
            background: #f3e5f5; 
            color: #7b1fa2; 
            border: 1px solid #e1bee7; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-size: 11px; 
            margin-right: 4px; 
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="color:#1565c0; margin:0;">📦 KHO HÀNG TỔNG HỢP</h1>
        <a href="index.php?module=admin&controller=product&action=create" class="btn btn-create">+ Tạo Sản Phẩm Mới</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom:20px; border-radius:4px;">
            <?php 
                if ($_GET['msg'] == 'created') echo "✅ Tạo sản phẩm mới thành công!";
                elseif ($_GET['msg'] == 'updated') echo "✅ Cập nhật sản phẩm thành công!";
                elseif ($_GET['msg'] == 'cloned') echo "📋 Nhân bản thành công!";
                elseif ($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa sản phẩm.";
            ?>
        </div>
    <?php endif; ?>

    <div class="table-box">
        <form method="GET" action="index.php" class="filter-box">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="index">

            <input type="text" name="q" value="<?= htmlspecialchars($keyword ?? '') ?>" class="input-search" placeholder="Tìm tên hoặc SKU...">
            
            <select name="master_id" class="input-select" onchange="this.form.submit()">
                <option value="0">-- Tất cả dòng sản phẩm --</option>
                <?php foreach($masters as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= (isset($filterMasterId) && $filterMasterId == $m['id']) ? 'selected' : '' ?>>
                        <?= $m['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn" style="background:#2196f3; color:white;">Lọc</button>
            <?php if(!empty($filterMasterId) || !empty($keyword)): ?>
                <a href="index.php?module=admin&controller=product&action=index" style="color:#c62828; text-decoration:none; font-weight:bold; margin-left:10px;">✕ Bỏ lọc</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th width="60">Ảnh</th>
                    <th width="250">Tên Sản Phẩm</th>
                    <th width="150">Biến thể</th> <th width="120">Thông tin</th>
                    <th width="100">Giá bán</th>
                    <th width="60">Kho</th>
                    <th width="100">Trạng thái</th>
                    <th width="240">Hành động</th> </tr>
            </thead>
            <tbody>
                <?php if(!empty($products)): ?>
                    <?php foreach($products as $row): ?>
                        <?php 
                            $isChild = ($row['parent_id'] > 0); 
                            $roleClass = $isChild ? 'is-child' : '';
                            
                            // Xử lý hiển thị biến thể từ JSON
                            $specs = json_decode($row['specs_json'], true) ?? [];
                            $variantHtml = '';
                            
                            // Duyệt qua JSON specs để tìm thuộc tính biến thể
                            if (!empty($specs) && !empty($variantIds)) {
                                foreach ($specs as $group) {
                                    if(isset($group['items'])) {
                                        foreach ($group['items'] as $item) {
                                            // Kiểm tra nếu thuộc tính này là biến thể và có giá trị
                                            if (isset($item['attr_id']) && in_array($item['attr_id'], $variantIds) && !empty($item['value'])) {
                                                $variantHtml .= '<span class="badge-variant">' . htmlspecialchars($item['name']) . ': ' . htmlspecialchars($item['value']) . '</span>';
                                            }
                                        }
                                    }
                                }
                            }
                        ?>
                        <tr class="<?= $roleClass ?>">
                            <td>
                                <img src="<?= $row['thumbnail'] ?>" style="width:50px; height:50px; object-fit:contain; border:1px solid #ddd; background:#fff; padding:2px; border-radius: 4px;">
                            </td>
                            
                            <td class="name-cell">
                                <div>
                                    <a href="index.php?module=admin&controller=product&action=edit&id=<?= $row['id'] ?>" style="color:#333; font-weight:bold; text-decoration:none; font-size:14px;">
                                        <?= $row['name'] ?>
                                    </a>
                                </div>
                                <div style="font-size:11px; color:#999; margin-top:3px;">
                                    SKU: <?= $row['sku'] ?>
                                </div>
                                <div style="margin-top: 5px;">
                                    <?php if(!$isChild): ?>
                                        <span class="badge-master">Master / Gốc</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td>
                                <?= $variantHtml ? $variantHtml : '<span style="color:#ccc; font-size:11px;">--</span>' ?>
                            </td>

                            <td style="font-size: 13px; color: #555;">
                                <div>📂 <?= $row['cate_name'] ?></div>
                                <div>🏷️ <?= $row['brand_name'] ?></div>
                            </td>

                            <td style="color:#d32f2f; font-weight:bold;">
                                <?= number_format($row['price']) ?>₫
                            </td>
                            
                            <td>
                                <?php if($row['quantity'] > 0): ?>
                                    <span style="color:#2e7d32; font-weight:bold;"><?= $row['quantity'] ?></span>
                                <?php else: ?>
                                    <span style="color:#c62828; background:#ffebee; padding:2px 5px; border-radius:4px; font-size:11px;">Hết</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    if($row['status'] == 1) echo '<span class="st-active">Đang bán</span>';
                                    elseif($row['status'] == 0) echo '<span class="st-hidden">Tạm ẩn</span>';
                                    elseif($row['status'] == -1) echo '<span class="st-stop">Ngừng KD</span>';
                                ?>
                            </td>

                            <td>
                                <?php 
                                    // Nếu là con -> Xem lịch sử của Cha
                                    // Nếu là cha -> Xem lịch sử của chính nó
                                    $historyId = ($row['parent_id'] > 0) ? $row['parent_id'] : $row['id'];
                                ?>

                                <a href="index.php?module=admin&controller=product&action=history&master_id=<?= $historyId ?>" 
                                   class="btn btn-history" 
                                   title="Xem lịch sử thay đổi">
                                    🕒 Log
                                </a>

                                <a href="index.php?module=admin&controller=product&action=clone&id=<?= $row['id'] ?>" class="btn btn-clone" title="Nhân bản sản phẩm này">
                                    ❐ Clone
                                </a>
                                <a href="index.php?module=admin&controller=product&action=edit&id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                                <a href="index.php?module=admin&controller=product&action=delete&id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('⚠️ CẢNH BÁO XÓA:\n\n- Nếu xóa MASTER, sản phẩm con kế tiếp sẽ được tự động đưa lên làm Master.\n- Hành động này không thể hoàn tác.\n\nBạn có chắc chắn muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px; color: #777;">Không tìm thấy sản phẩm nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>