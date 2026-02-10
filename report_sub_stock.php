<?php
require_once 'config.php';

// معالجة تحديث الكمية والسعر (النموذج الأصلي)
if(isset($_POST['update_stock'])) {
    $id = intval($_POST['item_id']);
    $new_qty = floatval($_POST['remaining_qty']);
    $new_price = floatval($_POST['price']);
    
    $sql_update = "UPDATE stock_balances SET qty = $new_qty, price = $new_price WHERE id = $id";
    if(mysqli_query($conn, $sql_update)) {
        echo "<script>alert('تم تحديث البيانات بنجاح'); window.location='report_sub_stock.php';</script>";
    }
}

// جلب البيانات
$sql = "SELECT s.*, p.product_name, p.product_code 
        FROM stock_balances s 
        JOIN products p ON s.product_id = p.id 
        WHERE s.warehouse_id = 2";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المخزن الفرعي - شركة أبو حريرة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f7f6; font-weight: 900; }
        .table-container { background: white; padding: 25px; border-radius: 15px; margin-top: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th { background-color: #0dcaf0 !important; color: white; text-align: center; border: none; }
        td { text-align: center; vertical-align: middle; border-bottom: 1px solid #eee; }
        .btn-log { border: none; background: #eef2f7; padding: 5px 12px; border-radius: 8px; font-weight: 900; cursor: pointer; transition: 0.3s; width: 100%; }
        .btn-log:hover { background: #dee2e6; transform: scale(1.05); }
        .qty-transfer { color: #0d6efd; border-right: 4px solid #0d6efd; }
        .qty-invoice { color: #dc3545; border-right: 4px solid #dc3545; }

        /* التعديلات الجديدة للنافذة المنبثقة لتظهر في الأعلى */
        .modal.fade .modal-dialog {
            transform: translate(0, -50px); /* تبدأ من أعلى قليلًا */
            transition: transform 0.3s ease-out;
            margin-top: 20px; /* المسافة من سقف الشاشة */
        }
        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }
        
        /* تنسيق زر الإغلاق (X) في اليمين */
        .modal-header {
            display: flex;
            flex-direction: row-reverse; /* عكس الاتجاه لجعل الزر يميناً والنص يساراً */
            justify-content: space-between;
            align-items: center;
            background: #212529;
            color: white;
        }
        .btn-close-white {
            margin: 0 !important; /* إلغاء الهوامش الافتراضية */
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="table-container">
        <h2 class="text-center mb-4"><i class="fa fa-truck-ramp-box text-info"></i> تقرير حركة بضاعة المخزن الفرعي</h2>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 25%;">الصنف</th>
                    <th>الكمية المحولة</th>
                    <th>فواتير (المباع)</th>
                    <th>المتبقي الحالي</th>
                    <th>السعر</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $p_id = $row['product_id'];
                    $t_sum_sql = "SELECT SUM(qty) as total FROM transfer_items ti JOIN transfers t ON ti.transfer_id = t.id WHERE ti.product_id = $p_id AND t.to_warehouse_id = 2";
                    $total_trans = mysqli_fetch_assoc(mysqli_query($conn, $t_sum_sql))['total'] ?? 0;

                    $i_sum_sql = "SELECT SUM(qty) as total FROM invoice_items ii JOIN invoices i ON ii.invoice_id = i.id WHERE ii.product_id = $p_id AND i.warehouse_id = 2";
                    $total_inv = mysqli_fetch_assoc(mysqli_query($conn, $i_sum_sql))['total'] ?? 0;
                ?>
                <tr>
                    <td class="text-end"><strong><?= $row['product_code'] ?></strong> - <?= $row['product_name'] ?></td>
                    <td><button class="btn-log qty-transfer" onclick="showLogs('transfers', <?= $p_id ?>)"><?= $total_trans ?></button></td>
                    <td><button class="btn-log qty-invoice" onclick="showLogs('invoices', <?= $p_id ?>)"><?= $total_inv ?></button></td>
                    <td><span class="badge bg-success p-2 fs-6"><?= $row['qty'] ?></span></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm fw-bold" onclick="openEditModal(<?= $row['id'] ?>, <?= $row['qty'] ?>, <?= $row['price'] ?>)">تعديل</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title" id="logTitle">تفاصيل الحركة</h5>
            </div>
            <div id="logBody" class="modal-body p-0">
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                <h5 class="modal-title fw-bold">تعديل الصنف</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="item_id" id="modal_id">
                <div class="mb-3"><label>الكمية المتبقية</label><input type="number" name="remaining_qty" id="modal_qty" class="form-control" required></div>
                <div class="mb-3"><label>السعر</label><input type="number" step="0.01" name="price" id="modal_price" class="form-control" required></div>
            </div>
            <div class="modal-footer"><button type="submit" name="update_stock" class="btn btn-dark w-100">حفظ</button></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showLogs(type, productId) {
    const title = type === 'transfers' ? 'سجل التحويلات الواردة (بالتاريخ)' : 'سجل الفواتير الصادرة (بالتاريخ)';
    document.getElementById('logTitle').innerText = title;
    
    // إظهار رسالة تحميل
    document.getElementById('logBody').innerHTML = '<div class="p-3 text-center">جاري جلب السجلات...</div>';
    
    // فتح النافذة
    var myModal = new bootstrap.Modal(document.getElementById('logModal'));
    myModal.show();

    fetch('fetch_details.php?type=' + type + '&product_id=' + productId)
        .then(r => r.text())
        .then(data => {
            document.getElementById('logBody').innerHTML = `
                <table class="table mb-0 text-center table-striped">
                    <thead class="table-light">
                        <tr><th>التاريخ</th><th>الرقم</th><th>${type==='transfers'?'المصدر':'العميل'}</th><th>الكمية</th></tr>
                    </thead>
                    <tbody>${data || '<tr><td colspan="4">لا توجد سجلات</td></tr>'}</tbody>
                </table>`;
        })
        .catch(err => {
            document.getElementById('logBody').innerHTML = '<div class="p-3 text-center text-danger">حدث خطأ أثناء الاتصال بالملف</div>';
        });
}

function openEditModal(id, qty, price) {
    document.getElementById('modal_id').value = id;
    document.getElementById('modal_qty').value = qty;
    document.getElementById('modal_price').value = price;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
</body>
</html>
