<?php
// تفعيل الأخطاء لمعرفة العمود المسبب للمشكلة [cite: 2026-01-27]
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php'; 

$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. جلب بيانات الفاتورة [cite: 2026-01-27]
$res_inv = mysqli_query($conn, "SELECT warehouse_id FROM invoices WHERE id = $invoice_id");
$inv_data = mysqli_fetch_assoc($res_inv);
$warehouse_id = $inv_data['warehouse_id'] ?? 0;

// 2. دالة ذكية لتحديد أسماء الأعمدة الصحيحة في جدولك [cite: 2026-01-27]
function getColumnName($conn, $table, $possible_names) {
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `$table` ");
    $existing = [];
    while($row = mysqli_fetch_assoc($res)) { $existing[] = $row['Field']; }
    foreach($possible_names as $name) { if(in_array($name, $existing)) return $name; }
    return false;
}

// تحديد الأسماء الحقيقية للأعمدة في قاعدة بياناتك [cite: 2026-01-27]
$col_code = getColumnName($conn, 'products', ['product_code', 'code', 'barcode', 'sku']) ?: 'id';
$col_balance = getColumnName($conn, 'stock_balances', ['balance', 'qty', 'current_stock', 'stock']) ?: 'product_id';

// 3. جلب الأصناف بناءً على الأعمدة التي تم إيجادها [cite: 2026-01-27]
$options = "";
$prod_sql = "SELECT p.id, p.product_name, p.$col_code as code, sb.$col_balance as bal 
             FROM products p 
             JOIN stock_balances sb ON p.id = sb.product_id 
             WHERE sb.warehouse_id = $warehouse_id";

$prod_res = mysqli_query($conn, $prod_sql) or die("خطأ في الاستعلام: " . mysqli_error($conn));

while($p = mysqli_fetch_assoc($prod_res)) {
    // بناء النص كما في الصورة (كود - اسم - رصيد)
    $display = "({$p['code']}) - {$p['product_name']} | المتوفر: " . ($p['bal'] ?? 0);
    $options .= "<option value='{$p['id']}' data-price='0'>{$display}</option>";
}

// 4. جلب أصناف الفاتورة الحالية [cite: 2026-01-27]
$sql_items = "SELECT it.*, p.product_name, p.$col_code as code 
              FROM invoice_items it 
              JOIN products p ON it.product_id = p.id 
              WHERE it.invoice_id = $invoice_id";
$res_items = mysqli_query($conn, $sql_items);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 15px; background: #fdfdfd; text-align: right; }
        .row-item { display: flex; gap: 8px; margin-bottom: 12px; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; }
        .col-p { flex: 5; } .col-q { flex: 1; } .col-pr { flex: 1.5; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%; text-align: center; }
        .btn-add { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-weight: bold; margin-bottom: 15px; }
        .btn-save { background: #007bff; color: white; border: none; padding: 15px; width: 100%; cursor: pointer; border-radius: 8px; font-size: 18px; font-weight: bold; margin-top: 20px; }
        .btn-del { background: #dc3545; color: white; border: none; width: 40px; height: 40px; cursor: pointer; border-radius: 5px; font-size: 20px; }
    </style>
</head>
<body>

<form action="update_invoice_action.php" method="POST">
    <input type="hidden" name="invoice_id" value="<?= $invoice_id ?>">
    
    <div id="items_container">
        <?php while($item = mysqli_fetch_assoc($res_items)): ?>
        <div class="row-item">
            <div class="col-p">
                <select name="product_ids[]" class="s2">
                    <option value="<?= $item['product_id'] ?>" selected>
                        (<?= $item['code'] ?>) - <?= $item['product_name'] ?>
                    </option>
                    <?= $options ?>
                </select>
            </div>
            <div class="col-q"><input type="number" name="qtys[]" value="<?= $item['qty'] ?>"></div>
            <div class="col-pr"><input type="number" step="0.01" name="prices[]" value="<?= $item['unit_price'] ?>"></div>
            <div style="width: 40px;"></div> 
        </div>
        <?php endwhile; ?>
    </div>

    <button type="button" class="btn-add" onclick="addRow()">+ إضافة صنف للفاتورة</button>
    <button type="submit" class="btn-save">حفظ التعديلات النهائية</button>
</form>

<script>
$(document).ready(function() {
    function initS2() { $('.s2').select2({ dir: "rtl", width: '100%' }); }
    initS2();

    window.addRow = function() {
        let row = `
        <div class="row-item" style="background:#f4fff4">
            <div class="col-p"><select name="product_ids[]" class="s2"><option value="">ابحث بالكود أو الاسم..</option><?= $options ?></select></div>
            <div class="col-q"><input type="number" name="qtys[]" value="1"></div>
            <div class="col-pr"><input type="number" step="0.01" name="prices[]" value="0"></div>
            <button type="button" class="btn-del" onclick="$(this).parent().remove()">×</button>
        </div>`;
        $('#items_container').append(row);
        initS2();
    };
});
</script>
</body>
</html>
