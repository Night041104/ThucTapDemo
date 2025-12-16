<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Hãng Sản Xuất</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; background-color: #f4f6f8; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <a href="index.php">← Dashboard</a>
    <h1>TẠO HÃNG SẢN XUẤT</h1>
    
    <div class="box">
        <form method="POST" action="index.php?act=store_brand">
            <label><b>Tên Hãng:</b></label><br>
            <input type="text" name="name" required placeholder="VD: Samsung" style="width:100%; padding:8px; margin:5px 0 15px 0;">
            
            <label><b>Hãng này thuộc các danh mục nào?</b></label>
            <div style="margin-top:5px; border:1px solid #ccc; padding:10px; max-height:200px; overflow-y:auto;">
                <?php foreach($categories as $c): ?>
                    <label style="display:block; margin-bottom:5px;">
                        <input type="checkbox" name="cate_ids[]" value="<?= $c['id'] ?>"> 
                        <?= $c['name'] ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit">LƯU HÃNG</button>
        </form>
    </div>
</body>
</html>