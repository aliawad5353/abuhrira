<?php
include 'dashboard.php';
require_once 'config.php';

// محرك البحث عن صنف داخل المخزن الفرعي
$search = $_GET['search'] ?? '';

// جلب البيانات: نركز هنا على التحويلات التي كان وجهتها "المخزن الفرعي" (ID = 2)
$sql = "SELECT ti.id as item_id, t.transfer_date, p.product_code, p.product_name, ti.qty, t.from_warehouse_id
        FROM transfer_items ti
        JOIN transfers t ON ti.transfer_id = t.id
        JOIN products p ON ti.product_id = p.id
        WHERE t.to_warehouse_id = 2 
        AND (p.product_name LIKE '%$search%' OR p.product_code LIKE '%$search%')
        ORDER BY t.transfer_date DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-5 shadow-lg" style="border-radius: 20px; border-top: 5px solid #17a2b8;">
            <h2 class="mb-5 text-center" style="font-weight: 900; color: #2c3e50;">
                <i class="fa fa-shuttle-van ms-2"></i> تقرير بضاعة المخزن الفرعي
            </h2>

            <form method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-info text-white"><i class="fa fa-search"></i></span>
                        <input type="text" name="search" class="form-control fw-bold" 
                               placeholder="ابحث عن صنف محول للمخزن الفرعي..." 
                               value="<?php echo $search; ?>" style="height: 60px; font-size: 20px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-info btn-lg w-100 fw-bold text-white" style="height: 60px;">بـحـث</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-info">
                        <tr style="font-size: 18px; font-weight: 900;">
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">الصنف (الكود والاسم)</th>
                            <th class="p-3">الكمية المحولة</th>
                            <th class="p-3">المصدر</th>
                            <th class="p-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 19px; font-weight: 800;">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                // جلب اسم مخزن المصدر
                                $source_id = $row['from_warehouse_id'];
                                $source_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM warehouses WHERE id = $source_id"))['name'];
                            ?>
                            <tr>
                                <td><?php echo $row['transfer_date']; ?></td>
                                <td class="text-start ps-4"><?php echo $row['product_code']." - ".$row['product_name']; ?></td>
                                <td class="text-primary"><?php echo $row['qty']; ?> كرتونة</td>
                                <td><span class="badge bg-secondary p-2"><?php echo $source_name; ?></span></td>
                                <td>
                                    <a href="delete_sub_transfer.php?id=<?php echo $row['item_id']; ?>" 
                                       class="btn btn-danger btn-lg fw-bold" 
                                       onclick="return confirm('هل تريد حذف هذه الكمية وإرجاعها للمخزن الأصلي؟')">
                                        <i class="fa fa-undo"></i> حذف وإرجاع
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-5 text-muted">لا توجد تحويلات مسجلة للمخزن الفرعي حالياً.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* تنسيق خاص لتقرير المخزن الفرعي */
    .table-info th { background-color: #17a2b8 !important; color: white !important; }
    .table td { border: 1px solid #dee2e6 !important; }
    .btn-danger { box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); }
</style>