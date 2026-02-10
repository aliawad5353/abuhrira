<?php
require_once 'config.php';

$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// جلب بيانات الرأس
$sql_inv = "SELECT i.*, c.name as customer_name, w.name as warehouse_name 
            FROM invoices i 
            JOIN customers c ON i.customer_id = c.id 
            JOIN warehouses w ON i.warehouse_id = w.id 
            WHERE i.id = $invoice_id";
$res_inv = mysqli_query($conn, $sql_inv);
$inv_data = mysqli_fetch_assoc($res_inv);

// جلب الأصناف
$sql_items = "SELECT it.*, p.product_name, p.product_code 
              FROM invoice_items it 
              JOIN products p ON it.product_id = p.id 
              WHERE it.invoice_id = $invoice_id";
$res_items = mysqli_query($conn, $sql_items);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 40px; color: #000; font-size: 14px; line-height: 1.6; }
        .page-container { max-width: 850px; margin: auto; }
        
        /* رأس الفاتورة - الشعار والكتابة الجانبية */
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .header-text { width: 30%; font-size: 12px; }
        .header-logo { width: 35%; text-align: center; }
        .header-logo img { max-width: 140px; }

        /* عنوان الفاتورة */
        .invoice-title { text-align: center; font-size: 22px; font-weight: bold; margin: 10px 0; }

        /* بيانات الفاتورة - كل بيان في سطر تحت الآخر */
        .info-section { margin-bottom: 30px; text-align: right; }
        .info-row { margin-bottom: 2px; }
        .info-label { font-weight: bold; display: inline-block; width: 100px; }

        /* جدول الأصناف - نصوص بدون حدود */
        .items-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .items-table th { border-bottom: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; }
        .items-table td { padding: 10px 5px; text-align: center; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }

        /* قسم المبالغ - يمين وبدون خطوط */
        .totals-section { margin-top: 30px; text-align: right; float: right; width: 100%; }
        .total-item { font-size: 16px; margin-bottom: 5px; }
        .grand-total { font-size: 18px; font-weight: bold; color: blue; }

        /* التوقيعات */
        .footer-sig { display: flex; justify-content: space-between; margin-top: 80px; font-size: 13px; }
        
        @media print { .no-print { display: none; } body { padding: 20px; } }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom:20px;">
    <button onclick="window.print()" style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;">طباعة الفاتورة 🖨️</button>
</div>

<div class="page-container">
    <div class="header-top">
        <div class="header-text">
            شركة أبو حريرة للأحذية<br>
            الوكيل الحصري بالسودان لأحذية LUOFU
        </div>
        <div class="header-logo">
            <img src="assets/logo.png" alt="Logo" onerror="this.style.display='none';"><br>
            <strong>شركة أبو حريرة للأحذية</strong>
        </div>
        <div class="header-text" style="text-align: left;">
            ABU HAREERA SHOES CO.<br>
            LUOFU SUDAN
        </div>
    </div>

    <div class="invoice-title">فاتورة بيع رقم <?= $inv_data['id'] ?></div>

    <div class="info-section">
        <div class="info-row"><span class="info-label">الأسم:</span> <?= htmlspecialchars($inv_data['customer_name']) ?></div>
        <div class="info-row"><span class="info-label">التاريخ:</span> <?= $inv_data['invoice_date'] ?></div>
        <div class="info-row"><span class="info-label">المخزن:</span> <?= htmlspecialchars($inv_data['warehouse_name']) ?></div>
        <div class="info-row"><span class="info-label">طريقة الدفع:</span> كاش</div>
    </div>

    <div style="font-weight:bold; border-bottom: 1px solid #000; display:inline-block; margin-bottom:10px;">الأصناف</div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">التاريخ</th>
                <th class="text-right" style="width: 45%;">الصنف</th>
                <th style="width: 10%;">العدد</th>
                <th style="width: 15%;">سعر الوحدة</th>
                <th style="width: 15%;">مجموع السعر</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($res_items)): ?>
            <tr>
                <td><?= $inv_data['invoice_date'] ?></td>
                <td class="text-right">
                    <?= htmlspecialchars($item['product_code']) ?> - <?= htmlspecialchars($item['product_name']) ?>
                </td>
                <td><?= number_format($item['qty']) ?></td>
                <td><?= number_format($item['unit_price'], 2) ?></td>
                <td><?= number_format($item['total_price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-item" style="font-weight:bold;">المجموع</div>
        <div class="total-item grand-total">المبلغ الكلي: <?= number_format($inv_data['total_amount'], 2) ?> ج.س</div>
        <div class="total-item">المبلغ المستلم: 0.00</div>
        <div class="total-item">المتبقي: <?= number_format($inv_data['total_amount'], 2) ?></div>
    </div>

    <div style="clear:both;"></div>

    <div class="footer-sig">
        <div>المدخل: الصديق علي الصديق</div>
        <div>اسم المستلم: ...........................</div>
        <div>توقيع المستلم: ...........................</div>
        <div>أمين المخزن: ...........................</div>
    </div>
</div>

</body>
</html>
