<?php 
include 'config.php'; 

// 1. إعدادات الترقيم (Pagination)
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;
$store_id = isset($_GET['store_id']) ? $_GET['store_id'] : '';

// 2. الاستعلام الرئيسي للجرد
$sql = "SELECT p.id as pid, p.product_code, p.product_name, sb.quantity as current_stock, sb.price, (sb.quantity * sb.price) as total_value
        FROM products p 
        JOIN stock_balances sb ON p.id = sb.product_id 
        WHERE sb.store_id = '$store_id'";

$result = mysqli_query($conn, $sql . " LIMIT $start, $limit");

// حساب عدد الصفحات
$total_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM stock_balances WHERE store_id = '$store_id'");
$total_count = mysqli_fetch_assoc($total_res)['count'];
$pages = ceil($total_count / $limit);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المخازن - شركة أبو حريرة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .report-box { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .top-bar { display: flex; gap: 20px; margin-bottom: 20px; background: #2c3e50; padding: 15px; border-radius: 8px; color: white; align-items: center; }
        input[type="text"], select { padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #34495e; color: white; padding: 12px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; }
        .btn-price { background: #e67e22; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-entry { background: #2980b9; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { padding: 8px 15px; border: 1px solid #ddd; margin: 2px; text-decoration: none; color: #333; border-radius: 5px; }
        .pagination a.active { background: #2980b9; color: white; }
        /* تصميم النموذج المنبثق (Modal) */
        .modal { display:none; position:fixed; z-index:1; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); }
        .modal-content { background:white; margin:10% auto; padding:20px; width:40%; border-radius:10px; text-align:right; }
    </style>
</head>
<body>

<div class="report-box">
    <h2>تقرير المخازن</h2>

    <div class="top-bar">
        <form method="GET" id="storeForm">
            <label>اختر مخزن:</label>
            <select name="store_id" onchange="this.form.submit()">
                <option value="">-- اختر --</option>
                <option value="1" <?php if($store_id == '1') echo 'selected'; ?>>المخزن الرئيسي</option>
                <option value="2" <?php if($store_id == '2') echo 'selected'; ?>>المخزن الفرعي</option>
                <option value="3" <?php if($store_id == '3') echo 'selected'; ?>>المعرض</option>
            </select>
        </form>
        <input type="text" id="pSearch" placeholder="بحث عن صنف..." onkeyup="filterRows()" style="flex-grow:1;">
    </div>

    <?php if($store_id): ?>
    <table id="invTable">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>اسم الصنف</th>
                <th>دخول (مشتريات)</th>
                <th>خروج (مبيعات)</th>
                <th>متبقي</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // حساب الخروج (المبيعات) لهذا الصنف في هذا المخزن
                $out_res = mysqli_query($conn, "SELECT SUM(quantity) as out_qty FROM invoice_items ii JOIN invoices i ON ii.invoice_id = i.id WHERE ii.product_id = '".$row['pid']."' AND i.store_id = '$store_id'");
                $out_qty = mysqli_fetch_assoc($out_res)['out_qty'] ?? 0;
            ?>
            <tr>
                <td><?php echo date('Y-m-d'); ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td>
                    <a href="entry_details.php?pid=<?php echo $row['pid']; ?>&sid=<?php echo $store_id; ?>" class="btn-entry">
                        تفاصيل الدخول
                    </a>
                </td>
                <td><?php echo $out_qty; ?></td>
                <td><?php echo $row['current_stock']; ?></td>
                <td>
                    <button class="btn-price" onclick="openPriceModal(<?php echo $row['pid']; ?>, <?php echo $row['price']; ?>, <?php echo $store_id; ?>)">
                        <?php echo number_format($row['price'], 2); ?>
                    </button>
                </td>
                <td><?php echo number_format($row['total_value'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?store_id=<?php echo $store_id; ?>&page=<?php echo $i; ?>" class="<?php if($page==$i) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php else: echo "<p>الرجاء اختيار مخزن لعرض البيانات</p>"; endif; ?>
</div>

<div id="priceModal" class="modal">
    <div class="modal-content">
        <h3>تعديل السعر</h3>
        <form action="report_actions.php?do=update_price" method="POST">
            <input type="hidden" name="pid" id="modal_pid">
            <input type="hidden" name="sid" id="modal_sid">
            <label>السعر الجديد:</label><br>
            <input type="number" name="new_price" id="modal_price" style="width:90%; margin:10px 0;"><br>
            <button type="submit" class="btn-entry" style="background:#27ae60; width:100%;">حفظ التعديل</button>
            <button type="button" onclick="closeModal()" style="background:#7f8c8d; color:white; border:none; padding:10px; width:100%; margin-top:5px; border-radius:5px; cursor:pointer;">إلغاء</button>
        </form>
    </div>
</div>

<script>
function filterRows() {
    let filter = document.getElementById("pSearch").value.toUpperCase();
    let rows = document.getElementById("invTable").getElementsByTagName("tr");
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName("td")[1];
        if (name) {
            rows[i].style.display = (name.innerText.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        }
    }
}
function openPriceModal(id, price, sid) {
    document.getElementById("modal_pid").value = id;
    document.getElementById("modal_price").value = price;
    document.getElementById("modal_sid").value = sid;
    document.getElementById("priceModal").style.display = "block";
}
function closeModal() { document.getElementById("priceModal").style.display = "none"; }
</script>
</body>
</html>
