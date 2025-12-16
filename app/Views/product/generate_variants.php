<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sinh Biến Thể</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; background: #f8f9fa; }
        .box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .row { display: flex; align-items: center; gap: 10px; padding: 5px 0; }
        input[type=number], input[type=text] { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { background: #28a745; color: white; padding: 15px; width: 100%; border: none; font-size: 16px; font-weight: bold; cursor: pointer; }
        .alert { padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>⚙️ SINH BIẾN THỂ</h1>
    <a href="index.php">← Về trang chủ</a>
    <br><br>

    <?php if(!empty($msg)) echo "<div class='alert'>$msg</div>"; ?>

    <form method="POST" action="index.php?act=store_variants">
        <div class="box">
            <label><b>Chọn Sản phẩm Cha:</b></label>
            <select name="parent_id" style="width:100%; padding:10px; margin-top:5px;" required>
                <?php foreach($parents as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?> (<?= $p['sku'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <div style="display:flex; gap:20px">
                <div style="flex:1">
                    <label>Giá gốc:</label>
                    <input type="number" name="base_price" value="10000000" style="width:100%">
                </div>
                <div style="flex:1">
                    <label>SKU Tiền tố:</label>
                    <input type="text" name="base_sku" value="IP15" style="width:100%">
                </div>
            </div>
        </div>

        <?php foreach($attributes as $attrId => $attrData): ?>
            <div class="box">
                <h3 style="margin-top:0"><?= $attrData['name'] ?></h3>
                <?php foreach($attrData['options'] as $opt): ?>
                    <div class="row">
                        <input type="checkbox" name="variants[<?= $attrId ?>][]" value="<?= $opt['id'] ?>">
                        <div style="width:150px"><b><?= $opt['value'] ?></b></div>
                        
                        <input type="number" name="price_mod[<?= $attrId ?>][<?= $opt['id'] ?>]" placeholder="+ Giá" style="width:100px">
                        
                        <?php if($attrData['is_customizable']): ?>
                            <input type="text" name="custom_names[<?= $attrId ?>][<?= $opt['id'] ?>]" placeholder="Tên tùy chỉnh (Deep Purple)..." style="flex:1">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="btn_generate" class="btn-submit">BẮT ĐẦU TẠO</button>
    </form>
</body>
</html>