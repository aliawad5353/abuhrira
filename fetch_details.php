<?php
require_once 'config.php';

if (isset($_GET['type']) && isset($_GET['product_id'])) {
    $type = $_GET['type'];
    $product_id = intval($_GET['product_id']);

    if ($type == 'transfers') {
        // استخدام جداول transfers و warehouses و transfer_items
        $query = "SELECT t.transfer_date as date, t.id, w.name as source, ti.qty 
                  FROM transfer_items ti 
                  JOIN transfers t ON ti.transfer_id = t.id 
                  JOIN warehouses w ON t.from_warehouse_id = w.id 
                  WHERE ti.product_id = $product_id AND t.to_warehouse_id = 2";
    } else {
        // استخدام جداول invoices و customers و invoice_items
        $query = "SELECT i.invoice_date as date, i.id, c.name as source, ii.qty 
                  FROM invoice_items ii 
                  JOIN invoices i ON ii.invoice_id = i.id 
                  JOIN customers c ON i.customer_id = c.id 
                  WHERE ii.product_id = $product_id AND i.warehouse_id = 2";
    }

    $res = mysqli_query($conn, $query);
    while($r = mysqli_fetch_assoc($res)) {
        echo "<tr><td>{$r['date']}</td><td>{$r['id']}</td><td>{$r['source']}</td><td><span class='badge bg-secondary'>{$r['qty']}</span></td></tr>";
    }
}
