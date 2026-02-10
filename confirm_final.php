<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. استلام البيانات الأساسية
    $wh_id = intval($_POST['warehouse_id']);
    $cu_id = intval($_POST['customer_id']);
    $total = floatval($_POST['total_amount']);
    $inv_date = mysqli_real_escape_string($conn, $_POST['invoice_date']);

    // 2. حساب رقم الفاتورة الجديد
    $res_inv = mysqli_query($conn, "SELECT MAX(id) as last_id FROM invoices");
    $row_inv = mysqli_fetch_assoc($res_inv);
    $new_id = ($row_inv['last_id'] ?? 0) + 1;

    // 3. إدخال رأس الفاتورة (حسب أعمدة الجدول الموضحة في تقريرك) [cite: 2026-01-27]
    $sql_main = "INSERT INTO invoices (id, customer_id, warehouse_id, invoice_date, total_amount) 
                 VALUES ($new_id, $cu_id, $wh_id, '$inv_date', $total)";
    
    if (mysqli_query($conn, $sql_main)) {
        
        // 4. معالجة الأصناف [cite: 2026-01-27]
        if (isset($_POST['p_ids']) && is_array($_POST['p_ids'])) {
            foreach ($_POST['p_ids'] as $index => $p_id) {
                $p_id = intval($p_id);
                $qty  = intval($_POST['p_qtys'][$index]);
                $prc  = floatval($_POST['p_prices'][$index]);
                $item_total = $qty * $prc;

                // إدراج الأصناف باستخدام المسميات الصحيحة (qty, unit_price, total_price) [cite: 2026-01-27]
                mysqli_query($conn, "INSERT INTO invoice_items (invoice_id, product_id, qty, unit_price, total_price) 
                                     VALUES ($new_id, $p_id, $qty, $prc, $item_total)");
                
                // 5. تحديث المخزون في جدول stock_balances (qty) [cite: 2026-01-27]
                mysqli_query($conn, "UPDATE stock_balances SET qty = qty - $qty 
                                     WHERE product_id = $p_id AND warehouse_id = $wh_id");
            }
        }

        // 6. رسالة النجاح والتحويل لصفحة العملاء
        echo "<script>
                alert('تم حفظ الفاتورة بنجاح برقم: $new_id');
                window.location.href = 'clients_list.php';
              </script>";
        exit;
        
    } else {
        die("خطأ في حفظ الفاتورة: " . mysqli_error($conn));
    }
}
?>
