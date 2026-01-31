<?php
include 'config.php';

// جلب رقم الفاتورة من الرابط
$invoice_id = $_GET['id'];

// استعلام لجلب بيانات الفاتورة والعميل والمخزن
$query = "SELECT i.*, c.name as customer_name, s.store_name 
          FROM invoices i 
          JOIN clients c ON i.client_id = c.id 
          JOIN stores s ON i.store_id = s.id 
          WHERE i.id = $invoice_id";
$result = mysqli_query($conn, $query);
$invoice = mysqli_fetch_assoc($result);

if (!$invoice) { die("الفاتورة غير موجودة"); }
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم <?php echo $invoice['id']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f0f0; margin: 0; padding: 20px; }
        .invoice-paper { 
            background: white; width: 210mm; min-height: 297mm; padding: 20px; margin: auto; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative;
        }
        
        /* زر الطباعة الأزرق في الأعلى يمين */
        .print-btn {
            background-color: #2980b9; color: white; border: none; padding: 10px 25px;
            font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer;
            position: absolute; top: 20px; right: 20px; transition: 0.3s;
        }
        .print-btn:hover { background-color: #1c5982; }

        /* الهيدر والشعار */
        .header { text-align: center; margin-top: 50px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .logo { width: 300px; }
        .company-info { display: flex; justify-content: space-between; font-weight: bold; margin-top: 10px; }
        
        /* بيانات الفاتورة */
        .bill-info { margin: 20px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 18px; font-weight: bold; }
        .invoice-title { text-align: center; font-size: 26px; font-weight: bold; margin: 10px 0; text-decoration: underline; }

        /* جدول الأصناف */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #eee; border: 1px solid #333; padding: 10px; font-size: 16px; }
        td { border: 1px solid #333; padding: 10px; text-align: center; font-weight: bold; font-size: 15px; }

        /* إجمالي الفاتورة */
        .total-box { margin-top: 30px; float: left; width: 300px; border: 2px solid #333; padding: 10px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 18px; font-weight: bold; }
        
        /* التوقيعات */
        .signatures { margin-top: 100px; display: flex; justify-content: space-between; font-weight: bold; }

        /* إخفاء الزر عند الطباعة */
        @media print {
            .print-btn { display: none; }
            body { background: white; padding: 0; }
            .invoice-paper { box-shadow: none; border: none; width: 100%; margin: 0; }
        }
    </style>
</head>
<body>

<div class="invoice-paper">
    <button class="print-btn" onclick="window.print()">طباعة الفاتورة</button>

    <div class="header">
        <img src="logo.jpg" alt="LUOFU Sudan" class="logo">
        <div class="company-info">
            <span>شركة أبو حريرة للأحذية</span>
            <span>الوكيل الحصري بالسودان لأحذية LUOFU</span>
        </div>
    </div>

    <div class="invoice-title">فاتورة شراء رقم <?php echo $invoice['id']; ?></div>

    <div class="bill-info">
        <div>اسم العميل: <?php echo $invoice['customer_name']; ?></div>
        <div>تاريخ الفاتورة: <?php echo $invoice['invoice_date']; ?></div>
        <div>المخزن: <?php echo $invoice['store_name']; ?></div>
        <div>رقم الفاتورة: <?php echo $invoice['id']; ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>اسم الصنف / الكود</th>
                <th>عدد الأصناف</th>
                <th>سعر الوحدة</th>
                <th>مجموع السعر</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $items_query = "SELECT ii.*, p.product_name, p.product_code 
                            FROM invoice_items ii 
                            JOIN products p ON ii.product_id = p.id 
                            WHERE ii.invoice_id = $invoice_id";
            $items_res = mysqli_query($conn, $items_query);
            while($item = mysqli_fetch_assoc($items_res)) {
                $total_item = $item['quantity'] * $item['price'];
                echo "<tr>";
                echo "<td>".$invoice['invoice_date']."</td>";
                echo "<td>".$item['product_name']." <br> <small>".$item['product_code']."</small></td>";
                echo "<td>".$item['quantity']."</td>";
                echo "<td>".number_format($item['price'], 2)."</td>";
                echo "<td>".number_format($total_item, 2)."</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-row">
            <span>المبلغ الكلي:</span>
            <span><?php echo number_format($invoice['total_amount'], 2); ?></span>
        </div>
        <div class="total-row" style="color: blue;">
            <span>المجموع النهائي:</span>
            <span><?php echo number_format($invoice['total_amount'], 2); ?> ج.س</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="signatures">
        <div>توقيع المستلم: .....................</div>
        <div>أمين المخزن: .....................</div>
    </div>
</div>

</body>
</html>
