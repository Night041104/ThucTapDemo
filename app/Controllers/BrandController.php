<?php
class BrandController extends BaseController {
    private $model;
    private $cateModel;

    public function __construct() {
        $this->model = new BrandModel();
        $this->cateModel = new CategoryModel();
    }

    public function index() {
        $categories = $this->cateModel->getAll();
        $this->view('brand/index', ['categories' => $categories]);
    }

    public function store() {
        $this->model->create($_POST['name'], $_POST['cate_ids'] ?? []);
        echo "<script>alert('Đã tạo hãng thành công!'); window.location.href='index.php?act=brand_setup';</script>";
    }
}
?>