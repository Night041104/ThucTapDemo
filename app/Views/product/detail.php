<?php
// Helper function để tìm ID biến thể dựa trên Matrix (Giữ logic PHP trong View để render link)
function findVariantId($matrix, $currentAttrs, $clickAttrId, $clickOptionId) {
    $desiredAttrs = $currentAttrs;
    $desiredAttrs[$clickAttrId] = $clickOptionId;
    
    // 1. Tìm khớp chính xác 100% các thuộc tính
    foreach ($matrix as $pid => $attrs) {
        $match = true;
        foreach ($desiredAttrs as $k => $v) {
            if (!isset($attrs[$k]) || $attrs[$k] != $v) { $match = false; break; }
        }
        if ($match) return $pid;
    }
    
    // 2. Tìm tương đối (Sản phẩm nào có chứa Option vừa click)
    foreach ($matrix as $pid => $attrs) {
        if (isset($attrs[$clickAttrId]) && $attrs[$clickAttrId] == $clickOptionId) return $pid;
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #f4f6f8; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; display: flex; overflow: hidden; min-height: 500px; padding: 20px; gap: 30px; }
        .left-col { width: 40%; }
        .right-col { width: 60%; }
        
        .main-img { width: 100%; height: 400px; object-fit: contain; border: 1px solid #eee; border-radius: 8px; }
        .thumb-list { display: flex; gap: 10px; margin-top: 10px; justify-content: center; }
        .thumb-img { width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; object-fit: cover; }
        .thumb-img:hover { border-color: #cb1c22; }

        h1 { margin: 0 0 10px 0; font-size: 24px; color: #333; }
        .price { font-size: 28px; color: #cb1c22; font-weight: bold; margin-bottom: 20px; }
        
        .variant-group { margin-bottom: 20px; }
        .variant-label { font-weight: bold; font-size: 14px; margin-bottom: 8px; display: block; }
        .variant-options { display: flex; flex-wrap: wrap; gap: 10px; }
        
        .btn-variant { text-decoration: none; padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; color: #333; font-size: 14px; background: #fff; position: relative; }
        .btn-variant:hover { border-color: #cb1c22; color: #cb1c22; }
        .btn-variant.active { border-color: #cb1c22; color: #cb1c22; background: #fff0f0; font-weight: bold; }
        .btn-variant.active::after { content: '✓'; position: absolute; top: -5px; right: -5px; background: #cb1c22; color: white; font-size: 10px; width: 15px; height: 15px; border-radius: 50%; text-align: center; line-height: 15px; }

        .specs-section { max-width: 1200px; margin: 20px auto; background: white; padding: 30px; border-radius: 8px; }
        .specs-table { width: 100%; border-collapse: collapse; }
        .specs-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .group-row { background: #f9f9f9; font-weight: bold; text-transform: uppercase; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <a href="index.php">← Về Dashboard</a>
    <br><br>

    <div class="container">
        <div class="left-col">
            <img src="<?= $images[0] ?>" id="mainImage" class="main-img">
            <div class="thumb-list">
                <?php foreach($images as $img): ?>
                    <img src="<?= $img ?>" class="thumb-img" onclick="document.getElementById('mainImage').src=this.src">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="right-col">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div class="price"><?= number_format($product['price'], 0, ',', '.') ?>₫</div>

            <?php if (!empty($variantsInfo)): ?>
                <?php foreach($variantsInfo as $attrId => $info): ?>
                    <div class="variant-group">
                        <span class="variant-label">Chọn <?= $info['name'] ?>:</span>
                        <div class="variant-options">
                            <?php foreach($info['options'] as $optId => $optLabel): ?>
                                <?php 
                                    $isActive = (isset($currentAttrs[$attrId]) && $currentAttrs[$attrId] == $optId);
                                    // Gọi hàm helper ở trên đầu file
                                    $targetId = findVariantId($matrix, $currentAttrs, $attrId, $optId);
                                ?>
                                
                                <?php if($targetId): ?>
                                    <a href="index.php?act=product_detail&id=<?= $targetId ?>" class="btn-variant <?= $isActive ? 'active' : '' ?>">
                                        <?= htmlspecialchars($optLabel) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="btn-variant" style="opacity:0.4; cursor:not-allowed; background:#eee;">
                                        <?= htmlspecialchars($optLabel) ?>
                                    </span>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <button style="background:#cb1c22; color:white; border:none; width:100%; padding:15px; font-size:18px; font-weight:bold; border-radius:4px; cursor:pointer; margin-top:20px">MUA NGAY</button>
        </div>
    </div>

    <div class="specs-section">
        <h3 style="border-left:4px solid #cb1c22; padding-left:10px;">THÔNG SỐ KỸ THUẬT</h3>
        <?php if($specs): ?>
            <table class="specs-table">
                <?php foreach($specs as $group): ?>
                    <tr><td colspan="2" class="group-row"><?= $group['group_name'] ?></td></tr>
                    <?php foreach($group['items'] as $item): ?>
                        <tr>
                            <td width="30%" style="color:#666"><?= $item['name'] ?></td>
                            <td><?= $item['value'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Chưa có thông số kỹ thuật.</p>
        <?php endif; ?>
    </div>
</body>
</html>