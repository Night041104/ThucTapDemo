<?php
class AttributeController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new AttributeModel();
    }

    public function index() {
        $data = [];
        if (isset($_GET['edit'])) {
            $data = $this->model->getById($_GET['edit']);
            // Lấy options dạng chuỗi
            $opts = [];
            foreach ($this->model->getAllWithOptions()[$data['id']]['options'] as $o) $opts[] = $o['value'];
            $data['options_str'] = implode(',', $opts);
        }
        
        $list = $this->model->getAllWithOptions();
        $this->view('attribute/index', ['list' => $list, 'current' => $data]);
    }

    public function store() {
        $this->model->save([
            'id' => $_POST['id'] ?? null,
            'code' => $_POST['code'],
            'name' => $_POST['name'],
            'is_customizable' => isset($_POST['is_customizable']) ? 1 : 0,
            'options' => $_POST['options']
        ]);
        $this->redirect('index.php?act=attributes');
    }

    public function delete() {
        if (isset($_GET['id'])) $this->model->delete($_GET['id']);
        $this->redirect('index.php?act=attributes');
    }
}
?>