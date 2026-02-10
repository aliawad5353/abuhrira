<?php
include 'dashboard.php';
require_once 'config.php';

// محرك البحث عن عميل محدد في سجل الخصومات
$search = $_GET['search'] ?? '';

// جلب بيانات الخصومات مع اسم العميل
$sql = "SELECT d.*, c.name as customer_name 
        FROM discounts_log d 
        JOIN customers c ON d.customer_id = c.id 
        WHERE c.name LIKE '%$search%' 
        ORDER BY d.discount_date DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-5 shadow-lg" style="border-radius: 20px; border-right: 10px solid #f1c40f;">
            <h2 class="mb-5" style="font-weight: 900; color: #2c3e50;">
                <i class="fa fa-percentage ms-2"></i> سجل خصومات العملاء
            </h2>

            <form method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control fw-bold shadow-sm" 
                           placeholder="ابحث عن تفاصيل خصومات عميل معين..." 
                           value="<?php echo $search; ?>" style="height: 60px; font-size: 20px;">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold" style="height: 60px;">بـحـث وفلترة</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr style="font-size: 18px; font-weight: 900;">
                            <th class="p-4">التاريخ</th>
                            <th class="p-4">اسم العميل</th>
                            <th class="p-4">مبلغ الخصم / السداد</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 20px; font-weight: 800;">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="p-3"><?php echo $row['discount_date']; ?></td>
                                <td class="p-3 text-primary"><?php echo $row['customer_name']; ?></td>
                                <td class="p-3 text-success" style="font-size: 24px;">
                                    - <?php echo number_format($row['discount_amount'], 2); ?> ج.س
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="p-5 text-muted fw-bold">لا توجد سجلات خصم مطابقة للبحث.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* جعل المربعات متباعدة ومتحاذية بارتفاع موحد */
    .form-control { border: 3px solid #ddd !important; border-radius: 12px !important; }
    .table-bordered { border: 2px solid #2c3e50 !important; }
    .table th { border: 1px solid #444 !important; }
    .table td { border-bottom: 2px solid #eee !important; }
</style>
