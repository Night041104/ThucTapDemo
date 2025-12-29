<?php
// --- HELPER: DỊCH & FORMAT DỮ LIỆU ---
// (Đặt ngay đầu file để xử lý hiển thị)

function translateField($field) {
    // Nếu là trường thông số kỹ thuật (bắt đầu bằng "Thông số:") thì giữ nguyên
    if (strpos($field, 'Thông số:') === 0) {
        return $field; 
    }

    $map = [
        'name'         => 'Tên sản phẩm',
        'slug'         => 'Đường dẫn (Slug)',
        'price'        => 'Giá bán',
        'market_price' => 'Giá niêm yết',
        'quantity'     => 'Tồn kho',
        'thumbnail'    => 'Ảnh đại diện',
        'gallery'      => 'Album ảnh',
        'description'  => 'Mô tả chi tiết',
        'status'       => 'Trạng thái',
        'brand_id'     => 'Thương hiệu',
        'category_id'  => 'Danh mục',
        'specs_json'   => 'Thông số kỹ thuật'
    ];
    return $map[$field] ?? $field;
}

function formatValue($field, $val, $maps = []) {
    if ($val === null || $val === '') return '<em style="color:#ccc">(Rỗng)</em>';

    switch ($field) {
        case 'status':
            if ($val == 1) return '<span class="badge-active">Đang bán</span>';
            if ($val == 0) return '<span class="badge-hidden">Tạm ẩn</span>';
            if ($val == -1) return '<span class="badge-stop">Ngừng KD</span>';
            break;
            
        case 'price':
        case 'market_price':
            return number_format((float)$val) . ' ₫';
            
        case 'brand_id':
            // Tra cứu tên thương hiệu từ map được truyền từ Controller
            if (isset($maps['brands'][$val])) {
                return '<strong style="color:#2c3e50">' . htmlspecialchars($maps['brands'][$val]) . '</strong>';
            }
            return "ID: " . $val;

        case 'category_id':
            // Tra cứu tên danh mục
            if (isset($maps['cates'][$val])) {
                return '<strong>' . htmlspecialchars($maps['cates'][$val]) . '</strong>';
            }
            return "ID: " . $val;

        case 'thumbnail':
            return '<img src="'.$val.'" class="mini-thumb">';
            
        default:
            return htmlspecialchars($val);
    }
    return htmlspecialchars($val);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử thay đổi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f0f2f5; color: #333; }
        
        /* 1. CONTAINER CHÍNH: Giới hạn chiều rộng và căn giữa */
        .main-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* HEADER */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { margin: 0; color: #1a237e; font-size: 22px; display: flex; align-items: center; gap: 10px; }
        .btn-back { text-decoration: none; color: #555; background: #fff; padding: 8px 15px; border-radius: 6px; border: 1px solid #ddd; font-weight: 500; transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn-back:hover { background: #e3f2fd; color: #1565c0; border-color: #bbdefb; }

        /* CARD */
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eef0f2; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f8f9fa; border-bottom: 2px solid #e9ecef; }
        th { padding: 15px; text-align: left; font-size: 13px; font-weight: 700; color: #5f6368; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #fafbfc; }

        /* CỘT 1: USER INFO */
        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar-circle { width: 35px; height: 35px; background: #e3f2fd; color: #1565c0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; flex-shrink: 0; }
        .user-details { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: #2c3e50; font-size: 14px; }
        .time-stamp { color: #888; font-size: 12px; margin-top: 2px; }

        /* CỘT 2: BADGES & INFO */
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; letter-spacing: 0.3px; width: fit-content; }
        .badge-shared { background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; }
        .badge-variant { background: #f3e5f5; color: #9c27b0; border: 1px solid #e1bee7; }
        
        .product-meta { margin-top: 8px; }
        .p-name { font-size: 13px; font-weight: 700; color: #333; display: block; }
        .p-sku { font-size: 11px; color: #777; margin-bottom: 4px; display: block; }
        .v-badge { display: inline-block; background: #f3e5f5; color: #7b1fa2; border: 1px solid #e1bee7; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-right: 4px; margin-top: 2px; font-weight: 600; }

        /* CỘT 3: GRID LAYOUT (CÂN ĐỐI) */
        .field-label { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; display: block; }
        
        .diff-container {
            display: grid;
            grid-template-columns: 1fr 24px 1fr; /* Chia cột đều nhau */
            align-items: stretch;
            gap: 10px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .val-box { padding: 10px 15px; font-size: 14px; display: flex; align-items: center; word-break: break-word; }
        
        /* Box Cũ */
        .val-old { background-color: #fff5f5; color: #c62828; border-right: 1px solid #eee; justify-content: flex-end; text-align: right; }
        
        /* Mũi tên ở giữa */
        .arrow-center { background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px; }
        
        /* Box Mới */
        .val-new { background-color: #f0fdf4; color: #1b5e20; border-left: 1px solid #eee; justify-content: flex-start; font-weight: 600; }

        /* HELPER STYLES */
        .badge-active { color: #2e7d32; font-weight:bold; }
        .badge-hidden { color: #f57c00; font-weight:bold; }
        .badge-stop   { color: #c62828; font-weight:bold; }
        .mini-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; background: #fff; }
        
        .empty-state { text-align: center; padding: 60px; color: #999; }
        .empty-state i { font-size: 48px; color: #eee; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="page-header">
        <div style="display:flex; align-items:center; gap:15px;">
            <i class="fa-solid fa-clock-rotate-left" style="font-size: 28px; color:#3f51b5"></i>
            <div>
                <div style="font-size: 13px; text-transform: uppercase; color: #777; font-weight: 700; letter-spacing: 0.5px;">
                    LỊCH SỬ DÒNG SẢN PHẨM (FAMILY LOG)
                </div>
                <h2 style="margin: 2px 0 0 0; color: #1a237e; font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <?= htmlspecialchars($masterProd['name'] ?? 'Sản phẩm') ?>
                    
                    <span style="font-size: 11px; background: #e3f2fd; color: #1565c0; padding: 3px 8px; border-radius: 20px; border: 1px solid #bbdefb; font-weight: 600; vertical-align: middle;">
                        & Các biến thể
                    </span>
                </h2>
            </div>
        </div>

        <a href="index.php?module=admin&controller=product&action=index" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th width="25%">Người sửa / Thời gian</th>
                        <th width="25%">Phạm vi & Đối tượng</th> <th width="50%">Chi tiết thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs)): ?>
                        <?php foreach($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-circle">
                                            <?= strtoupper(substr($log['admin_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?= htmlspecialchars($log['admin_name']) ?></span>
                                            <span class="time-stamp">
                                                <i class="fa-regular fa-calendar" style="font-size:10px; margin-right:3px;"></i>
                                                <?= date('H:i - d/m/Y', strtotime($log['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if($log['action_scope'] == 'SHARED'): ?>
                                        <span class="badge badge-shared">
                                            <i class="fa-solid fa-layer-group"></i> Toàn dòng
                                        </span>
                                    <?php else: ?>
                                        <div style="margin-bottom: 8px;">
                                            <span class="badge badge-variant">
                                                <i class="fa-solid fa-tag"></i> Biến thể
                                            </span>
                                        </div>

                                        <?php if (!empty($log['product_name'])): ?>
                                            <div class="product-meta">
                                                <span class="p-name"><?= htmlspecialchars($log['product_name']) ?></span>
                                                <span class="p-sku">SKU: <?= $log['product_sku'] ?></span>

                                                <?php 
                                                    $specs = json_decode($log['product_specs'], true) ?? [];
                                                    $variantsFound = [];
                                                    // $variantIds được truyền từ Controller
                                                    if (!empty($specs) && !empty($variantIds)) {
                                                        foreach ($specs as $group) {
                                                            if(isset($group['items'])) {
                                                                foreach ($group['items'] as $item) {
                                                                    if (isset($item['attr_id']) && in_array($item['attr_id'], $variantIds) && !empty($item['value'])) {
                                                                        $variantsFound[] = htmlspecialchars($item['name']) . ': ' . htmlspecialchars($item['value']);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                                
                                                <?php if(!empty($variantsFound)): ?>
                                                    <div>
                                                        <?php foreach($variantsFound as $vText): ?>
                                                            <span class="v-badge"><?= $vText ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color:#999; font-style:italic; font-size:12px;">
                                                (Sản phẩm đã xóa - ID: <?= $log['product_id'] ?>)
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="field-label">
                                        <?= translateField($log['field_name']) ?>
                                    </span>
                                    
                                    <div class="diff-container">
                                        <div class="val-box val-old">
                                            <?= formatValue(
                                                $log['field_name'], 
                                                $log['old_value'], 
                                                ['brands' => $brandsMap ?? [], 'cates' => $catesMap ?? []]
                                            ) ?>
                                        </div>
                                        
                                        <div class="arrow-center">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </div>
                                        
                                        <div class="val-box val-new">
                                            <?= formatValue(
                                                $log['field_name'], 
                                                $log['new_value'], 
                                                ['brands' => $brandsMap ?? [], 'cates' => $catesMap ?? []]
                                            ) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="fa-solid fa-box-open"></i>
                                    <p>Chưa có lịch sử thay đổi nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>