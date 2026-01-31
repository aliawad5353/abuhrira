<?php 
include 'config.php'; 

// إعدادات الترقيم (Pagination)
$limit = 10; // عدد الفواتير في كل صفحة
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// جلب الفواتير مع حساب عدد الأصناف الكلي (الكمية الكلية)
$sql = "SELECT i.*, c.name as customer_name, SUM(ii.quantity) as total_items_qty 
        FROM invoices i 
        JOIN clients c ON i.client_id = c.id 
        JOIN invoice_items ii ON i.id = ii.invoice_id 
        GROUP BY i.id 
        ORDER BY i.id DESC 
        LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// جلب العدد الكلي للفواتير لحساب عدد الصفحات
$total_res = mysqli_query($conn, "SELECT COUNT(id) AS id FROM invoices");
$custCount = mysqli_fetch_all($total_res, MYSQLI_ASSOC);
$total = $custCount[0]['id'];
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الفواتير - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .search-container { margin-bottom: 20px; background: #2c3e50; padding: 20px; border-radius: 8px; }
        #searchInput { width: 100%; height: 45px; padding: 0 15px; font-size: 18px; border-radius: 5px; border: none; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #34495e; color: white; padding: 12px; text-align: center; }
        td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; }
        
        /* أزرار الصفحات */
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { padding: 8px 16px; border: 1px solid #ddd; margin: 0 4px; text-decoration: none; color: #333; border-radius: 4px; font-weight: bold; }
        .pagination a.active { background: #2980b9; color: white; border-color: #2980b9; }

        .btn-edit { background: #f39c12; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; }
        .price-link { color: #2980b9; text-decoration: underline; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="border-right: 5px solid #2980b9; padding-right: 15px;">التبويب الثالث: إدارة فواتير المبيعات</h2>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="ابحث برقم الفاتورة أو اسم العميل..." onkeyup="filterTable()">
    </div>

    <table id="invoicesTable">
        <thead>
            <tr>
                <th>تاريخ الفاتورة</th>
                <th>اسم العميل</th>
                <th>رقم الفاتورة</th>
                <th>مبلغ الفاتورة الكلي</th>
                <th>عدد الأصناف (الكمية)</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['invoice_date']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <a href="view_invoice.php?id=<?php echo $row['id']; ?>" class="price-link">
                        <?php echo number_format($row['total_amount'], 2); ?> ج.س
                    </a>
                </td>
                <td><?php echo $row['total_items_qty']; ?> (قطعة/كرتونة)</td>
                <td>
                    <a href="edit_invoice.php?id=<?php echo $row['id']; ?>" class="btn-edit">تعديل الفاتورة</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php if($page==$i) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("invoicesTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdCustomer = tr[i].getElementsByTagName("td")[1];
        let tdID = tr[i].getElementsByTagName("td")[2];
        if (tdCustomer || tdID) {
            let text = (tdCustomer.textContent || tdCustomer.innerText) + (tdID.textContent || tdID.innerText);
            tr[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>

</body>
</html>
