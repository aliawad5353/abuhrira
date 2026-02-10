<?php
include 'dashboard.php';
require_once 'config.php';

// جلب المخازن للقوائم
$warehouses_query = mysqli_query($conn, "SELECT * FROM warehouses");
$warehouses = [];
while($w = mysqli_fetch_assoc($warehouses_query)) { $warehouses[] = $w; }

// تحديد المخزن المصدر إذا تم اختياره
$selected_source = isset($_POST['from_warehouse']) ? intval($_POST['from_warehouse']) : null;
?>

<div class="main-content">
    <div class="container-fluid">
        
        <?php if (!$selected_source): ?>
        <div class="card p-5 shadow-lg border-primary" style="border-radius: 20px; border-width: 3px;">
            <h2 class="text-center mb-5" style="font-weight: 900; color: #2c3e50;">
                <i class="fa fa-warehouse ms-2"></i> خطوة 1: تحديد مخزن المصدر
            </h2>
            <form method="POST">
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-7">
                        <label class="fs-4 mb-3 fw-bold text-primary text-center d-block">من أي مخزن تريد تحويل البضاعة؟</label>
                        <select name="from_warehouse" class="form-control text-center fw-bold" required style="height: 70px; font-size: 24px;">
                            <option value="">--- اضغط هنا لاختيار المخزن ---</option>
                            <?php foreach($warehouses as $wh): ?>
                                <option value="<?php echo $wh['id']; ?>"><?php echo $wh['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow" style="height: 70px; font-size: 22px;">
                            استمر للتحويل <i class="fa fa-arrow-left me-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php else: 
            $source_name = "";
            foreach($warehouses as $wh) { if($wh['id'] == $selected_source) $source_name = $wh['name']; }
        ?>
        <div class="card p-5 shadow-lg" style="border-radius: 20px; border: 2px solid #2c3e50;">
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
                <h2 style="font-weight: 900; color: #2c3e50;">
                    <i class="fa fa-exchange-alt"></i> تحويل من: <span class="text-danger"><?php echo $source_name; ?></span>
                </h2>
                <a href="transfers.php" class="btn btn-dark btn-lg fw-bold shadow-sm">
                    <i class="fa fa-sync-alt ms-1"></i> تغيير مخزن المصدر
                </a>
            </div>

            <form action="save_transfer.php" method="POST" id="transferForm">
                <input type="hidden" name="from_warehouse" value="<?php echo $selected_source; ?>">

                <div class="row mb-5 align-items-end">
                    <div class="col-md-4">
                        <label class="mb-2 fs-5">تاريخ التحويل:</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="day" class="form-control text-center fw-bold" value="<?php echo date('d'); ?>">
                            <input type="number" name="month" class="form-control text-center fw-bold" value="<?php echo date('m'); ?>">
                            <input type="number" name="year" class="form-control text-center fw-bold" value="2026">
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="mb-2 fs-5 text-success fw-bold">إلى (اختر المخزن المستلم):</label>
                        <select name="to_warehouse" class="form-control fw-bold border-success" required style="height: 60px;">
                            <option value="">--- اختر المخزن الهدف ---</option>
                            <?php foreach($warehouses as $wh): if($wh['id'] == $selected_source) continue; ?>
                                <option value="<?php echo $wh['id']; ?>"><?php echo $wh['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="transfer_items_container">
                    <div class="row mb-4 transfer-row align-items-center bg-white p-3 rounded shadow-sm border">
                        <div class="col-md-2">
                            <label class="small fw-bold">الكمية</label>
                            <input type="number" name="qty[]" class="form-control fw-bold text-center" placeholder="0" required>
                        </div>
                        <div class="col-md-10">
                            <label class="small fw-bold">الصنف (المتوفر في <?php echo $source_name; ?>)</label>
                            <select name="product_id[]" class="form-control select2-transfer" required>
                                <option value="">--- ابحث عن صنف ---</option>
                                <?php 
                                $sql_s = "SELECT p.*, s.qty FROM products p 
                                          JOIN stock_balances s ON p.id = s.product_id 
                                          WHERE s.warehouse_id = $selected_source AND s.qty > 0";
                                $res_s = mysqli_query($conn, $sql_s);
                                while($p = mysqli_fetch_assoc($res_s)): ?>
                                    <option value="<?php echo $p['id']; ?>">
                                        <?php echo $p['product_code']." - ".$p['product_name']." (المتوفر: ".$p['qty'].")"; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4">
                        <button type="button" onclick="addTransferRow()" class="btn btn-primary btn-lg w-100 fw-bold shadow" style="height: 65px;"><i class="fa fa-plus"></i> إضافة صنف</button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" onclick="removeTransferRow()" class="btn btn-danger btn-lg w-100 fw-bold shadow" style="height: 65px;"><i class="fa fa-trash"></i> حذف صنف</button>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="saveBtn" class="btn btn-success btn-lg w-100 fw-bold shadow" style="height: 65px;"><i class="fa fa-check-circle"></i> حفظ التحويل</button>
                    </div>
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
function initSelect2() {
    $('.select2-transfer').select2({ dir: "rtl", width: '100%' });
}

function addTransferRow() {
    let container = document.getElementById('transfer_items_container');
    let firstRow = document.querySelector('.transfer-row');
    let newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(i => i.value = '');
    if (newRow.querySelector('.select2-container')) { newRow.querySelector('.select2-container').remove(); }
    container.appendChild(newRow);
    initSelect2();
}

function removeTransferRow() {
    let rows = document.querySelectorAll('.transfer-row');
    if (rows.length > 1) rows[rows.length - 1].remove();
}

$(document).ready(function() { initSelect2(); });

document.getElementById('transferForm').onsubmit = function() {
    let btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = 'جاري التحويل...';
};
</script>

<style>
    .form-control { height: 55px !important; font-size: 18px !important; border: 2px solid #ced4da !important; }
    label { font-weight: 900 !important; color: #2c3e50; }
    .select2-selection { height: 55px !important; border: 2px solid #ced4da !important; }
    .select2-selection__rendered { line-height: 55px !important; font-weight: bold !important; font-size: 18px !important; }
</style>
