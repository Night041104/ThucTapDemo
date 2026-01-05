<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php 
    $totalRecords = isset($totalRecords) ? $totalRecords : 0;
    $totalPages   = isset($totalPages) ? $totalPages : 0;
    $page         = isset($page) ? $page : 1;
    $keyword      = isset($keyword) ? $keyword : '';
    
    // Biến thống kê
    $totalStat = isset($totalStat) ? $totalStat : 0;
    $hasLogo   = isset($hasLogo) ? $hasLogo : 0;
    $noLogo    = isset($noLogo) ? $noLogo : 0;
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-uppercase fw-bold text-primary small mb-1">Tổng thương hiệu</div>
                <div class="h3 mb-0 fw-bold text-gray-800"><?= $totalStat ?></div>
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
        <form id="filterForm" class="d-flex align-items-center" onsubmit="return false;">
            <input type="hidden" name="page" id="pageInput" value="<?= $page ?>">

            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                <input type="text" name="q" id="keyword" 
                       value="<?= htmlspecialchars($keyword) ?>"
                       class="form-control bg-light border-start-0" 
                       placeholder="Tìm tên thương hiệu...">
            </div>
            
            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary ms-2 d-none" role="status"></div>
        </form>
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
                        <tr><td colspan="5" class="text-center py-5 text-muted">Không tìm thấy thương hiệu nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center" id="pagination-container">
            <div class="small text-muted">
                Hiển thị <strong><?= count($listBrands) ?></strong> / <strong><?= $totalRecords ?></strong> kết quả
            </div>
            <?php require __DIR__ . '/../layouts/pagination.php'; ?>
        </div>
    </div>
</div>

<script>
    // [CẤU HÌNH] URL API cho trang Brand
    const API_URL = '<?= $this->baseUrl ?>admin/brand'; 
    const TABLE_BODY_ID = 'brandTableBody';

    function changePage(newPage) {
        event.preventDefault();
        const pageInput = document.getElementById('pageInput');
        if(pageInput) {
            pageInput.value = newPage;
            fetchData();
        }
    }

    function fetchData() {
        const form = document.getElementById('filterForm');
        const spinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById(TABLE_BODY_ID);
        const paginationContainer = document.getElementById('pagination-container');

        spinner.classList.remove('d-none');
        
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        const newUrl = API_URL + '?' + params.toString();
        window.history.pushState({path: newUrl}, '', newUrl);

        fetch(newUrl)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTbody = doc.getElementById(TABLE_BODY_ID);
                if(newTbody && tableBody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                }

                const newPagination = doc.getElementById('pagination-container');
                if(newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
            })
            .catch(err => console.error(err))
            .finally(() => spinner.classList.add('d-none'));
    }

    document.addEventListener("DOMContentLoaded", function() {
        const keywordInput = document.getElementById('keyword');
        let timeout = null;

        if (keywordInput) {
            keywordInput.addEventListener('input', () => {
                const pInput = document.getElementById('pageInput');
                if(pInput) pInput.value = 1; 
                
                clearTimeout(timeout);
                timeout = setTimeout(fetchData, 400); 
            });
        }
        
        window.addEventListener('popstate', function() {
            location.reload(); 
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>