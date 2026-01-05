<?php if (isset($totalPages) && $totalPages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0 justify-content-end">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" onclick="changePage(<?= $page - 1 ?>)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="#" onclick="changePage(<?= $i ?>)"><?= $i ?></a>
                </li>
            <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endfor; ?>

        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" onclick="changePage(<?= $page + 1 ?>)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<div class="small text-muted text-end mt-1">
    Trang <?= $page ?> / <?= $totalPages ?>
</div>
<?php endif; ?>