<?php
include 'dashboard.php';
require_once 'config.php';

// إعدادات البحث والفلترة
$warehouse_id = $_GET['warehouse_id'] ?? '';
$search = $_GET['search'] ?? '';

// جلب البيانات بناءً على الفلترة
$sql = "SELECT p.product_code, p.product_name, p.id as pid,
        SUM(CASE WHEN s.qty > 0 THEN s.qty ELSE 0 END) as total_in,
        (SELECT SUM(qty) FROM invoice_items WHERE product_id = p.id) as total_out,
        MAX(s.price) as last_price
        FROM products p
        LEFT JOIN stock_balances s ON p.id = s.product_id
        WHERE (p.product_name LIKE '%$search%' OR p.product_code LIKE '%$search%')";

if ($warehouse_id) { $sql .= " AND s.warehouse_id = '$warehouse_id'"; }
$sql .= " GROUP BY p.id";

$result = mysqli_query($conn, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-4 shadow-lg" style="border-radius: 20px;">
            <h2 class="mb-5 text-center" style="font-weight: 900;">📊 تقرير حركة المخازن - LUOFU</h2>

            <form method="GET" class="row g-3 mb-5 p-3 bg-light rounded border">
                <div class="col-md-4">
                    <label class="fw-bold mb-2">اختر المخزن:</label>
                    <select name="warehouse_id" class="form-control fw-bold" style="height: 55px;">
                        <option value="">كل المخازن</option>
                        <option value="1" <?php if($warehouse_id == 1) echo 'selected'; ?>>المخزن الرئيسي</option>
                        <option value="2" <?php if($warehouse_id == 2) echo 'selected'; ?>>المخزن الفرعي</option>
                        <option value="3" <?php if($warehouse_id == 3) echo 'selected'; ?>>المعرض</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="fw-bold mb-2">بحث عن صنف:</label>
                    <input type="text" name="search" class="form-control fw-bold" placeholder="كود الصنف أو الاسم..." value="<?php echo $search; ?>" style="height: 55px;">
                </div>
                <div class="col-md-3 mt-auto">
                    <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold" style="height: 55px;">بـحـث وتحديث</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle border-dark">
                    <thead class="table-dark">
                        <tr style="font-size: 18px;">
                            <th>التاريخ</th>
                            <th>اسم الصنف</th>
                            <th>دخول</th>
                            <th>خروج</th>
                            <th>المتبقي</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 18px; font-weight: 800;">
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $stock_left = $row['total_in'] - $row['total_out'];
                            $total_value = $stock_left * $row['last_price'];
                        ?>
                        <tr>
                            <td><?php echo date('Y/m/d'); ?></td>
                            <td class="text-start ps-3"><?php echo $row['product_code']." - ".$row['product_name']; ?></td>
                            <td>
                                <a href="item_details.php?id=<?php echo $row['pid']; ?>" class="btn btn-info fw-bold text-white w-100">
                                    <?php echo $row['total_in'] ?? 0; ?>
                                </a>
                            </td>
                            <td class="text-danger"><?php echo $row['total_out'] ?? 0; ?></td>
                            <td class="bg-warning text-dark"><?php echo $stock_left; ?></td>
                            <td>
                                <button onclick="editPrice(<?php echo $row['pid']; ?>, <?php echo $row['last_price']; ?>)" class="btn btn-outline-primary fw-bold w-100">
                                    <?php echo number_format($row['last_price'], 2); ?>
                                </button>
                            </td>
                            <td class="text-success"><?php echo number_format($total_value, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function editPrice(id, currentPrice) {
    let newPrice = prompt("تعديل سعر الوحدة:", currentPrice);
    if (newPrice != null) {
        window.location.href = "update_price.php?id=" + id + "&price=" + newPrice;
    }
}
</script>