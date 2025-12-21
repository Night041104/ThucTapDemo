<?php
require_once __DIR__ . '/../../models/AttributeModel.php';

class AttributeController {
    private $attrModel;

    public function __construct() {
        $this->attrModel = new AttributeModel();
    }

    // 1. Hiển thị danh sách (Index)
    public function index() {
        $listAttrs = $this->attrModel->getAll();
        require __DIR__ . '/../views/attribute/index.php';
    }

    // 2. Form Tạo mới (Create)
    public function create() {
        // Khởi tạo dữ liệu rỗng
        $currentData = [
            'id' => '', 'code' => '', 'name' => '', 
            'options_str' => '', 'is_customizable' => 0, 'is_variant' => 0
        ];
        require __DIR__ . '/../views/attribute/form.php';
    }

    // 3. Form Sửa (Edit)
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $data = $this->attrModel->getById($id);
        
        if ($data) {
            $currentData = $data; // Model đã gộp options thành chuỗi options_str
            require __DIR__ . '/../views/attribute/form.php';
        } else {
            header("Location: index.php?module=admin&controller=attribute&action=index");
            exit;
        }
    }

    // 4. Xử lý Lưu (Save - dùng chung cho Create & Update)
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $code = trim($_POST['code']);
            $name = trim($_POST['name']);
            $optionsStr = $_POST['options'];
            $isCustom = isset($_POST['is_customizable']) ? 1 : 0;
            $isVariant = isset($_POST['is_variant']) ? 1 : 0;

            // --- Validation ---
            $error = null;
            if (empty($code) || empty($name)) {
                $error = "❌ Mã và Tên thuộc tính không được để trống!";
            } elseif ($this->attrModel->checkCodeExists($code, $id)) {
                $error = "❌ Mã thuộc tính '$code' đã tồn tại!";
            }

            // Chuyển chuỗi Options thành mảng (cắt bởi dấu phẩy)
            $optionsArr = array_map('trim', explode(',', $optionsStr));

            if ($error) {
                // Sticky Form: Nếu lỗi, load lại form với dữ liệu cũ + thông báo lỗi
                $currentData = [
                    'id' => $id, 'code' => $code, 'name' => $name, 
                    'options_str' => $optionsStr, 
                    'is_customizable' => $isCustom, 'is_variant' => $isVariant
                ];
                $msg = $error;
                require __DIR__ . '/../views/attribute/form.php';
                exit;
            }

            // Gọi Model để lưu
            if ($id) {
                $this->attrModel->update($id, $code, $name, $isCustom, $isVariant, $optionsArr);
                $msg = "updated";
            } else {
                $this->attrModel->create($code, $name, $isCustom, $isVariant, $optionsArr);
                $msg = "created";
            }

            header("Location: index.php?module=admin&controller=attribute&action=index&msg=$msg");
            exit;
        }
    }

    // 5. Xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $this->attrModel->delete($_GET['id']);
            $msg = "deleted";
        }
        header("Location: index.php?module=admin&controller=attribute&action=index&msg=deleted");
        exit;
    }
    // [CLIENT] Lấy các thuộc tính dùng để lọc (Sidebar)
    public function getFiltersByCateForClient($cateId) {
        $cateId = $this->escape($cateId);

        // A. Lấy tên thuộc tính (RAM, ROM...) có xuất hiện trong danh mục này
        // Chỉ lấy những thuộc tính được đánh dấu là biến thể (is_variant = 1)
        $sqlAttrs = "SELECT DISTINCT a.id, a.name, a.code 
                     FROM attributes a
                     JOIN product_attribute_values pav ON a.id = pav.attribute_id
                     JOIN products p ON pav.product_id = p.id
                     WHERE p.category_id = '$cateId' 
                     AND p.status = 1 
                     AND a.is_variant = 1 
                     ORDER BY a.id ASC";
        
        $rsAttrs = $this->_query($sqlAttrs);
        $attributes = $rsAttrs ? mysqli_fetch_all($rsAttrs, MYSQLI_ASSOC) : [];

        // B. Lấy các giá trị (Options) thực tế cho từng thuộc tính
        foreach ($attributes as &$attr) {
            $attrId = $attr['id'];
            $sqlOpts = "SELECT DISTINCT pav.value_text, pav.value_id, ao.value as option_value
                        FROM product_attribute_values pav
                        JOIN products p ON pav.product_id = p.id
                        LEFT JOIN attribute_options ao ON pav.value_id = ao.id
                        WHERE p.category_id = '$cateId' 
                        AND p.status = 1
                        AND pav.attribute_id = '$attrId'
                        ORDER BY ao.id ASC, pav.value_text ASC";
            
            $rsOpts = $this->_query($sqlOpts);
            $options = [];
            while($row = mysqli_fetch_assoc($rsOpts)) {
                $val = !empty($row['option_value']) ? $row['option_value'] : $row['value_text'];
                if(!in_array($val, $options) && !empty($val)) {
                    $options[] = $val;
                }
            }
            $attr['filter_options'] = $options;
        }

        return $attributes;
    }
}
?>