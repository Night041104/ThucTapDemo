<?php
require_once 'app/Models/BaseModel.php';

class AttributeModel extends BaseModel {
    public function getAllWithOptions() {
        // ... (Giữ nguyên code cũ) ...
        $stmt = $this->conn->prepare("SELECT * FROM attributes ORDER BY id ASC");
        $stmt->execute();
        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($attributes as $attr) {
            $stmtOpt = $this->conn->prepare("SELECT * FROM attribute_options WHERE attribute_id = ?");
            $stmtOpt->execute([$attr['id']]);
            $result[$attr['id']] = [
                'code' => $attr['code'], // Thêm code
                'name' => $attr['name'],
                'is_customizable' => $attr['is_customizable'],
                'options' => $stmtOpt->fetchAll(PDO::FETCH_ASSOC)
            ];
        }
        return $result;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM attributes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM attributes WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data) {
        if (!empty($data['id'])) {
            // Update
            $stmt = $this->conn->prepare("UPDATE attributes SET code=?, name=?, is_customizable=? WHERE id=?");
            $stmt->execute([$data['code'], $data['name'], $data['is_customizable'], $data['id']]);
            $id = $data['id'];
        } else {
            // Insert
            $stmt = $this->conn->prepare("INSERT INTO attributes (code, name, input_type, is_customizable) VALUES (?, ?, 'select', ?)");
            $stmt->execute([$data['code'], $data['name'], $data['is_customizable']]);
            $id = $this->conn->lastInsertId();
        }

        // Insert Options (Chỉ thêm mới)
        if (!empty($data['options'])) {
            $opts = explode(',', $data['options']);
            foreach ($opts as $val) {
                $val = trim($val);
                if ($val) {
                    $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM attribute_options WHERE attribute_id=? AND value=?");
                    $stmtCheck->execute([$id, $val]);
                    if ($stmtCheck->fetchColumn() == 0) {
                        $this->conn->prepare("INSERT INTO attribute_options (attribute_id, value) VALUES (?, ?)")->execute([$id, $val]);
                    }
                }
            }
        }
    }

    public function delete($id) {
        $this->conn->prepare("DELETE FROM attribute_options WHERE attribute_id=?")->execute([$id]);
        $this->conn->prepare("DELETE FROM attributes WHERE id=?")->execute([$id]);
    }
}
?>