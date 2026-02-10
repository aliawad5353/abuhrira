<?php
include 'dashboard.php';
require_once 'config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id == 0) die("خطأ في معرف الصنف");

// جلب بيانات الصنف للعنوان
$res_p = mysqli_query($conn, "SELECT product_code, product_name FROM products WHERE id = $product_id");
$p_data = mysqli_fetch_assoc($res_p);

// الاستعلام البسيط الذي يعمل (مع إضافة جلب السعر) [cite: 2026-01-27]
$sql_in = "SELECT 'دخول' as move_type, s.qty as amount, s.price as unit_p, s.entry_date as move_date, w.name as wh_name, 'توريد مخزني' as notes, s.id as ref_no, 'stock_balances' as tbl
           FROM stock_balances s
           LEFT JOIN warehouses w ON s.warehouse_id = w.id
           WHERE s.product_id = $product_id AND s.qty > 0";

$sql_out = "SELECT 'خروج' as move_type, it.qty as amount, it.unit_price as unit_p, inv.invoice_date as move_date, w.name as wh_name, CONCAT('فاتورة رقم: ', inv.id) as notes, inv.id as ref_no, 'invoice_items' as tbl
            FROM invoice_items it
            JOIN invoices inv ON it.invoice_id = inv.id
            LEFT JOIN warehouses w ON inv.warehouse_id = w.id
            WHERE it.product_id = $product_id";

$sql_combined = "($sql_in) UNION ($sql_out) ORDER BY move_date DESC";
$res_moves = mysqli_query($conn, $sql_combined);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-4 shadow-lg" style="border-radius: 20px;">
            <h2 class="text-center mb-5" style="font-weight: 900;">
                <?php echo $p_data['product_code'] . " - " . $p_data['product_name']; ?>
            </h2>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle border-dark">
                    <thead class="table-dark">
                        <tr>
                            <th>الفاتورة</th>
                            <th>التاريخ</th>
                            <th>الحركة</th>
                            <th>المخزن</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>تعديل</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody style="font-weight: 800; font-size: 17px;">
                        <?php while($move = mysqli_fetch_assoc($res_moves)): ?>
                        <tr>
                            <td class="text-primary">#<?php echo $move['ref_no']; ?></td>
                            <td><?php echo $move['move_date']; ?></td>
                            <td>
                                <span class="badge <?php echo ($move['move_type'] == 'دخول') ? 'bg-success' : 'bg-danger'; ?> p-2">
                                    <?php echo $move['move_type']; ?>
                                </span>
                            </td>
                            <td><?php echo $move['wh_name'] ?? '---'; ?></td>
                            <td><?php echo $move['amount']; ?></td>
                            <td class="text-info"><?php echo number_format($move['unit_p'], 2); ?></td>
                            <td>
                                <button onclick="openEditModal(<?php echo $move['ref_no']; ?>, '<?php echo $move['tbl']; ?>', <?php echo $move['amount']; ?>, <?php echo $move['unit_p']; ?>)" class="btn btn-sm btn-outline-dark">تعديل</button>
                            </td>
                            <td>
                                <button onclick="deleteMove(<?php echo $move['ref_no']; ?>, '<?php echo $move['tbl']; ?>')" class="btn btn-sm btn-danger">حذف</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div class="modal-content bg-white p-4 shadow" style="margin:10% auto; width:350px; border-radius:15px; position:relative;">
        <h5 class="mb-4 border-bottom pb-2">تعديل البيانات</h5>
        <div class="mb-3 text-start">
            <label class="fw-bold mb-1">السعر:</label>
            <input type="number" id="modal_price" class="form-control mb-3" step="0.01">
            <label class="fw-bold mb-1">الكمية:</label>
            <input type="number" id="modal_qty" class="form-control">
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button onclick="saveChanges()" class="btn btn-success w-50 me-1">تحديث</button>
            <button onclick="document.getElementById('editModal').style.display='none'" class="btn btn-secondary w-50 ms-1">إلغاء</button>
        </div>
    </div>
</div>

<script>
let editRef, editTbl;

function openEditModal(ref, tbl, qty, price) {
    editRef = ref; editTbl = tbl;
    document.getElementById('modal_qty').value = qty;
    document.getElementById('modal_price').value = price;
    document.getElementById('editModal').style.display = 'block';
}

function saveChanges() {
    let q = document.getElementById('modal_qty').value;
    let p = document.getElementById('modal_price').value;
    window.location.href = `process_action.php?action=edit&ref=${editRef}&tbl=${editTbl}&qty=${q}&price=${p}&pid=<?php echo $product_id; ?>`;
}

function deleteMove(ref, tbl) {
    if(confirm('هل أنت متأكد من الحذف؟')) {
        window.location.href = `process_action.php?action=delete&ref=${ref}&tbl=${tbl}&pid=<?php echo $product_id; ?>`;
    }
}
</script>
