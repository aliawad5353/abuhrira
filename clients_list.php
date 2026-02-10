<?php
include 'dashboard.php';
require_once 'config.php';

// محرك البحث
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM customers WHERE name LIKE '%$search%' OR phone LIKE '%$search%' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-4 shadow-sm">
            <h2 class="mb-4" style="font-weight: 900;">قائمة العملاء</h2>

            <form method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control fw-bold" placeholder="ابحث باسم العميل أو رقم الهاتف..." value="<?php echo $search; ?>" style="height: 55px;">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold" style="height: 55px;">
                        <i class="fa fa-search"></i> بـحـث
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr style="font-size: 18px;">
                            <th>اسم العميل</th>
                            <th>رقم الهاتف</th>
                            <th>المشتريات (اضغط لعرض الفاتورة)</th>
                            <th>استخراج فاتورة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 18px; font-weight: bold;">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="p-3"><?php echo $row['name']; ?></td>
                            <td class="p-3"><?php echo $row['phone']; ?></td>
                            <td class="p-3">
                                <?php 
                                // جلب فواتير العميل لإظهارها كأزرار مبالغ
                                $c_id = $row['id'];
                                $inv_q = mysqli_query($conn, "SELECT id, total_amount FROM invoices WHERE customer_id = $c_id");
                                if(mysqli_num_rows($inv_q) > 0) {
                                    while($inv = mysqli_fetch_assoc($inv_q)) {
                                        echo "<a href='view_invoice.php?id=".$inv['id']."' class='btn btn-outline-success btn-sm m-1 fw-bold' style='min-width: 80px;'>".$inv['total_amount']." ج.س</a>";
                                    }
                                } else {
                                    echo "<span class='text-muted small'>لا توجد مشتريات</span>";
                                }
                                ?>
                            </td>
                            <td class="p-3">
                                <a href="invoice_step1.php?customer_id=<?php echo $row['id']; ?>" class="btn btn-primary fw-bold">
                                    <i class="fa fa-file-invoice"></i> استخراج فاتورة
                                </a>
                            </td>
                            <td class="p-3">
                                <a href="delete_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف العميل؟')">
                                    <i class="fa fa-trash"></i> حذف
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* تحسين شكل الجدول ليكون عريضاً وواضحاً */
    .table th { padding: 20px !important; }
    .table td { border-bottom: 2px solid #eee !important; }
    .btn { border-radius: 8px !important; }
</style>