<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';
$ref    = intval($_GET['ref'] ?? 0);
$tbl    = $_GET['tbl'] ?? '';
$qty    = intval($_GET['qty'] ?? 0);
$price  = floatval($_GET['price'] ?? 0); // استلام السعر الجديد
$pid    = intval($_GET['pid'] ?? 0);

if ($ref == 0 || empty($tbl)) die("بيانات ناقصة");

if ($action == 'delete') {
    if ($tbl == 'purchases') { $sql = "DELETE FROM purchases WHERE id = $ref"; }
    elseif ($tbl == 'invoice_items') { $sql = "DELETE FROM invoice_items WHERE invoice_id = $ref AND product_id = $pid"; }
    elseif ($tbl == 'transfer_items') { $sql = "DELETE FROM transfer_items WHERE transfer_id = $ref AND product_id = $pid"; }
} 

elseif ($action == 'edit') {
    // تحديث الكمية والسعر بناءً على الجدول
    if ($tbl == 'purchases') {
        $sql = "UPDATE purchases SET qty = $qty, price = $price WHERE id = $ref";
    } elseif ($tbl == 'invoice_items') {
        $sql = "UPDATE invoice_items SET qty = $qty, unit_price = $price, total_price = ($qty * $price) WHERE invoice_id = $ref AND product_id = $pid";
    } elseif ($tbl == 'transfer_items') {
        $sql = "UPDATE transfer_items SET qty = $qty WHERE transfer_id = $ref AND product_id = $pid";
    }
}

if (mysqli_query($conn, $sql)) {
    header("Location: item_details.php?id=$pid&msg=success");
} else {
    echo "خطأ: " . mysqli_error($conn);
}
?>
