<?php
require_once 'config.php';

if(isset($_POST['warehouse_id'])) {
    $wh_id = intval($_POST['warehouse_id']);
    
    // جلب الأصناف التي لها رصيد في هذا المخزن فقط
    $query = "SELECT p.id, p.product_code, p.product_name, s.qty 
              FROM stock_balances s 
              JOIN products p ON s.product_id = p.id 
              WHERE s.warehouse_id = $wh_id AND s.qty > 0";
              
    $result = mysqli_query($conn, $query);
    
    echo '<option value="">--- اختر الصنف (المتوفر) ---</option>';
    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="'.$row['id'].'">'.$row['product_code'].' - '.$row['product_name'].' (المتوفر: '.$row['qty'].')</option>';
    }
}
?>
