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
        // Escape toàn bộ dữ liệu đầu vào
        $name     = $this->escape($data['name']);
        $sku      = $this->escape($data['sku']);
        $slug     = $this->escape($data['slug']);
        $cateId   = (int)$data['category_id'];
        $brandId  = (int)$data['brand_id'];
        $thumb    = $this->escape($data['thumbnail']);
        $specs    = $this->escape($data['specs_json']);
        $price    = (int)$data['price'];
        $mPrice   = (int)$data['market_price'];
        $qty      = (int)$data['quantity'];
        $status   = (int)$data['status'];
        $parentId = isset($data['parent_id']) ? (int)$data['parent_id'] : 0;

        $sql = "INSERT INTO products (parent_id, name, sku, slug, category_id, brand_id, thumbnail, specs_json, price, market_price, quantity, status) 
                VALUES ('$parentId', '$name', '$sku', '$slug', '$cateId', '$brandId', '$thumb', '$specs', '$price', '$mPrice', '$qty', '$status')";
        
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
    public function syncFamilyData($currentId, $parentId, $dataToSync) {
        // 1. Xác định Master ID (Trưởng tộc)
        $masterId = ($parentId == 0 || $parentId == NULL) ? $currentId : $parentId;
        
        // 2. Lấy dữ liệu cần đồng bộ
        $brandId = (int)$dataToSync['brand_id'];
        $cateId  = (int)$dataToSync['category_id'];
        $status  = (int)$dataToSync['status'];
        $specsJsonNew = $dataToSync['specs_json']; // Template specs mới vừa sửa

        // 3. Đồng bộ thông tin cơ bản (Brand, Category, Status) cho cả dòng họ
        // Dùng câu lệnh SQL này để update 1 lần cho tất cả (nhanh gọn)
        $sqlBasic = "UPDATE products SET brand_id='$brandId', category_id='$cateId', status='$status' 
                     WHERE id='$masterId' OR parent_id='$masterId'";
        $this->_query($sqlBasic);

        // 4. ĐỒNG BỘ THÔNG SỐ (SMART SYNC)
        // Phần này phải xử lý bằng PHP vì logic "trộn" JSON quá phức tạp với SQL thuần

        // A. Lấy danh sách các thành viên khác trong gia đình (trừ thằng đang sửa)
        $sqlFamily = "SELECT id FROM products WHERE (id='$masterId' OR parent_id='$masterId') AND id != '$currentId'";
        $rsFamily = $this->_query($sqlFamily);
        
        if ($rsFamily) {
            $familyMembers = mysqli_fetch_all($rsFamily, MYSQLI_ASSOC);
            $specsArrayNew = json_decode($specsJsonNew, true) ?? []; // Decode JSON mới ra mảng để dùng làm khuôn

            foreach ($familyMembers as $member) {
                $memId = $member['id'];
                
                // B. Với mỗi thành viên, lục lại trong kho EAV xem nó có đặc điểm gì riêng?
                // (Chỉ lấy những thuộc tính có is_variant=1, ví dụ: Màu, RAM)
                $sqlMemEav = "SELECT pav.value_custom, a.name 
                              FROM product_attribute_values pav 
                              JOIN attributes a ON pav.attribute_id=a.id 
                              WHERE pav.product_id='$memId' AND a.is_variant=1";
                $rsMem = $this->_query($sqlMemEav);
                
                $memVars = []; // Mảng chứa đặc điểm riêng: ['màu sắc' => 'Đỏ', 'ram' => '8GB']
                while($r = mysqli_fetch_assoc($rsMem)) {
                    $memVars[mb_strtolower(trim($r['name']), 'UTF-8')] = $r['value_custom'];
                }

                // C. TRỘN DỮ LIỆU ("Bình mới rượu cũ")
                // Lấy cái khuôn mới ($specsArrayNew) ốp vào, nhưng điền lại giá trị riêng ($memVars) vào đúng chỗ
                $tempSpecs = $specsArrayNew;
                foreach ($tempSpecs as $gK => $group) {
                    foreach ($group['items'] as $iK => $item) {
                        $key = mb_strtolower(trim($item['name']), 'UTF-8');
                        // Nếu tên thông số khớp với đặc điểm riêng -> Ghi đè giá trị cũ vào
                        if (isset($memVars[$key])) {
                            $tempSpecs[$gK]['items'][$iK]['value'] = $memVars[$key];
                        }
                    }
                }
                
                // D. Lưu lại JSON đã trộn cho thành viên này
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
}
?>