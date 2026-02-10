<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_id = intval($_POST['invoice_id']);
    $product_ids = $_POST['product_ids'];
    $qtys = $_POST['qtys'];
    $prices = $_POST['prices'];

    // 1. حذف الأصناف القديمة للفاتورة لإعادة بنائها (أو يمكنك التحديث المباشر)
    mysqli_query($conn, "DELETE FROM invoice_items WHERE invoice_id = $invoice_id");

    $total_invoice_amount = 0;

    // 2. إدخال الأصناف الجديدة وحساب الإجمالي
    for ($i = 0; $i < count($product_ids); $i++) {
        $p_id = intval($product_ids[$i]);
        $qty = floatval($qtys[$i]);
        $price = floatval($prices[$i]);
        $subtotal = $qty * $price;
        $total_invoice_amount += $subtotal;

        if ($p_id > 0) {
            $sql_item = "INSERT INTO invoice_items (invoice_id, product_id, qty, unit_price, total_price) 
                         VALUES ($invoice_id, $p_id, $qty, $price, $subtotal)";
            mysqli_query($conn, $sql_item);
        }
    }

    // 3. تحديث الإجمالي في جدول الفواتير الرئيسي لمنع ظهور "صفر"
    // تأكد من اسم العمود (total_amount أو invoice_total) حسب قاعدة بياناتك
    $update_main = "UPDATE invoices SET total_amount = $total_invoice_amount WHERE id = $invoice_id";
    mysqli_query($conn, $update_main);

    // 4. جلب معرف العميل لإعادة التوجيه لصفحته
    $res_cust = mysqli_query($conn, "SELECT customer_id FROM invoices WHERE id = $invoice_id");
    $cust_data = mysqli_fetch_assoc($res_cust);
    $customer_id = $cust_data['customer_id'];

    // 5. إعادة التوجيه لصفحة العميل (استبدل customer_details.php باسم ملفك)
    echo "<script>
            alert('تم تحديث الفاتورة بنجاح. المجموع الجديد: $total_invoice_amount');
            window.location.href = 'customer_details.php?id=$customer_id';
          </script>";
}
?>
