<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
    $total = count($listBrands);
    $hasLogo = 0;
    foreach($listBrands as $b) {
        if(!empty($b['logo_url'])) $hasLogo++;
    }
    $noLogo = $total - $hasLogo;
?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng thương hiệu</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $total ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-success small mb-1">Có Logo</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $hasLogo ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #858796 !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-secondary small mb-1">Chưa có Logo</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $noLogo ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Quản lý Thương hiệu</h4>
        <p class="text-muted small mb-0">Danh sách các hãng sản xuất / nhà cung cấp</p>
    </div>
    <a href="admin/brand/create" class="btn btn-primary shadow-sm px-3">
        <i class="fa fa-plus-circle me-2"></i>Thêm mới
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if ($_GET['msg'] == 'created') echo "Tạo thương hiệu thành công!";
            elseif ($_GET['msg'] == 'updated') echo "Cập nhật thành công!";
            elseif ($_GET['msg'] == 'deleted') echo "Đã xóa thương hiệu.";
            else echo htmlspecialchars(urldecode($_GET['msg']));
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0">
        <div class="input-group" style="max-width: 400px;">
            <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Tìm tên thương hiệu...">
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" width="60">ID</th>
                        <th width="100">Logo</th>
                        <th>Tên Thương hiệu</th>
                        <th>Slug (URL)</th>
                        <th class="text-end pe-4" width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody id="brandTableBody">
                    <?php if(!empty($listBrands)): ?>
                        <?php foreach($listBrands as $row): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $row['id'] ?></td>
                                <td>
                                    <?php if(!empty($row['logo_url'])): ?>
                                        <div class="bg-white p-1 border rounded d-inline-block">
                                            <img src="<?= $row['logo_url'] ?>" style="width: 40px; height: 40px; object-fit: contain;">
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center small" style="width: 40px; height: 40px;">
                                            <i class="fa fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($row['name']) ?></span>
                                </td>
                                <td>
                                    <code class="text-muted bg-light px-2 py-1 rounded"><?= $row['slug'] ?></code>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="admin/brand/edit?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Sửa">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a href="admin/brand/delete?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0 rounded-circle ms-1" 
                                       onclick="return confirm('⚠️ Cảnh báo: Xóa thương hiệu này có thể ảnh hưởng đến sản phẩm liên quan.\n\nBạn có chắc chắn muốn xóa?')" title="Xóa">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có thương hiệu nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#brandTableBody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? '' : 'none';
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>