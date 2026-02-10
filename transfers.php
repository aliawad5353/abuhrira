<?php
include 'dashboard.php';
require_once 'config.php';

// جلب المخازن
$warehouses = mysqli_query($conn, "SELECT * FROM warehouses");
$selected_source = $_POST['source_warehouse'] ?? null;
?>

<div class="main-content">
    <div class="container-fluid">
        <?php if (!$selected_source): ?>
        <div class="card p-5 shadow-lg border-dark">
            <h2 class="text-center mb-5" style="font-weight: 900;">تحويل بضاعة بين المخازن</h2>
            <form method="POST">
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-6">
                        <label class="fs-4 mb-3">اختر المخزن المراد التحويل منه:</label>
                        <select name="source_warehouse" class="form-control fw-bold" required>
                            <option value="">--- اختر مخزن ---</option>
                            <option value="1">المخزن الرئيسي</option>
                            <option value="2">المخزن الفرعي</option>
                            <option value="3">المعرض</option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">استمر <i class="fa fa-arrow-left"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <?php else: 
            $source_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM warehouses WHERE id = $selected_source"))['name'];
        ?>
        <div class="card p-5 shadow-lg" style="border-radius: 20px; border: 2px solid #2c3e50;">
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
                <h2 style="font-weight: 900;">تحويل بضاعة بين المخازن</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold">تغيير المخزن:</span>
                    <form method="POST" class="d-flex gap-2">
                        <select name="source_warehouse" class="form-control fw-bold py-1" style="height: 40px !important;">
                            <option value="1" <?php if($selected_source == 1) echo 'selected'; ?>>المخزن الرئيسي</option>
                            <option value="2" <?php if($selected_source == 2) echo 'selected'; ?>>المخزن الفرعي</option>
                            <option value="3" <?php if($selected_source == 3) echo 'selected'; ?>>المعرض</option>
                        </select>
                        <button type="submit" class="btn btn-dark fw-bold">استمر</button>
                    </form>
                </div>
            </div>

            <form action="save_transfer.php" method="POST" id="transferForm">
                <input type="hidden" name="from_warehouse" value="<?php echo $selected_source; ?>">

                <div class="row mb-5">
                    <div class="col-md-4">
                        <label class="mb-2">التاريخ:</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="day" class="form-control text-center fw-bold" value="<?php echo date('d'); ?>">
                            <input type="number" name="month" class="form-control text-center fw-bold" value="<?php echo date('m'); ?>">
                            <input type="number" name="year" class="form-control text-center fw-bold" value="2025">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="mb-2">من:</label>
                        <input type="text" class="form-control fw-bold bg-light text-primary" value="<?php echo $source_name; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="mb-2">إلى:</label>
                        <select name="to_warehouse" class="form-control fw-bold border-danger" required>
                            <option value="">اختر المخزن المحول له...</option>
                            <?php 
                            $targets = mysqli_query($conn, "SELECT * FROM warehouses WHERE id != $selected_source");
                            while($t = mysqli_fetch_assoc($targets)): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div id="transfer_items">
                    <div class="row mb-4 transfer-row align-items-end p-3 bg-light rounded shadow-sm">
                        <div class="col-md-2">
                            <label class="small fw-bold">الكمية</label>
                            <input type="number" name="qty[]" class="form-control fw-bold" placeholder="0" required>
                        </div>
                        <div class="col-md-10">
                            <label class="small fw-bold">الصنف (كود - اسم - المتوفر)</label>
                            <select name="product_id[]" class="form-control select2-transfer" required>
                                <option value="">ابحث في أصناف <?php echo $source_name; ?>...</option>
                                <?php 
                                // جلب الأصناف المتوفرة فقط في هذا المخزن
                                $stock = mysqli_query($conn, "SELECT p.*, s.qty FROM products p JOIN stock_balances s ON p.id = s.product_id WHERE s.warehouse_id = $selected_source AND s.qty > 0");
                                while($s = mysqli_fetch_assoc($stock)): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['product_code']." - ".$s['product_name']." (المتوفر: ".$s['qty'].")"; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4"><button type="button" onclick="addTransferRow()" class="btn btn-outline-primary btn-lg w-100 fw-bold">إضافة صنف</button></div>
                    <div class="col-md-4"><button type="button" onclick="removeTransferRow()" class="btn btn-outline-danger btn-lg w-100 fw-bold">حذف صنف</button></div>
                    <div class="col-md-4"><button type="submit" id="saveBtn" class="btn btn-success btn-lg w-100 fw-bold shadow">حفظ التحويل</button></div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() { initSelect2(); });
function initSelect2() { $('.select2-transfer').select2({ dir: "rtl", width: '100%' }); }

function addTransferRow() {
    let row = document.querySelector('.transfer-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelector('.select2-container').remove();
    document.getElementById('transfer_items').appendChild(row);
    initSelect2();
}
function removeTransferRow() {
    let rows = document.querySelectorAll('.transfer-row');
    if(rows.length > 1) rows[rows.length-1].remove();
}

document.getElementById('transferForm').onsubmit = function() {
    let btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = 'جاري المعالجة والخصم من المخزن...';
};
</script>

<style>
    .form-control { border: 2px solid #ccc !important; border-radius: 8px !important; height: 50px !important; margin-bottom: 0 !important; }
    label { font-weight: 900 !important; color: #333; }
    .select2-selection { height: 50px !important; border: 2px solid #ccc !important; }
    .select2-selection__rendered { line-height: 50px !important; font-weight: bold !important; }
</style>