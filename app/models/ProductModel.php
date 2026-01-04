<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    public function getIdBySlug($slug) {
        $slug = $this->escape($slug);
        $result = $this->_query("SELECT id FROM products WHERE slug = '$slug'");
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['id'] : 0;
    }
    // --- CÁC HÀM GET (GIỮ NGUYÊN) ---
    public function getAll($filterMasterId = 0, $keyword = ''){
        $where = "1=1";
        if($filterMasterId > 0) {
            $fid = $this -> escape($filterMasterId);
            $where .= " AND (p.id = '$fid' or p.parent_id = '$fid')"; 
        }
        if($keyword) {
            $kw = $this -> escape($keyword);
            $where .= " AND (p.name LIKE '%$kw%' OR p.sku LIKE '%$kw%')"; 
        }
        $sql = "SELECT p.*, c.name as cate_name, b.name as brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE $where
                ORDER BY IF (p.parent_id = 0, p.id, p.parent_id) DESC, p.id ASC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : []; 
    }

    public function getMasters(){
        $sql = "SELECT id, name FROM products WHERE parent_id IS NULL OR parent_id = 0 ORDER BY id DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : []; 
    }

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

    // --- CÁC HÀM CREATE / UPDATE (GIỮ NGUYÊN) ---
    public function create($data) {
        $name     = $this->escape($data['name']);
        $sku      = $this->escape($data['sku']);
        $slug     = $this->escape($data['slug']);
        $cateId   = (int)$data['category_id'];
        $brandId  = (int)$data['brand_id'];
        $thumb    = $this->escape($data['thumbnail']);
        $specs    = $this->escape($data['specs_json']);
        $price    = (int)str_replace(',', '', $data['price']);
        $mPrice   = (int)str_replace(',', '', $data['market_price']);
        $qty      = (int)$data['quantity'];
        $status   = (int)$data['status'];

        if (!empty($data['parent_id']) && $data['parent_id'] > 0) {
            $parentId = "'" . (int)$data['parent_id'] . "'";
        } else {
            $parentId = "NULL"; 
        }

        $sql = "INSERT INTO products 
                (parent_id, name, sku, slug, category_id, brand_id, thumbnail, specs_json, price, market_price, quantity, status) 
                VALUES 
                ($parentId, '$name', '$sku', '$slug', '$cateId', '$brandId', '$thumb', '$specs', '$price', '$mPrice', '$qty', '$status')";
        
        if ($this->_query($sql)) {
            return mysqli_insert_id($this->conn);
        }
        return false;
    }

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

    public function addAttributeValue($productId, $attrId, $optId, $val) {
        $val = $this->escape($val);
        $sql = "INSERT INTO product_attribute_values (product_id, attribute_id, option_id, value_custom) 
                VALUES ('$productId', '$attrId', '$optId', '$val')";
        $this->_query($sql);
    }

    public function addImage($productId, $url) {
        $url = $this->escape($url);
        $this->_query("INSERT INTO product_images (product_id, image_url) VALUES ('$productId', '$url')");
    }

    public function clearAttributes($productId) {
        $productId = (int)$productId;
        $this->_query("DELETE FROM product_attribute_values WHERE product_id = '$productId'");
    }

    // =================================================================
    // [LOGIC MỚI] XÓA ẢNH LẺ TRONG FORM EDIT
    // =================================================================
    public function deleteImage($imgId) {
        $imgId = (int)$imgId;    
        
        // 1. Lấy đường dẫn ảnh để kiểm tra sau này
        $query = $this->_query("SELECT image_url FROM product_images WHERE id = '$imgId'");
        $img = mysqli_fetch_assoc($query);
        $pathToCheck = ($img && !empty($img['image_url'])) ? $img['image_url'] : null;

        // 2. Xóa dòng trong Database TRƯỚC (ngắt kết nối ảnh với sản phẩm này)
        $this->_query("DELETE FROM product_images WHERE id = '$imgId'");

        // 3. Sau khi xóa DB, kiểm tra xem file đó có trở thành "mồ côi" không
        // Nếu không còn ai dùng nữa -> Xóa file vật lý
        if ($pathToCheck) {
            $this->cleanupFile($pathToCheck);
        }
    }

    // --- LOGIC ĐỒNG BỘ (GIỮ NGUYÊN) ---
    public function syncFamilyData($currentId, $parentId, $dataToSync, $eavData) {
        $masterId = ($parentId == 0 || $parentId == NULL) ? $currentId : $parentId;
        $brandId = (int)$dataToSync['brand_id'];
        $cateId  = (int)$dataToSync['category_id'];
        $status  = (int)$dataToSync['status'];
        $specsJsonNew = $dataToSync['specs_json']; 

        $this->_query("UPDATE products SET brand_id='$brandId', category_id='$cateId', status='$status' 
                       WHERE id='$masterId' OR parent_id='$masterId'");

        $variantIds = [];
        $rsVar = $this->_query("SELECT id FROM attributes WHERE is_variant = 1");
        while($r = mysqli_fetch_assoc($rsVar)) $variantIds[] = $r['id'];

        $sharedEav = [];
        foreach($eavData as $e) {
            if(!in_array($e['attr_id'], $variantIds)) {
                $sharedEav[] = $e;
            }
        }

        $sqlFamily = "SELECT id FROM products WHERE (id='$masterId' OR parent_id='$masterId') AND id != '$currentId'";
        $rsFamily = $this->_query($sqlFamily);
        
        if ($rsFamily) {
            $familyMembers = mysqli_fetch_all($rsFamily, MYSQLI_ASSOC);
            $specsArrayNew = json_decode($specsJsonNew, true) ?? []; 

            foreach ($familyMembers as $member) {
                $memId = $member['id'];

                if(!empty($sharedEav)) {
                    foreach($sharedEav as $se) {
                        $aId = $se['attr_id'];
                        $this->_query("DELETE FROM product_attribute_values WHERE product_id='$memId' AND attribute_id='$aId'");
                    }
                    foreach($sharedEav as $se) {
                        $this->addAttributeValue($memId, $se['attr_id'], $se['opt_id'], $se['val']);
                    }
                }
                
                $sqlMemEav = "SELECT pav.attribute_id, pav.value_custom, pav.option_id, ao.value as option_value
                              FROM product_attribute_values pav 
                              JOIN attributes a ON pav.attribute_id = a.id 
                              LEFT JOIN attribute_options ao ON pav.option_id = ao.id
                              WHERE pav.product_id='$memId' AND a.is_variant=1";
                              
                $rsMem = $this->_query($sqlMemEav);
                $memVars = []; 
                while($r = mysqli_fetch_assoc($rsMem)) {
                    $val = !empty($r['value_custom']) ? $r['value_custom'] : $r['option_value'];
                    $memVars[$r['attribute_id']] = $val;
                }

                $tempSpecs = $specsArrayNew;
                foreach ($tempSpecs as $gK => $group) {
                    if(isset($group['items'])){
                        foreach ($group['items'] as $iK => $item) {
                            $attrId = isset($item['attr_id']) ? $item['attr_id'] : 0;
                            if ($attrId > 0 && isset($memVars[$attrId])) {
                                $tempSpecs[$gK]['items'][$iK]['value'] = $memVars[$attrId];
                            }
                        }
                    }
                }
                
                $finalJson = $this->escape(json_encode($tempSpecs, JSON_UNESCAPED_UNICODE));
                $this->_query("UPDATE products SET specs_json = '$finalJson' WHERE id = '$memId'");
            }
        }
    }

    // =================================================================
    // [LOGIC MỚI] XÓA SẢN PHẨM & KẾ THỪA 
    // =================================================================
    public function deleteWithInheritance($id) {
        $id = $this->escape($id);
        
        $check = $this->_query("SELECT id, parent_id, thumbnail FROM products WHERE id = '$id'");
        $product = mysqli_fetch_assoc($check);
        
        if($product){
            $checkOrder = $this->_query("SELECT id FROM order_details WHERE product_id = '$id' LIMIT 1");
            $hasOrder = (mysqli_num_rows($checkOrder) > 0);

            $isMaster = ($product['parent_id'] == 0 || $product['parent_id'] == NULL);
            
            // 1. XỬ LÝ KẾ THỪA CHA CON
            if($isMaster){
                $rsHeir = $this->_query("SELECT id FROM products WHERE parent_id = '$id' ORDER BY id ASC LIMIT 1 ");
                $heir = mysqli_fetch_assoc($rsHeir);
                
                if($heir){
                    $newMasterId = $heir['id'];

                    // A. Phong Cha mới
                    $this->_query("UPDATE products SET parent_id = NULL WHERE id = '$newMasterId'"); 
                    $this->_query("UPDATE products SET parent_id = '$newMasterId' WHERE parent_id = '$id' ");
                    
                    // B. [QUAN TRỌNG] KHÔNG COPY GALLERY CỦA CHA CŨ SANG CHA MỚI
                    // Sản phẩm kế thừa chỉ sử dụng ảnh của chính nó.

                    // C. Chuyển EAV (Thuộc tính chung) - Giữ nguyên logic này
                    $varIds = [];
                    $rsVar = $this->_query("SELECT id FROM attributes WHERE is_variant = 1");
                    while($r = mysqli_fetch_assoc($rsVar)) $varIds[] = $r['id'];
                    
                    $sqlGetShared = "SELECT attribute_id, option_id, value_custom FROM product_attribute_values WHERE product_id = '$id'";
                    $rsShared = $this->_query($sqlGetShared);
                    
                    while($row = mysqli_fetch_assoc($rsShared)) {
                        if(!in_array($row['attribute_id'], $varIds)) {
                            $attrId = $row['attribute_id'];
                            $optId  = $row['option_id'] ? "'".$row['option_id']."'" : "NULL";
                            $val    = $this->escape($row['value_custom']);
                            $this->_query("DELETE FROM product_attribute_values WHERE product_id='$newMasterId' AND attribute_id='$attrId'");
                            $this->_query("INSERT INTO product_attribute_values (product_id, attribute_id, option_id, value_custom) VALUES ('$newMasterId', '$attrId', $optId, '$val')");
                        }
                    }
                }
            }

            // 2. QUYẾT ĐỊNH XÓA HAY ẨN
            if ($hasOrder) {
                // Có đơn hàng -> Xóa mềm (Chỉ ẩn, không xóa file)
                $this->_query("UPDATE products SET status = -1 WHERE id = '$id'");
            } else {
                // XÓA VĨNH VIỄN
                
                // A. Lưu lại danh sách ảnh cần kiểm tra trước khi xóa DB
                $pathsToCheck = [];

                // Lấy Thumbnail
                if (!empty($product['thumbnail'])) {
                    $pathsToCheck[] = $product['thumbnail'];
                }

                // Lấy Gallery
                $rsGal = $this->_query("SELECT image_url FROM product_images WHERE product_id = '$id'");
                while ($img = mysqli_fetch_assoc($rsGal)) {
                    $pathsToCheck[] = $img['image_url'];
                }

                // B. Xóa dữ liệu của sản phẩm này trong DB
                $this->_query("DELETE FROM product_images WHERE product_id = '$id'");
                $this->_query("DELETE FROM product_attribute_values WHERE product_id = '$id'");
                $this->_query("DELETE FROM products WHERE id = '$id'");

                // C. [DỌN DẸP FILE]
                // Sau khi đã xóa DB của sản phẩm này, ta kiểm tra từng file ảnh.
                // Nếu file đó không còn xuất hiện trong DB nữa (không ai dùng) -> Xóa file.
                // Nếu file đó vẫn còn trong DB (con hoặc clone đang dùng) -> Giữ lại.
                foreach (array_unique($pathsToCheck) as $path) {
                    $this->cleanupFile($path);
                }
            }

            return true;
        }
        return false;
    }

    // =================================================================
    // [HÀM DỌN DẸP FILE RÁC - LOGIC CHUẨN]
    // Hàm này kiểm tra xem file có còn tồn tại trong DB không. 
    // Nếu KHÔNG tìm thấy ai dùng -> Xóa file vật lý.
    // =================================================================
    public function cleanupFile($filePath) {
        if (empty($filePath)) return;
        $path = $this->escape($filePath);
        
        // 1. Quét bảng PRODUCTS (Thumbnail)
        $sql1 = "SELECT count(*) as total FROM products WHERE thumbnail = '$path'";
        $rs1 = mysqli_fetch_assoc($this->_query($sql1));
        if ($rs1['total'] > 0) return; // Vẫn còn người dùng -> GIỮ FILE

        // 2. Quét bảng PRODUCT_IMAGES (Gallery)
        $sql2 = "SELECT count(*) as total FROM product_images WHERE image_url = '$path'";
        $rs2 = mysqli_fetch_assoc($this->_query($sql2));
        if ($rs2['total'] > 0) return; // Vẫn còn người dùng -> GIỮ FILE

        // 3. Không ai dùng -> Xóa file vật lý
        $realPath = __DIR__ . '/../../' . $filePath;
        if (file_exists($realPath)) {
            unlink($realPath);
        }
    }

    public function checkSlugExists($slug, $excludeId = 0) {
        $slug = $this->escape($slug);
        $sql = "SELECT id FROM products WHERE slug = '$slug'";
        if ($excludeId > 0) $sql .= " AND id != '$excludeId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }

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

    public function getProductFamily($masterId) {
        $masterId = $this->escape($masterId);
        $sql = "SELECT id, name, slug, price, market_price, thumbnail, specs_json, parent_id 
                FROM products 
                WHERE (id = '$masterId' OR parent_id = '$masterId') AND status = 1 
                ORDER BY price ASC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function getFamilyVariantMap($masterId) {
        $masterId = $this->escape($masterId);
        $sql = "SELECT 
                    p.id as product_id,
                    p.slug,  
                    p.price,
                    p.thumbnail,
                    pav.attribute_id,
                    a.name as attribute_name,
                    /* ĐỔI THỨ TỰ: Ưu tiên lấy value_custom trước, nếu trống mới lấy ao.value */
                    COALESCE(NULLIF(pav.value_custom, ''), ao.value) as attribute_value
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

    public function getProductsByIds($arrIds) {
        if (empty($arrIds)) return [];
        $ids = array_map('intval', $arrIds);
        $ids = array_filter($ids); 
        if(empty($ids)) return [];
        $strIds = implode(',', $ids);
        $sql = "SELECT id, name, sku, slug, price, quantity, thumbnail 
                FROM products WHERE id IN ($strIds)";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    public function searchProducts($keyword) {
        $kw = $this->escape($keyword);
        // Chỉ lấy sản phẩm đang kinh doanh và là sản phẩm cha (Master)
        $where = "p.status = 1 AND (p.parent_id IS NULL OR p.parent_id = 0)";
        
        if ($kw != '') {
            // MỞ RỘNG: Tìm theo tên SP OR Tên danh mục OR Tên thương hiệu
            $where .= " AND (p.name LIKE '%$kw%' 
                        OR c.name LIKE '%$kw%' 
                        OR b.name LIKE '%$kw%'
                        OR p.sku LIKE '%$kw%')"; 
        }

        $sql = "SELECT p.*, c.name as cate_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE $where 
                ORDER BY 
                    CASE 
                        WHEN p.name LIKE '$kw%' THEN 1       -- Ưu tiên tên SP khớp đầu câu
                        WHEN c.name LIKE '%$kw%' THEN 2      -- Ưu tiên khớp tên danh mục
                        WHEN p.name LIKE '% $kw%' THEN 3     -- Khớp tên SP ở giữa
                        ELSE 4 
                    END ASC, 
                    p.id DESC";
                
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    // 1. Hàm lấy danh sách mặc định (Chỉ lấy SP Cha)
    public function getProductsByCateForClient($cateId) {
        $cateId = $this->escape($cateId);
        // THÊM: AND (parent_id IS NULL OR parent_id = 0)
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

    // 2. Hàm lọc sản phẩm (Chỉ lấy SP Cha)
    public function getProductsByFilter($cateId, $brandIds = [], $priceRange = '', $attributes = []) {
        $cateId = $this->escape($cateId);
        
        // THÊM: AND (p.parent_id IS NULL OR p.parent_id = 0)
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.category_id = '$cateId' 
                AND p.status = 1
                AND (p.parent_id IS NULL OR p.parent_id = 0)";

        // ... (Giữ nguyên các logic lọc Brand, Price, Attribute cũ của bạn ở đây) ...
        
        // Code ghép chuỗi SQL lọc cũ của bạn:
        if (!empty($brandIds)) {
            $ids = implode(',', array_map('intval', $brandIds));
            $sql .= " AND p.brand_id IN ($ids)";
        }
        if (!empty($priceRange)) {
            $ranges = explode('-', $priceRange);
            if (count($ranges) == 2) {
                $min = (int)$ranges[0];
                $max = ($ranges[1] == 'max') ? 99999999999 : (int)$ranges[1];
                $sql .= " AND p.price >= $min AND p.price <= $max";
            }
        }
        // Logic lọc Attribute (EXISTS) giữ nguyên...
        if (!empty($attributes)) {
             // ... (Code lọc thuộc tính cũ) ...
             foreach ($attributes as $attrName => $values) {
                if (!empty($values)) {
                    $attrName = $this->escape($attrName);
                    $valStr = "'" . implode("','", array_map([$this, 'escape'], $values)) . "'";
                    $subQuery = "SELECT 1 FROM product_attribute_values pav
                                 JOIN attributes a ON pav.attribute_id = a.id
                                 LEFT JOIN attribute_options ao ON pav.option_id = ao.id
                                 WHERE pav.product_id = p.id AND a.name = '$attrName'
                                 AND (pav.value_custom IN ($valStr) OR ao.value IN ($valStr))";
                    $sql .= " AND EXISTS ($subQuery)";
                }
            }
        }

        $sql .= " ORDER BY p.id DESC";
        
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
// [MỚI] Lấy giá thấp nhất và cao nhất trong danh mục để chia khoảng lọc
    public function getMinMaxPrice($cateId) {
        $cateId = $this->escape($cateId);
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price 
                FROM products 
                WHERE category_id = '$cateId' AND status = 1";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        
        return [
            'min' => (int)($row['min_price'] ?? 0),
            'max' => (int)($row['max_price'] ?? 0)
        ];
    }
    
}
?>