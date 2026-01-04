<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
    $total = count($listCates);
    $hasConfig = 0;
    foreach($listCates as $c) {
        $tpl = json_decode($c['spec_template'], true);
        if(!empty($tpl)) $hasConfig++;
    }
?>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng danh mục</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $total ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-success small mb-1">Đã cấu hình thông số</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $hasConfig ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Quản lý Danh mục</h4>
        <p class="text-muted small mb-0">Phân loại sản phẩm và cấu hình mẫu thông số kỹ thuật</p>
    </div>
    <a href="admin/category/create" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus-circle me-2"></i>Thêm mới
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if ($_GET['msg'] == 'created') echo "Tạo danh mục thành công!";
            elseif ($_GET['msg'] == 'updated') echo "Cập nhật thành công!";
            elseif ($_GET['msg'] == 'deleted') echo "Đã xóa danh mục.";
            else echo htmlspecialchars(urldecode($_GET['msg']));
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0">
        <div class="input-group" style="max-width: 400px;">
            <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Tìm tên danh mục...">
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" width="60">ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Slug (URL)</th>
                        <th>Cấu hình Template</th>
                        <th class="text-end pe-4" width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody id="cateTableBody">
                    <?php if(!empty($listCates)): ?>
                        <?php foreach($listCates as $c): 
                            $tpl = json_decode($c['spec_template'], true) ?? []; 
                            $countItems = 0;
                            foreach($tpl as $g) $countItems += count($g['items']);
                        ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= $c['id'] ?></td>
                            <td>
                                <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($c['name']) ?></span>
                            </td>
                            <td>
                                <code class="text-muted bg-light px-2 py-1 rounded border"><?= $c['slug'] ?></code>
                            </td>
                            <td>
                                <?php if(count($tpl) > 0): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-light me-1">
                                        <?= count($tpl) ?> nhóm
                                    </span>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-light">
                                        <?= $countItems ?> thông số
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">(Chưa cấu hình)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="admin/category/edit?id=<?= $c['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Sửa">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="admin/category/delete?id=<?= $c['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger border-0 rounded-circle ms-1" 
                                   onclick="return confirm('⚠️ CẢNH BÁO QUAN TRỌNG:\n\nXóa danh mục sẽ ảnh hưởng đến TẤT CẢ sản phẩm thuộc danh mục này.\nBạn có chắc chắn muốn xóa?')" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có danh mục nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .border-primary-light { border-color: #cce5ff !important; }
    .border-info-light { border-color: #cdf4fc !important; }
</style>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#cateTableBody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? '' : 'none';
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>