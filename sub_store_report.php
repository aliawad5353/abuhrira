<?php 
include 'config.php'; 

// 1. إعدادات الترقيم (Pagination)
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// 2. الاستعلام عن عمليات التحويل (بافتراض وجود جدول التحويلات transfers)
$sql = "SELECT t.*, p.product_name, s_from.store_name as from_store 
        FROM transfers t 
        JOIN products p ON t.product_id = p.id 
        JOIN stores s_from ON t.from_store_id = s_from.id 
        WHERE t.to_store_id = 2 
        ORDER BY t.transfer_date DESC";

$result = mysqli_query($conn, $sql . " LIMIT $start, $limit");

// حساب عدد الصفحات
$total_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM transfers WHERE to_store_id = 2");
$total_count = mysqli_fetch_assoc($total_res)['count'];
$pages = ceil($total_count / $limit);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المخزن الفرعي - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .report-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .search-bar { background: #2c3e50; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"] { flex-grow: 1; padding: 10px; border-radius: 5px; border: none; font-weight: bold; }
        .btn-search { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #34495e; color: white; padding: 12px; text-align: center; }
        td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; }
        
        .btn-delete { background: #e74c3c; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { padding: 8px 15px; border: 1px solid #ddd; margin: 2px; text-decoration: none; color: #333; border-radius: 5px; font-weight: bold; }
        .pagination a.active { background: #e74c3c; color: white; border-color: #e74c3c; }
    </style>
</head>
<body>

<div class="report-card">
    <h2 style="border-right: 5px solid #e74c3c; padding-right: 15px;">تقرير حركة المخزن الفرعي</h2>

    <div class="search-bar">
        <input type="text" id="subSearch" placeholder="ابحث عن صنف محول..." onkeyup="filterSubTable()">
        <button class="btn-search">بحث</button>
    </div>

    <table id="subTable">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>اسم الصنف المحول</th>
                <th>الكمية المحولة</th>
                <th>من مخزن</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['transfer_date']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['from_store']; ?></td>
                <td>
                    <a href="report_actions.php?do=delete_transfer&id=<?php echo $row['id']; ?>" 
                       class="btn-delete" 
                       onclick="return confirm('هل تريد حذف عملية التحويل وإرجاع الكمية للمخزن الأصلي؟')">
                        حذف وإرجاع
                    </a>
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
function filterSubTable() {
    let input = document.getElementById("subSearch");
    let filter = input.value.toUpperCase();
    let tr = document.getElementById("subTable").getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[1]; // عمود الصنف
        if (td) {
            tr[i].style.display = (td.innerText.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        }
    }
}
</script>

</body>
</html>
