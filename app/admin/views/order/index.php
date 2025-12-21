<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <style>
        /* CSS dùng chung với các trang Admin khác của bạn */
        body { font-family: sans-serif; padding: 20px; background: #f4f6f8; }
        .table-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #e3f2fd; color: #0d47a1; }
        
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; }
        .st-1 { background: #ffc107; color: black; } /* Chờ xác nhận */
        .st-2 { background: #17a2b8; } /* Đã xác nhận */
        .st-3 { background: #007bff; } /* Đang giao */
        .st-4 { background: #28a745; } /* Hoàn thành */
        .st-5 { background: #dc3545; } /* Đã hủy */

        .btn-view { background: #e3f2fd; color: #1565c0; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>

    <h1 style="color:#1565c0">📦 QUẢN LÝ ĐƠN HÀNG</h1>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $row): ?>
                    <tr>
                        <td><strong><?= $row['order_code'] ?></strong></td>
                        <td>
                            <?= htmlspecialchars($row['fullname']) ?><br>
                            <small style="color:#666"><?= $row['phone'] ?></small>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td style="color:#d32f2f; font-weight:bold;">
                            <?= number_format($row['total_money'], 0, ',', '.') ?>₫
                        </td>
                        <td>
                            <?php
                                $s = $row['status'];
                                $label = '';
                                switch($s) {
                                    case 1: $label = 'Chờ xác nhận'; break;
                                    case 2: $label = 'Đã xác nhận'; break;
                                    case 3: $label = 'Đang giao'; break;
                                    case 4: $label = 'Hoàn thành'; break;
                                    case 5: $label = 'Đã hủy'; break;
                                    default: $label = 'Không rõ';
                                }
                            ?>
                            <span class="badge st-<?= $s ?>"><?= $label ?></span>
                        </td>
                        <td>
                            <a href="index.php?module=admin&controller=order&action=detail&id=<?= $row['id'] ?>" class="btn-view">
                                Xem chi tiết ➝
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>