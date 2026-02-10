<?php
include 'dashboard.php';
require_once 'config.php';

// إعدادات تقسيم الصفحات (Pagination)
$limit = 10; // عدد الفواتير في كل صفحة
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// محرك البحث (باسم العميل أو رقم الفاتورة)
$search = $_GET['search'] ?? '';
$search_query = "";
if ($search) {
    $search_query = " WHERE c.name LIKE '%$search%' OR i.id = '$search' ";
}

// جلب الفواتير مع حساب عدد الأصناف لكل فاتورة
$sql = "SELECT i.*, c.name as customer_name, 
        (SELECT SUM(qty) FROM invoice_items WHERE invoice_id = i.id) as total_qty 
        FROM invoices i 
        JOIN customers c ON i.customer_id = c.id 
        $search_query 
        ORDER BY i.id DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

// جلب العدد الكلي للصفحات
$total_rows = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM invoices"));
$total_pages = ceil($total_rows / $limit);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-4 shadow-lg" style="border-radius: 15px; border: 1px solid #ccc;">
            <h2 class="mb-4" style="font-weight: 900; color: #2c3e50;">أرشيف الفواتير</h2>

            <form method="GET" class="row g-3 mb-5 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white"><i class="fa fa-search"></i></span>
                        <input type="text" name="search" class="form-control fw-bold" 
                               placeholder="ايحث عن عميل او رقم فاتوره ..." 
                               value="<?php echo $search; ?>" style="height: 60px; font-size: 20px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" style="height: 60px;">بحث</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr style="font-size: 18px;">
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">اسم العميل</th>
                            <th class="p-3">رقم الفاتورة</th>
                            <th class="p-3">مبلغ الفاتورة الكلي</th>
                            <th class="p-3">عدد الأصناف</th>
                            <th class="p-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 19px; font-weight: 800;">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['invoice_date']; ?></td>
                            <td class="text-primary"><?php echo $row['customer_name']; ?></td>
                            <td><span class="badge bg-secondary p-2"><?php echo $row['id']; ?></span></td>
                            <td class="text-success"><?php echo number_format($row['total_amount'], 2); ?> ج.س</td>
                            <td><?php echo $row['total_qty'] ?? 0; ?> قطعة</td>
                            <td>
                                <div class="btn-group gap-2">
                                    <a href="edit_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-warning fw-bold">
                                        <i class="fa fa-edit"></i> تعديل
                                    </a>
                                    <a href="view_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-info fw-bold text-white">
                                        <i class="fa fa-print"></i> عرض/طباعة
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link fw-bold px-4 py-2" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
    /* تنسيق الفواتير ليكون ضخماً وواضحاً */
    .table td { padding: 15px !important; border: 1px solid #ddd !important; }
    .pagination .page-link { color: #2c3e50; font-size: 18px; border: 2px solid #ddd; margin: 0 5px; border-radius: 8px; }
    .pagination .active .page-link { background-color: #2c3e50; border-color: #2c3e50; color: white; }
    
    /* تباعد الأزرار */
    .btn-group .btn { border-radius: 10px !important; padding: 10px 20px; }
</style>