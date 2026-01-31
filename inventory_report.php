<?php 
include 'config.php'; 

$store_filter = isset($_GET['store_id']) ? $_GET['store_id'] : '';

// استعلام لجلب الكميات والأسعار وتفاصيل الأصناف
$query = "SELECT p.product_code, p.product_name, s.store_name, sb.quantity, sb.price, (sb.quantity * sb.price) as total_value
          FROM stock_balances sb
          JOIN products p ON sb.product_id = p.id
          JOIN stores s ON sb.store_id = s.id";

if ($store_filter != '') {
    $query .= " WHERE sb.store_id = $store_filter";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير جرد المخازن - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .report-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .filter-bar { background: #2c3e50; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; color: white; }
        select { height: 40px; border-radius: 5px; padding: 0 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #34495e; color: white; padding: 12px; text-align: center; border: 1px solid #456; }
        td { padding: 10px; border: 1px solid #ddd; text-align: center; font-weight: bold; }
        .low-stock { background-color: #ffeb3b; } /* لون للأصناف التي قارب مخزونها على النفاذ */
        .summary-box { margin-top: 20px; display: flex; gap: 20px; }
        .stat-item { background: #fff; padding: 15px; border-radius: 8px; border: 2px solid #2980b9; flex: 1; text-align: center; }
    </style>
</head>
<body>

<div class="report-card">
    <h2><i class="fas fa-boxes"></i> تقرير جرد المخازن التفصيلي</h2>

    <div class="filter-bar">
        <span>عرض مخزون:</span>
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="store_id" onchange="this.form.submit()">
                <option value="">-- كل المخازن --</option>
                <?php
                $stores = mysqli_query($conn, "SELECT * FROM stores");
                while($st = mysqli_fetch_assoc($stores)) {
                    $selected = ($store_filter == $st['id']) ? 'selected' : '';
                    echo "<option value='".$st['id']."' $selected>".$st['store_name']."</option>";
                }
                ?>
            </select>
        </form>
        <button onclick="window.print()" style="margin-right: auto; padding: 10px 20px; cursor: pointer; background: #27ae60; color: white; border: none; border-radius: 5px; font-weight: bold;">طباعة التقرير</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>كود الصنف</th>
                <th>اسم الصنف</th>
                <th>المخزن</th>
                <th>الكمية المتوفرة</th>
                <th>سعر التكلفة/البيع</th>
                <th>إجمالي القيمة</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_qty = 0;
            $total_value_all = 0;
            while($row = mysqli_fetch_assoc($result)): 
                $total_qty += $row['quantity'];
                $total_value_all += $row['total_value'];
            ?>
            <tr class="<?php echo ($row['quantity'] < 5) ? 'low-stock' : ''; ?>">
                <td><?php echo $row['product_code']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['store_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo number_format($row['total_value'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="stat-item">
            <div style="color: #7f8c8d;">إجمالي عدد القطع</div>
            <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?php echo $total_qty; ?></div>
        </div>
        <div class="stat-item">
            <div style="color: #7f8c8d;">إجمالي قيمة المخزون</div>
            <div style="font-size: 24px; font-weight: bold; color: #27ae60;"><?php echo number_format($total_value_all, 2); ?> ج.س</div>
        </div>
    </div>
</div>

</body>
</html>
