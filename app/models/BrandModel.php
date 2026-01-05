<?php
require_once __DIR__ . '/BaseModel.php';

class BrandModel extends BaseModel {
    
    // 1. Lấy tất cả brand
    // File: models/BrandModel.php

    // [THAY THẾ] Hàm getAll hỗ trợ Tìm kiếm & Phân trang
    public function getAll($keyword = '', $page = 1, $limit = 10) {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND name LIKE '%$kw%'";
        }

        // Tính Offset
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM brands WHERE $where ORDER BY id DESC LIMIT $offset, $limit";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // [THÊM MỚI] Đếm tổng số bản ghi (cho Phân trang)
    public function countAll($keyword = '') {
        $where = "1=1";
        if ($keyword) {
            $kw = $this->escape($keyword);
            $where .= " AND name LIKE '%$kw%'";
        }
        $sql = "SELECT COUNT(*) as total FROM brands WHERE $where";
        $result = $this->_query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // [THÊM MỚI] Đếm số lượng có Logo (cho Thống kê)
    public function countHasLogo() {
        $result = $this->_query("SELECT COUNT(*) as total FROM brands WHERE logo_url IS NOT NULL AND logo_url != ''");
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    // 2. [MỚI] Lấy Brands theo Category ID (Dùng cho Product form)
    public function getByCategoryId($cateId) {
        $cateId = $this->escape($cateId);
        // Join với bảng trung gian
        $sql = "SELECT b.* FROM brands b 
                JOIN category_brand cb ON b.id = cb.brand_id 
                WHERE cb.category_id = '$cateId'
                ORDER BY b.name ASC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // 3. [MỚI] Lấy danh sách ID danh mục của 1 Brand (Dùng khi Edit Brand)
    public function getCategoryIds($brandId) {
        $brandId = $this->escape($brandId);
        $result = $this->_query("SELECT category_id FROM category_brand WHERE brand_id = '$brandId'");
        $ids = [];
        while($row = mysqli_fetch_assoc($result)) {
            $ids[] = $row['category_id'];
        }
        return $ids;
    }

    public function getById($id) {
        $id = $this->escape($id);
        $result = $this->_query("SELECT * FROM brands WHERE id = '$id'");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public function checkNameExists($name, $excludeId = 0) {
        $name = $this->escape($name);
        $sql = "SELECT id FROM brands WHERE name = '$name'";
        if ($excludeId > 0) $sql .= " AND id != '$excludeId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }

    // [CẬP NHẬT] Create trả về ID để lưu category
    public function create($name, $logoUrl) {
        $name = $this->escape($name);
        $slug = $this->escape($this->createSlug($name));
        $logoUrl = $this->escape($logoUrl);
        
        $sql = "INSERT INTO brands (name, slug, logo_url) VALUES ('$name', '$slug', '$logoUrl')";
        if ($this->_query($sql)) {
            return mysqli_insert_id($this->conn); // Trả về ID vừa tạo
        }
        return false;
    }

    public function update($id, $name, $logoUrl) {
        $id = $this->escape($id);
        $name = $this->escape($name);
        $slug = $this->escape($this->createSlug($name));
        $logoUrl = $this->escape($logoUrl);

        $sql = "UPDATE brands SET name='$name', slug='$slug', logo_url='$logoUrl' WHERE id='$id'";
        return $this->_query($sql);
    }

    // [MỚI] Lưu quan hệ Brand - Category
    public function updateCategories($brandId, $categoryIds) {
        $brandId = $this->escape($brandId);
        // Xóa hết cũ
        $this->_query("DELETE FROM category_brand WHERE brand_id = '$brandId'");
        
        // Thêm mới
        if (!empty($categoryIds) && is_array($categoryIds)) {
            foreach ($categoryIds as $catId) {
                $catId = (int)$catId;
                $this->_query("INSERT INTO category_brand (brand_id, category_id) VALUES ('$brandId', '$catId')");
            }
        }
    }

    public function countProducts($brandId) {
        $brandId = $this->escape($brandId);
        $result = $this->_query("SELECT COUNT(*) as total FROM products WHERE brand_id = '$brandId'");
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function delete($id) {
        $id = $this->escape($id);
        
        // Xóa quan hệ trước
        $this->_query("DELETE FROM category_brand WHERE brand_id = '$id'");

        $brand = $this->getById($id);
        if ($brand && !empty($brand['logo_url'])) {
            $rootPath = dirname(__DIR__, 2);
            $relativePath = ltrim($brand['logo_url'], '/'); 
            $filePath = $rootPath . DIRECTORY_SEPARATOR . $relativePath;
            if (file_exists($filePath)) unlink($filePath);
        }

        return $this->_query("DELETE FROM brands WHERE id = '$id'");
    }
    // Hàm xóa Logo chuyên biệt (Giống logic deleteImage của Product nhưng dùng UPDATE)
    public function removeLogo($brandId) {
        $brandId = $this->escape($brandId);
        
        // 1. Lấy đường dẫn ảnh hiện tại
        $brand = $this->getById($brandId);
        
        // 2. Xóa file vật lý nếu tồn tại
        if ($brand && !empty($brand['logo_url'])) {
            // Xử lý đường dẫn (tương tự ProductModel)
            // __DIR__ đang ở app/models, lùi 2 cấp ra root
            $rootPath = dirname(__DIR__, 2); 
            $relativePath = ltrim($brand['logo_url'], '/\\');
            $filePath = $rootPath . DIRECTORY_SEPARATOR . $relativePath;

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 3. Cập nhật DB (Set logo thành rỗng, KHÔNG xóa dòng brand)
        $sql = "UPDATE brands SET logo_url = '' WHERE id = '$brandId'";
        return $this->_query($sql);
    }
}
?>