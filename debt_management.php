<?php 
include 'config.php'; 

// 1. حساب المديونية الكلية لجميع العملاء (مجموع المشتريات - الخصومات)
$total_debts_query = mysqli_query($conn, "SELECT SUM(total_amount) as all_invoices FROM invoices");
$all_inv = mysqli_fetch_assoc($total_debts_query)['all_invoices'] ?? 0;

$total_discounts_query = mysqli_query($conn, "SELECT SUM(discount_amount) as all_discs FROM discounts_log");
$all_discs = mysqli_fetch_assoc($total_discounts_query)['all_discs'] ?? 0;

$net_debt_total = $all_inv - $all_discs;

// 2. الترقيم (Pagination)
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// 3. استعلام العملاء مع مديونياتهم
$sql = "SELECT c.id, c.name, 
        (SELECT SUM(total_amount) FROM invoices WHERE client_id = c.id) as total_bought,
        (SELECT SUM(discount_amount) FROM discounts_log WHERE client_id = c.id) as total_paid
        FROM clients c";
$result = mysqli_query($conn, $sql . " LIMIT $start, $limit");

$count_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM clients");
$pages = ceil(mysqli_fetch_assoc($count_res)['count'] / $limit);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المديونية - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .debt-container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stats-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .total-box { background: #d32f2f; color: white; padding: 15px 30px; border-radius: 10px; font-size: 20px; font-weight: bold; box-shadow: 0 4px 10px rgba(211,47,47,0.3); }
        .search-area { background: #ebf2f7; padding: 15px; border-radius: 8px; display: flex; gap: 10px; margin-bottom: 20px; }
        input[type="text"] { flex-grow: 1; padding: 10px; border: 2px solid #3498db; border-radius: 5px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #2c3e50; color: white; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; }
        
        .btn-pay { background: #27ae60; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .modal { display:none; position:fixed; z-index:100; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); }
        .modal-content { background:white; margin:10% auto; padding:30px; width:400px; border-radius:15px; text-align:right; border-top: 8px solid #27ae60; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { padding: 8px 12px; border: 1px solid #ddd; margin: 2px; text-decoration: none; color: #333; border-radius: 4px; }
        .active-page { background: #2c3e50; color: white !important; }
    </style>
</head>
<body>

<div class="debt-container">
    <div class="stats-header">
        <h2 style="margin:0;">تبويب المديونية</h2>
        <div class="total-box">المديونية الكلية: <?php echo number_format($net_debt_total, 2); ?> ج.س</div>
    </div>

    <div class="search-area">
        <input type="text" id="debtSearch" placeholder="ابحث عن اسم العميل لتصفية المديونية..." onkeyup="filterDebts()">
        <button style="padding:10px 20px; background:#2c3e50; color:white; border:none; border-radius:5px; cursor:pointer;">بحث</button>
    </div>

    <table id="debtTable">
        <thead>
            <tr>
                <th>اسم العميل</th>
                <th>إجمالي المديونية (الفواتير)</th>
                <th>سداد / خصم</th>
                <th>المتبقي الحالي</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $debt = $row['total_bought'] - $row['total_paid'];
            ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo number_format($row['total_bought'], 2); ?></td>
                <td>
                    <button class="btn-pay" onclick="openPaymentModal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $debt; ?>)">
                        <i class="fas fa-hand-holding-usd"></i> سداد
                    </button>
                </td>
                <td style="color:#d32f2f;"><?php echo number_format($debt, 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($page==$i)?'active-page':''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<div id="paymentModal" class="modal">
    <div class="modal-content">
        <h3>تسجيل عملية سداد/خصم</h3>
        <hr>
        <form action="report_actions.php?do=save_payment" method="POST">
            <input type="hidden" name="client_id" id="client_id_val">
            <p><strong>العميل:</strong> <span id="client_name_display"></span></p>
            <p><strong>التاريخ:</strong> <?php echo date('Y-m-d'); ?></p>
            
            <label>الجملة الكلية للمديونية:</label>
            <input type="text" id="total_debt_val" readonly style="width:100%; background:#eee; margin-bottom:15px; padding:8px;">
            
            <label>مبلغ الخصم/السداد:</label>
            <input type="number" name="discount_amount" required style="width:100%; padding:10px; border:2px solid #27ae60; font-size:18px;">
            
            <button type="submit" style="width:100%; padding:15px; background:#27ae60; color:white; border:none; border-radius:8px; margin-top:20px; font-weight:bold; cursor:pointer;">حفظ السداد وتحديث المتبقي</button>
            <button type="button" onclick="closeModal()" style="width:100%; padding:10px; background:#7f8c8d; color:white; border:none; border-radius:8px; margin-top:5px; cursor:pointer;">إلغاء</button>
        </form>
    </div>
</div>

<script>
function openPaymentModal(id, name, debt) {
    document.getElementById("client_id_val").value = id;
    document.getElementById("client_name_display").innerText = name;
    document.getElementById("total_debt_val").value = debt.toLocaleString() + " ج.س";
    document.getElementById("paymentModal").style.display = "block";
}
function closeModal() { document.getElementById("paymentModal").style.display = "none"; }
function filterDebts() {
    let input = document.getElementById("debtSearch").value.toUpperCase();
    let rows = document.getElementById("debtTable").getElementsByTagName("tr");
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName("td")[0];
        if (name) {
            rows[i].style.display = (name.innerText.toUpperCase().indexOf(input) > -1) ? "" : "none";
        }
    }
}
</script>
</body>
</html>
