<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    //Hàm getAll dưới dây sẽ dùng để hiển thị danh sách sản phẩm, đây là 1 hàm hiển thị 3 trong 1:
    //-Hiển thị toàn bộ danh sách
    //-Hiển thị theo keyword (ô tìm kiếm)
    //-Hiển thị theo danh sách cha (ô select, phần này lên cty giải thích sau)
    public function getAll($filterMasterId = 0, $keyword = ''){
        $where = "1=1";
        //Logic lọc theo Cha (giải thích sau)
        if($filterMasterId > 0) {
            $fid = $this -> escape($filterMasterId);
            $where .= " AND (p.id = '$fid' or p.parent_id = '$fid')"; //chèn thêm điều kiện vào where
        }
        //Logic tìm kiếm theo keyword (tên hoặc sku)
        if($keyword) {
            $kw = $this -> escape($keyword);
            $where .= " AND (p.name LIKE '%$kw%' OR p.sku LIKE '%$kw%')"; //chèn thêm điều kiện gần đúng 
        }
        // Logic sắp xếp: Cha con cho nằm gần nhau
        $sql = "SELECT p.*,
                c.name as cate_name,
                b.name as brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE $where
                ORDER BY IF (p.parent_id = 0, p.id, p.parent_id) DESC, p.id ASC   
        ";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : []; // trả về danh sách 
    }
    //Lấy danh sách cha, để tạo dữ liệu cho thẻ select
    public function getMasters(){
        $sql = "SELECT id, name FROM products WHERE parent_id IS NULL OR parent_id = 0 ORDER BY id DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : []; // trả về danh sách 
    }
    //Hàm dưới đây dùng cho logic xóa và kế thừa, nghĩa là khi xóa 1 sp cha, sẽ đẩy 1 sản phẩm con của sản phẩm đó lên làm cha
    public function deleteWithInheritance($id) {
        $id = $this->escape($id);
        //Bước 1: Kiểm tra xem sản phẩm có tồn tại hay không
        $check = $this->_query("SELECT id,parent_id FROM products WHERE id = '$id'");
        $product = mysqli_fetch_assoc($check);
        if($product){
            $isMaster = ($product['parent_id']==0 || $product['parent_id']==NULL); //dòng này là để kiểm tra xem sản phẩm này có phải là sản phẩm cha hay không
            //2 Logic kế thừa: nếu sản phẩm trên là cha, thì ta sẽ để cho 1 sản phẩm con ngay sau nó kế thừa và bến sp con đó trở thành cha
            if($isMaster){
                //Tìm sản phẩm con đầu tiên của sp cha trên, dùng thêm LIMIT 1 để chỉ lấy đúng 1 sản phẩm
                $rsHeir = $this->_query("SELECT id FROM products WHERE parent_id = '$id' ORDER BY id ASC LIMIT 1 ");
                $heir = mysqli_fetch_assoc($rsHeir);
                if($heir){
                    $newMasterId = $heir['id']; // gán id sp con mới tìm được vào 1 biến, biến này sẽ được sử dụng cho lệnh sql dưới đây để biến sp con thành cha
                    $this->_query("UPDATE products SET parent_id = 0 WHERE id = '$newMasterId'"); //các sp con đều có parent_id khác 0, vì vậy khi ta set parent_id của sp con này = 0 nghĩa là đã công nhận nó trở thành cha
                    $this->_query("UPDATE products SET parent_id = '$newMasterId' WHERE parent_id = '$id' ");//lấy có tất cả sp con của sp cha ban đầu và đổi cha nó sang sp cha mới kế thừa ở dòng ngay trên                   
                }
            }
            // B. Xóa Thumbnail vật lý
            if (!empty($product['thumbnail'])) {
                $thumbPath = __DIR__ . '/../../' . $product['thumbnail'];
                if (file_exists($thumbPath)) unlink($thumbPath);
            }

            // C. Xóa Gallery vật lý
            $rsGal = $this->_query("SELECT image_url FROM product_images WHERE product_id = '$id'");
            while ($img = mysqli_fetch_assoc($rsGal)) {
                if (!empty($img['image_url'])) {
                    $galPath = __DIR__ . '/../../' . $img['image_url'];
                    if (file_exists($galPath)) unlink($galPath);
                }
            }
            //3.Xóa dữ liệu (Ảnh, EAV, Sản phẩm) của sản phẩm cha ban đầu
            $this->_query("DELETE FROM product_images WHERE product_id = '$id'");
            $this->_query("DELETE FROM product_attribute_values WHERE product_id = '$id'");
            $this->_query("DELETE FROM products WHERE id = '$id'");
            return true;
        }
        return false;
    }
    //hàm sau dùng cho edit_product và clone_product
    public function getById($id){
        $id = $this->escape($id);
        $result = $this->_query("SELECT * FROM products WHERE id = '$id'");
        return $result ? mysqli_fetch_assoc($result) : null;
    }
    public function getGallery($productId) {
        $id = $this->escape($productId);
        $result = $this->_query("SELECT * FROM product_images WHERE product_id = '$id'");
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // các hàm sau dùng cho create_product và clone_product
    public function create($data) {
    // 1. Escape dữ liệu text bình thường
    $name     = $this->escape($data['name']);
    $sku      = $this->escape($data['sku']);
    $slug     = $this->escape($data['slug']);
    $cateId   = (int)$data['category_id'];
    $brandId  = (int)$data['brand_id'];
    $thumb    = $this->escape($data['thumbnail']);
    $specs    = $this->escape($data['specs_json']);
    
    // Xử lý giá (bỏ dấu phẩy nếu có)
    $price    = (int)str_replace(',', '', $data['price']);
    $mPrice   = (int)str_replace(',', '', $data['market_price']);
    $qty      = (int)$data['quantity'];
    $status   = (int)$data['status'];

    // 2. [QUAN TRỌNG] Xử lý Parent ID để tránh lỗi Foreign Key
    // Nếu có parent_id > 0 thì thêm dấu nháy đơn vào: '123'
    // Nếu không có (hoặc bằng 0) thì gán cứng chữ: NULL (không nháy)
    if (!empty($data['parent_id']) && $data['parent_id'] > 0) {
        $parentId = "'" . (int)$data['parent_id'] . "'";
    } else {
        $parentId = "NULL"; 
    }

    // 3. Câu lệnh SQL
    // Lưu ý: $parentId ở dưới KHÔNG được bao quanh bởi dấu nháy '' nữa
    $sql = "INSERT INTO products 
            (parent_id, name, sku, slug, category_id, brand_id, thumbnail, specs_json, price, market_price, quantity, status) 
            VALUES 
            ($parentId, '$name', '$sku', '$slug', '$cateId', '$brandId', '$thumb', '$specs', '$price', '$mPrice', '$qty', '$status')";
    
    if ($this->_query($sql)) {
        return mysqli_insert_id($this->conn);
    }
    return false;
}

    // Insert EAV (Dùng chung cho Create/Edit/Clone)
    public function addAttributeValue($productId, $attrId, $optId, $val) {
        $val = $this->escape($val);
        $sql = "INSERT INTO product_attribute_values (product_id, attribute_id, option_id, value_custom) 
                VALUES ('$productId', '$attrId', '$optId', '$val')";
        $this->_query($sql);
    }

    // Insert Gallery
    public function addImage($productId, $url) {
        $url = $this->escape($url);
        $this->_query("INSERT INTO product_images (product_id, image_url) VALUES ('$productId', '$url')");
    }

    // --- DÙNG CHO: edit_product.php ---
    public function update($id, $data) {
        $id       = (int)$id;
        $name     = $this->escape($data['name']);
        $slug     = $this->escape($data['slug']);
        $cateId   = (int)$data['category_id'];
        $brandId  = (int)$data['brand_id'];
        $thumb    = $this->escape($data['thumbnail']);
        $specs    = $this->escape($data['specs_json']);
        $price    = (int)$data['price'];
        $mPrice   = (int)$data['market_price'];
        $qty      = (int)$data['quantity'];
        $status   = (int)$data['status'];

        $sql = "UPDATE products SET 
                name='$name', slug='$slug', category_id='$cateId', brand_id='$brandId', 
                thumbnail='$thumb', specs_json='$specs', 
                price='$price', market_price='$mPrice', quantity='$qty', status='$status' 
                WHERE id='$id'";
        
        return $this->_query($sql);
    }

    // Xóa hết EAV cũ (để lưu mới khi Update)
    public function clearAttributes($productId) {
        $productId = (int)$productId;
        $this->_query("DELETE FROM product_attribute_values WHERE product_id = '$productId'");
    }

    // Xóa 1 ảnh gallery
    public function deleteImage($imgId) {
        $imgId = (int)$imgId;    
        // 1. Lấy đường dẫn ảnh trước khi xóa DB
        $query = $this->_query("SELECT image_url FROM product_images WHERE id = '$imgId'");
        $img = mysqli_fetch_assoc($query);   
        // 2. Xóa file vật lý nếu tồn tại
        if ($img && !empty($img['image_url'])) {
            $filePath = __DIR__ . '/../../' . $img['image_url']; // Định vị từ thư mục models ra root
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // 3. Xóa trong DB
        $this->_query("DELETE FROM product_images WHERE id = '$imgId'");
    }
    // Logic đồng bộ sản phẩm cha-con: Mỗi khi có 1  sự chỉnh sửa thông tin chung từ bất kỳ sp cha hoặc sp con nào thì sẽ cập nhật lại toàn bộ các sp cha và  con đó
    // [ĐÃ SỬA TRIỆT ĐỂ] Logic đồng bộ: Khớp theo Attribute ID để bảo toàn biến thể con
    // [ĐÃ SỬA LỖI] Logic đồng bộ: Ưu tiên Custom Value để không bị mất chữ nhập tay của con
    public function syncFamilyData($currentId, $parentId, $dataToSync) {
        // 1. Xác định Master ID
        $masterId = ($parentId == 0 || $parentId == NULL) ? $currentId : $parentId;
        
        // 2. Đồng bộ thông tin cơ bản
        $brandId = (int)$dataToSync['brand_id'];
        $cateId  = (int)$dataToSync['category_id'];
        $status  = (int)$dataToSync['status'];
        $specsJsonNew = $dataToSync['specs_json']; 

        $this->_query("UPDATE products SET brand_id='$brandId', category_id='$cateId', status='$status' 
                       WHERE id='$masterId' OR parent_id='$masterId'");

        // 3. ĐỒNG BỘ THÔNG SỐ (GIỮ LẠI GIÁ TRỊ RIÊNG CỦA CON)
        $sqlFamily = "SELECT id FROM products WHERE (id='$masterId' OR parent_id='$masterId') AND id != '$currentId'";
        $rsFamily = $this->_query($sqlFamily);
        
        if ($rsFamily) {
            $familyMembers = mysqli_fetch_all($rsFamily, MYSQLI_ASSOC);
            $specsArrayNew = json_decode($specsJsonNew, true) ?? []; 

            foreach ($familyMembers as $member) {
                $memId = $member['id'];
                
                // A. Lấy giá trị thuộc tính hiện có của Con
                $sqlMemEav = "SELECT pav.attribute_id, pav.value_custom, pav.option_id, ao.value as option_value
                              FROM product_attribute_values pav 
                              JOIN attributes a ON pav.attribute_id = a.id 
                              LEFT JOIN attribute_options ao ON pav.option_id = ao.id
                              WHERE pav.product_id='$memId' AND a.is_variant=1";
                              
                $rsMem = $this->_query($sqlMemEav);
                $memVars = []; 
                while($r = mysqli_fetch_assoc($rsMem)) {
                    // [SỬA LỖI TẠI ĐÂY]: Ưu tiên lấy value_custom (chữ nhập tay) trước!
                    // Vì value_custom luôn chứa text hiển thị chính xác (kể cả khi chọn dropdown)
                    $val = !empty($r['value_custom']) ? $r['value_custom'] : $r['option_value'];
                    
                    $memVars[$r['attribute_id']] = $val;
                }

                // B. Trộn dữ liệu
                $tempSpecs = $specsArrayNew;
                foreach ($tempSpecs as $gK => $group) {
                    if(isset($group['items'])){
                        foreach ($group['items'] as $iK => $item) {
                            $attrId = isset($item['attr_id']) ? $item['attr_id'] : 0;
                            
                            // Nếu Con có giá trị riêng -> Ghi đè lại vào JSON
                            if ($attrId > 0 && isset($memVars[$attrId])) {
                                $tempSpecs[$gK]['items'][$iK]['value'] = $memVars[$attrId];
                            }
                        }
                    }
                }
                
                // C. Lưu lại JSON
                $finalJson = $this->escape(json_encode($tempSpecs, JSON_UNESCAPED_UNICODE));
                $this->_query("UPDATE products SET specs_json = '$finalJson' WHERE id = '$memId'");
            }
        }
    }
    // Check trùng slug (Helper)
    public function checkSlugExists($slug, $excludeId = 0) {
        $slug = $this->escape($slug);
        $sql = "SELECT id FROM products WHERE slug = '$slug'";
        if ($excludeId > 0) $sql .= " AND id != '$excludeId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }
    // [CLIENT] Lấy danh sách sản phẩm theo Danh mục (Chỉ hiện SP Cha & Đang bán)
    public function getProductsByCateForClient($cateId) {
        $cateId = $this->escape($cateId);
        
        // Logic: 
        // - p.status = 1: Đang bán
        // - (p.parent_id IS NULL OR p.parent_id = 0): CHỈ LẤY SẢN PHẨM CHA (Đại diện)
        // - Tránh hiện hàng loạt biến thể con ra trang danh sách
        
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.category_id = '$cateId' 
                AND p.status = 1 
                AND (p.parent_id IS NULL OR p.parent_id = 0) 
                ORDER BY p.id DESC";
                
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    // [MỚI] Lấy danh sách Attribute & Option ID của sản phẩm để fill vào form sửa
    public function getAttributeValues($productId) {
        $id = $this->escape($productId);
        $sql = "SELECT attribute_id, option_id, value_custom FROM product_attribute_values WHERE product_id = '$id'";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    public function getVariantAttributeIds() {
        $sql = "SELECT id FROM attributes WHERE is_variant = 1";
        $result = $this->_query($sql);
        $ids = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }
    // [CLIENT] Lấy danh sách các sản phẩm cùng gia đình (Cha + Con) để hiển thị nút chọn biến thể
    public function getProductFamily($masterId) {
        $masterId = $this->escape($masterId);
        // Lấy ID, Tên, Giá, Thumbnail, Specs, Slug của cả cha lẫn con
        $sql = "SELECT id, name, slug, price, market_price, thumbnail, specs_json, parent_id 
                FROM products 
                WHERE (id = '$masterId' OR parent_id = '$masterId') AND status = 1 
                ORDER BY price ASC"; // Sắp xếp theo giá tăng dần
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    // [CLIENT] Lấy bản đồ biến thể của cả gia đình sản phẩm (Dùng để gom nhóm nút bấm)
    public function getFamilyVariantMap($masterId) {
        $masterId = $this->escape($masterId);
        
        // Query này lấy ra: ProductID nào sở hữu Attribute nào và Giá trị là gì
        // Chỉ lấy những thuộc tính là biến thể (is_variant = 1)
        // Ưu tiên lấy giá trị từ bảng options, nếu không có thì lấy value_custom
        $sql = "SELECT 
                    p.id as product_id,
                    p.price,
                    pav.attribute_id,
                    a.name as attribute_name,
                    COALESCE(ao.value, pav.value_custom) as attribute_value
                FROM products p
                JOIN product_attribute_values pav ON p.id = pav.product_id
                JOIN attributes a ON pav.attribute_id = a.id
                LEFT JOIN attribute_options ao ON pav.option_id = ao.id
                WHERE (p.id = '$masterId' OR p.parent_id = '$masterId') 
                AND p.status = 1 
                AND a.is_variant = 1
                ORDER BY a.id ASC, p.price ASC";

        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
}
?>