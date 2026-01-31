<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة العملاء - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .search-box { display: flex; gap: 10px; margin-bottom: 25px; background: #ebf2f7; padding: 15px; border-radius: 8px; }
        input[type="search"] { flex-grow: 1; height: 45px; padding: 0 15px; border: 2px solid #3498db; border-radius: 5px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: white; padding: 15px; font-size: 18px; text-align: center; }
        td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; font-size: 16px; }
        
        /* تنسيق أزرار الجدول */
        .btn-invoice-link { background: #f39c12; color: white; padding: 5px 15px; border-radius: 20px; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-action { padding: 8px 15px; border-radius: 5px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; color: white; }
        .btn-extract { background-color: #2980b9; }
        .btn-delete { background-color: #e74c3c; }
    </style>
</head>
<body>

<div class="container">
    <h2>قائمة العملاء</h2>
    
    <div class="search-box">
        <input type="search" id="customerSearch" placeholder="ابحث عن اسم العميل أو رقم الهاتف..." onkeyup="filterCustomers()">
        <button class="btn-action btn-extract" style="width:100px;">بحث</button>
    </div>

    <table id="customersTable">
        <thead>
            <tr>
                <th>اسم العميل</th>
                <th>رقم الهاتف</th>
                <th>المشتريات (الفواتير)</th>
                <th>استخراج فاتورة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM clients ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($res)) {
                $client_id = $row['id'];
                echo "<tr>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['phone']."</td>";
                echo "<td>";
                // جلب مبالغ الفواتير كأزرار قابلة للضغط
                $invoices = mysqli_query($conn, "SELECT id, total_amount FROM invoices WHERE client_id = $client_id");
                while($inv = mysqli_fetch_assoc($invoices)) {
                    echo "<a href='view_invoice.php?id=".$inv['id']."' class='btn-invoice-link'>".$inv['total_amount']." ج.س</a> ";
                }
                echo "</td>";
                echo "<td><a href='create_invoice_step1.php?client_id=$client_id' class='btn-action btn-extract'>استخرج فاتورة</a></td>";
                echo "<td><a href='delete_client.php?id=$client_id' class='btn-action btn-delete' onclick='return confirm(\"هل أنت متأكد من حذف العميل؟\")'>حذف</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
function filterCustomers() {
    let input = document.getElementById("customerSearch");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("customersTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdName = tr[i].getElementsByTagName("td")[0];
        let tdPhone = tr[i].getElementsByTagName("td")[1];
        if (tdName || tdPhone) {
            let txtValue = (tdName.textContent || tdName.innerText) + (tdPhone.textContent || tdPhone.innerText);
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>

</body>
</html>
