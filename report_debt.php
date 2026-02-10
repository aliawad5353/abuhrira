<?php
include 'dashboard.php';
require_once 'config.php';

// 1. حساب المديونية الكلية لجميع العملاء (إجمالي الفواتير - إجمالي الخصومات)
$total_invoices_sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM invoices"))['total'] ?? 0;
$total_discounts_sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(discount_amount) as total FROM discounts_log"))['total'] ?? 0;
$grand_total_debt = $total_invoices_sum - $total_discounts_sum;

// 2. محرك البحث عن عميل
$search = $_GET['search'] ?? '';

// 3. جلب بيانات كل عميل ومديونيته
$sql = "SELECT c.id, c.name, 
        IFNULL((SELECT SUM(total_amount) FROM invoices WHERE customer_id = c.id), 0) as total_bought,
        IFNULL((SELECT SUM(discount_amount) FROM discounts_log WHERE customer_id = c.id), 0) as total_paid
        FROM customers c
        WHERE c.name LIKE '%$search%'
        ORDER BY c.name ASC";

$result = mysqli_query($conn, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-5 align-items-center">
            <div class="col-md-7">
                <h2 style="font-weight: 900; color: #2c3e50;">
                    <i class="fa fa-hand-holding-usd ms-2"></i> تقرير مديونية العملاء
                </h2>
            </div>
            <div class="col-md-5">
                <div class="card bg-dark text-white p-3 text-center shadow-lg" style="border-right: 10px solid #e74c3c; border-radius: 15px;">
                    <h4 style="font-weight: 900; margin-bottom: 5px;">المديونية الكلية المستحقة</h4>
                    <h2 style="font-weight: 900; color: #f1c40f;"><?php echo number_format($grand_total_debt, 2); ?> ج.س</h2>
                </div>
            </div>
        </div>

        <div class="card p-5 shadow-lg" style="border-radius: 20px;">
            <form method="GET" class="row g-3 mb-5">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control fw-bold" 
                           placeholder="ابحث عن عميل لمراجعة مديونيته..." 
                           value="<?php echo $search; ?>" style="height: 60px; font-size: 20px;">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" style="height: 60px;">بـحـث</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr style="font-size: 18px; font-weight: 900;">
                            <th class="p-4">اسم العميل</th>
                            <th class="p-4">إجمالي المديونية (الفواتير)</th>
                            <th class="p-4">المسدد (الخصومات)</th>
                            <th class="p-4">المتبقي الحالي</th>
                            <th class="p-4">إجراء سداد</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 20px; font-weight: 800;">
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $current_balance = $row['total_bought'] - $row['total_paid'];
                            if($current_balance <= 0 && $search == "") continue; // إخفاء من ليس عليه ديون إلا في حالة البحث
                        ?>
                        <tr>
                            <td class="p-3 text-primary"><?php echo $row['name']; ?></td>
                            <td class="p-3"><?php echo number_format($row['total_bought'], 2); ?></td>
                            <td class="p-3 text-success"><?php echo number_format($row['total_paid'], 2); ?></td>
                            <td class="p-3 text-danger" style="font-size: 24px;"><?php echo number_format($current_balance, 2); ?></td>
                            <td class="p-3">
                                <button onclick="openPaymentModal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $current_balance; ?>)" 
                                        class="btn btn-warning btn-lg fw-bold w-100 shadow-sm" style="border-radius: 12px;">
                                    <i class="fa fa-money-bill-alt ms-1"></i> سـداد
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4" style="border-radius: 20px; border: 3px solid #f1c40f;">
      <div class="modal-header border-0">
        <h3 class="modal-title fw-bold">سداد مديونية: <span id="modal_cust_name" class="text-primary"></span></h3>
      </div>
      <form action="save_payment.php" method="POST" id="paymentForm">
          <input type="hidden" name="customer_id" id="modal_cust_id">
          <div class="modal-body">
              <div class="mb-4">
                  <label class="fw-bold fs-5 mb-2">التاريخ:</label>
                  <input type="date" name="payment_date" class="form-control fw-bold" value="<?php echo date('Y-m-d'); ?>" style="height: 50px;">
              </div>
              <div class="mb-4">
                  <label class="fw-bold fs-5 mb-2">الجملة الكلية للمديونية:</label>
                  <input type="text" id="modal_total_debt" class="form-control fw-bold bg-light" readonly style="height: 50px; font-size: 20px;">
              </div>
              <div class="mb-4">
                  <label class="fw-bold fs-5 mb-2 text-danger">مبلغ الخصم (المدفوع حالياً):</label>
                  <input type="number" name="discount_amount" class="form-control fw-bold border-danger" placeholder="0.00" required style="height: 60px; font-size: 24px;">
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="submit" id="modalSaveBtn" class="btn btn-success btn-lg w-100 fw-bold py-3">تأكيد وحفظ الخصم</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openPaymentModal(id, name, total) {
    document.getElementById('modal_cust_id').value = id;
    document.getElementById('modal_cust_name').innerText = name;
    document.getElementById('modal_total_debt').value = total.toLocaleString() + ' ج.س';
    var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    myModal.show();
}

// منع تكرار الحفظ
document.getElementById('paymentForm').onsubmit = function() {
    document.getElementById('modalSaveBtn').disabled = true;
    document.getElementById('modalSaveBtn').innerHTML = "جاري معالجة السداد...";
};
</script>

<style>
    .table th { border: 1px solid #444 !important; }
    .form-control { border: 2px solid #ced4da !important; border-radius: 10px !important; }
    .btn-warning:hover { background-color: #d4ac0d !important; }
</style>
