<?php
include 'dashboard.php';
require_once 'config.php';

// استلام البيانات وتأمينها
$selected_customer = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : (isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null);
$selected_warehouse = isset($_POST['warehouse_id']) ? intval($_POST['warehouse_id']) : null;

$warehouses = mysqli_query($conn, "SELECT * FROM warehouses");
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="main-content" dir="rtl" style="text-align: right; font-family: 'Segoe UI', Tahoma, sans-serif;">
    <div class="container-fluid">

        <?php if (!$selected_warehouse): ?>
        <div class="card p-4 shadow-lg border-0" style="border-radius: 15px; background: #f8f9fa;">
            <form method="POST">
                <input type="hidden" name="customer_id" value="<?= $selected_customer ?>">
                <h1 class="mb-4 text-center" style="font-weight: 900; color: #2c3e50;">اختر مخزن السحب</h1>
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-3">
                        <select name="warehouse_id" class="form-control fw-bold shadow-sm" required style="height: 60px; font-size: 20px;">
                            <option value="">--- حدد المخزن ---</option>
                            <?php while($wh = mysqli_fetch_assoc($warehouses)): ?>
                                <option value="<?= $wh['id'] ?>"><?= $wh['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" style="height: 60px;">استمر</button>
                    </div>
                </div>
            </form>
        </div>

        <?php else: 
            $wh_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM warehouses WHERE id = $selected_warehouse"));
            $cu_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM customers WHERE id = $selected_customer"));
        ?>
        <div class="card p-3 shadow-sm border-0" style="background: #fff; min-height: 85vh;">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h1 style="font-weight: 900; color: #000; font-size: 28px;">فاتورة بيع</h1>
                <span class="text-danger fw-bold">المخزن الحالي: <?= $wh_info['name'] ?></span>
            </div>

            <form action="save_invoice.php" method="POST" id="invoiceForm">
                <input type="hidden" name="warehouse_id" value="<?= $selected_warehouse ?>">
                <input type="hidden" name="customer_id" value="<?= $selected_customer ?>">

                <div id="items_container">
                    <div class="row g-2 mb-2 item-row align-items-end border-bottom pb-3">
                        <div class="col-md-2">
                            <label class="fw-bold mb-1 small">الكمية</label>
                            <input type="number" name="qty[]" class="form-control text-center fw-bold" value="1" min="1">
                        </div>
                        <div class="col-md-8">
                            <label class="fw-bold mb-1 text-primary small">ابحث عن صنف</label>
                            <select name="product_id[]" class="form-control select2-searchable" required>
                                <option value="">--- اكتب اسم الصنف أو الكود ---</option>
                                <?php 
                                // استعلام يجلب الرصيد من المخزن الحالي والسعر من المخزن الرئيسي (ID: 1)
                                $stock_sql = "SELECT p.id, p.product_code, p.product_name, 
                                              (SELECT qty FROM stock_balances WHERE product_id = p.id AND warehouse_id = $selected_warehouse) as current_qty,
                                              (SELECT price FROM stock_balances WHERE product_id = p.id AND price > 0 ORDER BY (warehouse_id = 1) DESC, price DESC LIMIT 1) as main_price
                                              FROM products p";
                                
                                $stock_res = mysqli_query($conn, $stock_sql);
                                while($st = mysqli_fetch_assoc($stock_res)): 
                                    $q = $st['current_qty'] ?? 0;
                                    $p = $st['main_price'] ?? 0;
                                ?>
                                    <option value="<?= $st['id'] ?>">
                                        <?= $st['product_code'] ?> - <?= $st['product_name'] ?> (المتوفر هنا: <?= $q ?> | السعر: <?= number_format($p, 2) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2 delete-btn-container"></div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4"><button type="submit" class="btn btn-info w-100 text-white fw-bold py-3">استخراج ومعاينة الفاتورة</button></div>
                    <div class="col-md-4"><button type="button" onclick="addRow()" class="btn btn-success w-100 fw-bold py-3">إضافة صنف +</button></div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function activateSearch() {
    $('.select2-searchable').select2({ dir: "rtl", placeholder: "ابحث عن صنف...", width: '100%' });
}
function addRow() {
    let container = document.getElementById('items_container');
    let firstRow = document.querySelector('.item-row');
    let newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(i => i.value = '1');
    if (newRow.querySelector('.select2-container')) { newRow.querySelector('.select2-container').remove(); }
    let deleteContainer = newRow.querySelector('.delete-btn-container');
    deleteContainer.innerHTML = '<button type="button" onclick="this.parentElement.parentElement.remove()" class="btn btn-outline-danger w-100 fw-bold" style="height: 40px;">حذف</button>';
    container.appendChild(newRow);
    activateSearch();
}
$(document).ready(function() { activateSearch(); });
</script>
