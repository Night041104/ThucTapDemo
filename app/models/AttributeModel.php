<?php
require_once __DIR__ . '/BaseModel.php';

class AttributeModel extends BaseModel {
    
    // Lấy danh sách để hiển thị ra bảng (index)
    public function getAll() {
        $sql = "SELECT a.*, GROUP_CONCAT(o.value SEPARATOR ', ') as opts_list 
                FROM attributes a 
                LEFT JOIN attribute_options o ON a.id = o.attribute_id 
                GROUP BY a.id ORDER BY a.id DESC";
        $result = $this->_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Lấy chi tiết để đổ vào Form Sửa
    public function getById($id) {
        $id = $this->escape($id);
        $result = $this->_query("SELECT * FROM attributes WHERE id = '$id'");
        $data = $result ? mysqli_fetch_assoc($result) : null;
        
        if ($data) {
            // Lấy options dạng chuỗi để hiển thị vào textarea
            $rsOpt = $this->_query("SELECT value FROM attribute_options WHERE attribute_id = '$id'");
            $opts = [];
            if($rsOpt) {
                while($row = mysqli_fetch_assoc($rsOpt)) $opts[] = $row['value'];
            }
            $data['options_str'] = implode(', ', $opts);
        }
        return $data;
    }

    // Dùng cho Controller Product (Lấy options để hiển thị dropdown)
    public function getAllOptionsGrouped() {
        $sql = "SELECT * FROM attribute_options ORDER BY attribute_id ASC, id ASC";
        $result = $this->_query($sql);
        $data = [];
        if($result) {
            while($row = mysqli_fetch_assoc($result)){
                $data[$row['attribute_id']][] =  $row;
            }
        }
        return $data;
    }

    // Kiểm tra trùng mã code (Validation)
    public function checkCodeExists($code, $excludeId = 0) {
        $code = $this->escape($code);
        $sql = "SELECT id FROM attributes WHERE code = '$code'";
        if ($excludeId > 0) $sql .= " AND id != '$excludeId'";
        $rs = $this->_query($sql);
        return mysqli_num_rows($rs) > 0;
    }

    // [CREATE] Thêm mới thuộc tính
    public function create($code, $name, $isCustom, $isVariant, $options) {
        $code = $this->escape($code);
        $name = $this->escape($name);
        $isCustom = (int)$isCustom;
        $isVariant = (int)$isVariant;

        $sql = "INSERT INTO attributes (code, name, input_type, is_customizable, is_variant) 
                VALUES ('$code', '$name', 'select', '$isCustom', '$isVariant')";
        
        if ($this->_query($sql)) {
            $attrId = mysqli_insert_id($this->conn);
            $this->insertOptions($attrId, $options);
            return true;
        }
        return false;
    }

    // [UPDATE] Cập nhật thuộc tính
    public function update($id, $code, $name, $isCustom, $isVariant, $options) {
        $id = $this->escape($id);
        $code = $this->escape($code);
        $name = $this->escape($name);
        $isCustom = (int)$isCustom;
        $isVariant = (int)$isVariant;

        $sql = "UPDATE attributes SET code='$code', name='$name', is_customizable='$isCustom', is_variant='$isVariant' WHERE id='$id'";
        
        if ($this->_query($sql)) {
            $this->insertOptions($id, $options);
            return true;
        }
        return false;
    }

    // Helper: Thêm Options (Logic cũ: Chỉ thêm mới, KHÔNG xóa cũ)
    private function insertOptions($attrId, $optionsArr) {
        foreach ($optionsArr as $val) {
            $val = trim($this->escape($val));
            if ($val !== '') {
                // Check trùng lặp trong DB trước khi insert
                $checkSql = "SELECT id FROM attribute_options WHERE attribute_id='$attrId' AND value='$val'";
                $rsCheck = $this->_query($checkSql);
                if (mysqli_num_rows($rsCheck) == 0) {
                    $this->_query("INSERT INTO attribute_options (attribute_id, value) VALUES ('$attrId', '$val')");
                }
            }
        }
    }

    // Xóa (Lưu ý: Nếu thuộc tính đang dùng trong SP thì MySQL có thể chặn nếu có FK)
    public function delete($id) {
        $id = $this->escape($id);
        $this->_query("DELETE FROM attribute_options WHERE attribute_id='$id'");
        return $this->_query("DELETE FROM attributes WHERE id='$id'");
    }
}
?>