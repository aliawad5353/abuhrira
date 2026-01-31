<?php 
include 'config.php';

// استلام البيانات من المرحلة الثانية
$client_id = $_POST['client_id'];
$store_id  = $_POST['store_id'];
$qtys      = $_POST['qty'];
$p_ids     = $_POST['product_id'];

// جلب بيانات العميل والمخزن للعرض
$client_res = mysqli_query($conn, "SELECT name FROM clients WHERE id = $client_id");
$client_data = mysqli_fetch_assoc($client_res);
$store_res = mysqli_query($conn, "SELECT store_name FROM stores WHERE id = $store_id");
$store_data = mysqli_fetch_assoc($store_res);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مراجعة الفاتورة - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .review-box { background: white; padding: 30px; border-radius: 12px; max-width: 900px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .alert-info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-right: 5px solid #0c5460; }
        
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #2c3e50; color: white; padding: 12px; text-align: center; font-size: 18px; }
        td { padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold; font-size: 17px; }
        
        .total-section { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: left; margin-top: 20px; border: 2px solid #2c3e50; }
        .total-amount { font-size: 24px; color: #e74c3c; font-weight: bold; }
        
        .btn-confirm { width: 100%; padding: 18px; background-color: #27ae60; color: white; border: none; border-radius: 8px; font-size: 22px; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-confirm:hover { background-color: #219150; }
    </style>
</head>
<body>

<div class="review-box">
    <div class="alert-info">
        <i class="fas fa-exclamation-triangle"></i> أنت تسحب الآن من: (<?php echo $store_data['store_name']; ?>)
    </div>

    <div style="margin-bottom: 20px; font-size: 18px; font-weight: bold;">
        تاريخ الفاتورة: <?php echo date('Y-m-d'); ?> <br>
        اسم العميل: <?php echo $client_data['name']; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>سعر الوحده</th>
                <th>السعر الكلي</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grand_total = 0;
            // حلقة لعرض الأصناف المختارة
            for ($i = 0; $i < count($p_ids); $i++) {
                $pid = $p_ids[$i];
                $qty = $qtys[$i];
                
                // جلب بيانات الصنف وسعره من المخزن المختار
                $p_info = mysqli_query($conn, "SELECT p.product_name, sb.price FROM products p 
                          JOIN stock_balances sb ON p.id = sb.product_id 
                          WHERE p.id = $pid AND sb.store_id = $store_id");
                $p_data = mysqli_fetch_assoc($p_info);
                
                $unit_price = $p_data['price'];
                $row_total = $unit_price * $qty;
                $grand_total += $row_total;

                echo "<tr>";
                echo "<td>".$p_data['product_name']."</td>";
                echo "<td>".$qty."</td>";
                echo "<td>".number_format($unit_price, 2)."</td>";
                echo "<td>".number_format($row_total, 2)."</td>";
                echo "</tr>";
                
                // سنمرر البيانات مخفية لتأكيد البيع
                echo "<input type='hidden' name='p_ids[]' value='$pid'>";
                echo "<input type='hidden' name='qtys[]' value='$qty'>";
            }
            ?>
        </tbody>
    </table>

    <div class="total-section">
        <span style="font-size: 20px; font-weight: bold;">المبلغ الكلي للفاتورة: </span>
        <span class="total-amount"><?php echo number_format($grand_total, 2); ?> ج.س</span>
    </div>

    <form action="confirm_sale.php" method="POST">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        <input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
        <input type="hidden" name="total_amount" value="<?php echo $grand_total; ?>">
        <?php
        foreach($p_ids as $id) echo "<input type='hidden' name='p_ids[]' value='$id'>";
        foreach($qtys as $q) echo "<input type='hidden' name='qtys[]' value='$q'>";
        ?>
        <button type="submit" class="btn-confirm" onclick="this.disabled=true; this.innerText='جاري معالجة البيع...'; this.form.submit();">
            تأكيد البيع وحفظ الفاتورة
        </button>
    </form>
</div>

</body>
</html>
