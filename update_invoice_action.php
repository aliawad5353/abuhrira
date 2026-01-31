<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_id = $_POST['invoice_id'];
    $store_id   = $_POST['store_id'];
    $p_ids      = $_POST['product_id'];
    $qtys       = $_POST['qty'];

    // الخطوة 1: إرجاع الكميات القديمة للمخزن قبل حذفها
    $old_items = $conn->query("SELECT product_id, quantity FROM invoice_items WHERE invoice_id = $invoice_id");
    while ($old = $old_items->fetch_assoc()) {
        $pid = $old['product_id'];
        $qty = $old['quantity'];
        $conn->query("UPDATE stock_balances SET quantity = quantity + $qty WHERE product_id = $pid AND store_id = $store_id");
    }

    // الخطوة 2: حذف تفاصيل الفاتورة القديمة
    $conn->query("DELETE FROM invoice_items WHERE invoice_id = $invoice_id");

    // الخطوة 3: إضافة الأصناف الجديدة وخصم كمياتها الجديدة من المخزن
    $new_grand_total = 0;
    for ($i = 0; $i < count($p_ids); $i++) {
        $pid = $p_ids[$i];
        $qty = $qtys[$i];

        // جلب السعر الحالي للصنف من المخزن
        $price_query = $conn->query("SELECT price FROM stock_balances WHERE product_id = $pid AND store_id = $store_id");
        $price_data = $price_query->fetch_assoc();
        $unit_price = $price_data['price'];
        $new_grand_total += ($unit_price * $qty);

        // إدراج الصنف الجديد في تفاصيل الفاتورة
        $conn->query("INSERT INTO invoice_items (invoice_id, product_id, quantity, price) VALUES ('$invoice_id', '$pid', '$qty', '$unit_price')");

        // خصم الكمية الجديدة من المخزن
        $conn->query("UPDATE stock_balances SET quantity = quantity - $qty WHERE product_id = $pid AND store_id = $store_id");
    }

    // الخطوة 4: تحديث إجمالي الفاتورة في الجدول الرئيسي
    $conn->query("UPDATE invoices SET total_amount = '$new_grand_total' WHERE id = $invoice_id");

    echo "<script>alert('تم تعديل الفاتورة وتحديث المخزن بنجاح'); window.location.href='customers_list.php';</script>";
}
?>
