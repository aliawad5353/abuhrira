<?php 
include 'config.php'; 

// 1. حساب إجمالي المخزن الرئيسي (ID = 1)
// المتوفر = (المشتريات + التحويلات الواردة) - (المبيعات + التحويلات الصادرة)
$res_main = mysqli_query($conn, "SELECT SUM(quantity) as qty FROM stock_balances WHERE store_id = 1");
$main_stock = mysqli_fetch_assoc($res_main)['qty'] ?? 0;

// 2. حساب إجمالي المخزن الفرعي (ID = 2)
$res_sub = mysqli_query($conn, "SELECT SUM(quantity) as qty FROM stock_balances WHERE store_id = 2");
$sub_stock = mysqli_fetch_assoc($res_sub)['qty'] ?? 0;

// 3. البضاعة الكلية (مجموع المخزنين)
$grand_total_stock = main_stock + $sub_stock;

// إعدادات الترقيم للجدول بالأسفل
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// استعلام لجلب الأصناف ومقارنة كمياتها في المخزنين
$sql = "SELECT p.product_name, p.product_code,
        COALESCE((SELECT quantity FROM stock_balances WHERE product_id = p.id AND store_id = 1), 0) as q1,
        COALESCE((SELECT quantity FROM stock_balances WHERE product_id = p.id AND store_id = 2), 0) as q2
        FROM products p";
$result = mysqli_query($conn, $sql . " LIMIT $start, $limit");

$count_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$pages = ceil(mysqli_fetch_assoc($count_res)['count'] / $limit);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>البضاعة الكلية - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .overview-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { flex: 1; padding: 20px; border-radius: 12px; color: white; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .card-main { background: #2980b9; }
        .card-sub { background: #27ae60; }
        .card-total { background: #2c3e50; border: 4px solid #f39c12; }
        .card h3 { margin: 0 0 10px 0; font-size: 18px; opacity: 0.9; }
        .card div { font-size: 32px; font-weight: bold; }

        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eee; padding: 12px; border: 1px solid #ddd; }
        td { padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold; }
        
        .search-box { margin-bottom: 15px; width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { padding: 8px 16px; border: 1px solid #ddd; margin: 0 5px; text-decoration: none; color: #333; border-radius: 5px; }
        .active-p { background: #2c3e50; color: white !important; }
    </style>
</head>
<body>

    <h2 style="margin-bottom: 25px;">تبويب: البضاعة الكلية (موقف المخازن)</h2>

    <div class="overview-cards">
        <div class="card card-main">
            <h3>المخزن الرئيسي</h3>
            <div><?php echo number_format($main_stock); ?> <small>قطعة</small></div>
        </div>
        <div class="card card-sub">
            <h3>المخزن الفرعي</h3>
            <div><?php echo number_format($sub_stock); ?> <small>قطعة</small></div>
        </div>
        <div class="card card-total">
            <h3>إجمالي بضاعة الشركة</h3>
            <div><?php echo number_format($grand_total_stock); ?> <small>قطعة</small></div>
        </div>
    </div>

    

    <div class="table-container">
        <input type="text" id="pSearch" class="search-box" placeholder="ابحث عن صنف معين لمقارنة كمياته..." onkeyup="filterItems()">
        
        <table id="pTable">
            <thead>
                <tr>
                    <th>كود الصنف</th>
                    <th>اسم الصنف</th>
                    <th>المتبقي بالرئيسي</th>
                    <th>المتبقي بالفرعي</th>
                    <th>الإجمالي المتبقي</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $row_total = $row['q1'] + $row['q2'];
                ?>
                <tr>
                    <td><?php echo $row['product_code']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td style="color:#2980b9;"><?php echo $row['q1']; ?></td>
                    <td style="color:#27ae60;"><?php echo $row['q2']; ?></td>
                    <td style="background:#f9f9f9;"><?php echo $row_total; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for($i=1; $i<=$pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($page==$i)?'active-p':''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

<script>
function filterItems() {
    let input = document.getElementById("pSearch").value.toUpperCase();
    let rows = document.getElementById("pTable").getElementsByTagName("tr");
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName("td")[1];
        if (name) {
            rows[i].style.display = (name.innerText.toUpperCase().indexOf(input) > -1) ? "" : "none";
        }
    }
}
</script>

</body>
</html>
