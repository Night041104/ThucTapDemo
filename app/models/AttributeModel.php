<?php
require_once __DIR__ . '/BaseModel.php';

class AttributeModel extends BaseModel {
    public function getAll() {
        $sql = "SELECT a.*, GROUP_CONCAT(o.value SEPARATOR ', ') as opts_list 
                FROM attributes a 
                LEFT JOIN attribute_options o ON a.id = o.attribute_id 
                GROUP BY a.id ORDER BY a.id DESC";
        $result = mysqli_query($this->conn, $sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = $this->escape($id);
        $result = mysqli_query($this->conn, "SELECT * FROM attributes WHERE id = '$id'");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public function getOptions($id) {
        $id = $this->escape($id);
        $result = mysqli_query($this->conn, "SELECT value FROM attribute_options WHERE attribute_id = '$id'");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row['value'];
        return $data;
    }

    public function create($code, $name, $isCustom, $options) {
        $code = $this->escape($code); $name = $this->escape($name); $isCustom = (int)$isCustom;
        mysqli_query($this->conn, "INSERT INTO attributes (code, name, is_customizable) VALUES ('$code', '$name', '$isCustom')");
        $attrId = mysqli_insert_id($this->conn);
        foreach ($options as $val) {
            $val = trim($this->escape($val));
            if ($val) mysqli_query($this->conn, "INSERT INTO attribute_options (attribute_id, value) VALUES ('$attrId', '$val')");
        }
    }

    public function update($id, $code, $name, $isCustom, $options) {
        $id = $this->escape($id); $code = $this->escape($code); $name = $this->escape($name); $isCustom = (int)$isCustom;
        mysqli_query($this->conn, "UPDATE attributes SET code='$code', name='$name', is_customizable='$isCustom' WHERE id='$id'");
        foreach ($options as $val) {
            $val = trim($this->escape($val));
            if ($val) {
                // Check exist to avoid duplicates
                $check = mysqli_query($this->conn, "SELECT id FROM attribute_options WHERE attribute_id='$id' AND value='$val'");
                if (mysqli_num_rows($check) == 0) {
                    mysqli_query($this->conn, "INSERT INTO attribute_options (attribute_id, value) VALUES ('$id', '$val')");
                }
            }
        }
    }

    public function delete($id) {
        $id = $this->escape($id);
        mysqli_query($this->conn, "DELETE FROM attribute_options WHERE attribute_id='$id'");
        mysqli_query($this->conn, "DELETE FROM attributes WHERE id='$id'");
    }
}
?>