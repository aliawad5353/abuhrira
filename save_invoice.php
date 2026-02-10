<?php
include 'dashboard.php';
require_once 'config.php';

// 1. استقبال وتأمين البيانات
$warehouse_id = isset($_POST['warehouse_id']) ? intval($_POST['warehouse_id']) : 0;
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : (isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0);
$payment_type = $_POST['payment_type'] ?? 'cash';

if (!empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['day'])) {
    $invoice_date = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
} else {
    $invoice_date = date('Y-m-d'); 
}

$product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
$quantities = isset($_POST['qty']) ? $_POST['qty'] : [];

// جلب الأسماء الأساسية للعرض
$cust_res = mysqli_query($conn, "SELECT name FROM customers WHERE id=$customer_id");
$cust_name = ($cust_res && mysqli_num_rows($cust_res) > 0) ? mysqli_fetch_assoc($cust_res)['name'] : "عميل غير معروف";

$wh_res = mysqli_query($conn, "SELECT name FROM warehouses WHERE id=$warehouse_id");
$wh_name = ($wh_res && mysqli_num_rows($wh_res) > 0) ? mysqli_fetch_assoc($wh_res)['name'] : "مخزن غير معروف";

// متغير لمراقبة صلاحية المخزون
$stock_error = false;

// --- القسم الأول: صفحة تأكيد عملية البيع النهائية ---
if (isset($_POST['go_to_confirm'])) {
    $grand_total = floatval($_POST['temp_total']);
?>
    <div class="main-content" dir="rtl">
        <div class="container-fluid py-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center"><h4 class="fw-bold">تأكيد عملية البيع النهائية</h4></div>
                <div class="card-body p-0">
                    <table class="table mb-0 table-bordered">
                        <tr><td class="bg-light fw-bold" style="width:20%;">العميل:</td><td><?= $cust_name ?></td><td class="bg-light fw-bold" style="width:20%;">المخزن:</td><td><?= $wh_name ?></td></tr>
                    </table>
                    <table class="table table-bordered text-center mt-3">
                        <thead class="table-secondary">
                            <tr><th>كود الصنف</th><th>اسم الصنف</th><th>العدد</th><th>السعر</th><th>الإجمالي</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($product_ids as $idx => $pid) {
                                $qty = intval($quantities[$idx]);
                                $price = floatval($_POST['p_prices'][$idx]);
                                $p_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_name, product_code FROM products WHERE id=$pid"));
                                echo "<tr>
                                        <td>{$p_info['product_code']}</td>
                                        <td class='text-right px-3'>{$p_info['product_name']}</td>
                                        <td>$qty</td>
                                        <td>".number_format($price,2)."</td>
                                        <td class='fw-bold text-primary'>".number_format($qty*$price,2)."</td>
                                      </tr>";
                            } ?>
                        </tbody>
                    </table>
                    <div class="p-4 text-center" style="background: #fffbe6;"><h2 class="text-danger">إجمالي الفاتورة: <?= number_format($grand_total, 2) ?> ج.س</h2></div>
                    
                    <form action="confirm_final.php" method="POST" class="p-3">
                        <input type="hidden" name="warehouse_id" value="<?= $warehouse_id ?>">
                        <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
                        <input type="hidden" name="invoice_date" value="<?= $invoice_date ?>">
                        <input type="hidden" name="total_amount" value="<?= $grand_total ?>">
                        <?php foreach($product_ids as $idx => $pid): ?>
                            <input type="hidden" name="p_ids[]" value="<?= $pid ?>">
                            <input type="hidden" name="p_qtys[]" value="<?= $quantities[$idx] ?>">
                            <input type="hidden" name="p_prices[]" value="<?= $_POST['p_prices'][$idx] ?>">
                        <?php endforeach; ?>
                        <div class="row">
                            <div class="col-6"><button type="button" onclick="window.history.back()" class="btn btn-secondary w-100">رجوع للتعديل</button></div>
                            <div class="col-6"><button type="submit" name="confirm_btn" class="btn btn-success btn-lg w-100 fw-bold">تأكيد وحفظ نهائي</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php 
// --- القسم الثاني: صفحة مراجعة الأصناف قبل التأكيد (تظهر أولاً) ---
} else { ?>
    <div class="main-content" dir="rtl">
        <div class="container-fluid py-2">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="bg-dark p-2 fw-bold text-white text-center">مراجعة الأسعار والأرصدة في: <?= $wh_name ?></div>
                    <form method="POST">
                        <input type="hidden" name="warehouse_id" value="<?= $warehouse_id ?>">
                        <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
                        <input type="hidden" name="payment_type" value="<?= $payment_type ?>">
                        <input type="hidden" name="invoice_date" value="<?= $invoice_date ?>">
                        
                        <table class="table table-bordered text-center mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>الكود</th>
                                    <th>الصنف</th>
                                    <th>المتوفر</th>
                                    <th>الكمية المطلوبة</th>
                                    <th>السعر (من المشتريات)</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $temp_total = 0; 
                                foreach ($product_ids as $index => $p_id) {
                                    $p_id = intval($p_id);
                                    $qty = intval($quantities[$index]);
                                    
                                    $p_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_name, product_code FROM products WHERE id = $p_id"));
                                    $s_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty FROM stock_balances WHERE product_id = $p_id AND warehouse_id = $warehouse_id"));
                                    $stock = $s_data['qty'] ?? 0;

                                    // فحص إذا كانت الكمية المطلوبة أكبر من المتوفر
                                    $is_over = ($qty > $stock);
                                    if($is_over) $stock_error = true;

                                    $price_sql = "SELECT price FROM stock_balances 
                                                  WHERE product_id = $p_id 
                                                  ORDER BY (warehouse_id = $warehouse_id) DESC, price DESC 
                                                  LIMIT 1";
                                    $price_res = mysqli_query($conn, $price_sql);
                                    $price_data = mysqli_fetch_assoc($price_res);
                                    
                                    $price = floatval($price_data['price'] ?? 0);
                                    $sub = $qty * $price; 
                                    $temp_total += $sub; 
                                ?>
                                    <tr class="<?= $is_over ? 'table-danger' : '' ?>">
                                        <td><?= $p_data['product_code'] ?></td>
                                        <td class="text-right px-3"><?= $p_data['product_name'] ?></td>
                                        <td class="fw-bold <?= $is_over ? 'text-white' : 'text-danger' ?>" style="<?= $is_over ? 'background: red;' : '' ?>">
                                            <?= number_format($stock) ?>
                                            <?= $is_over ? '<br><small>(غير كافٍ)</small>' : '' ?>
                                        </td>
                                        <td class="<?= $is_over ? 'fw-bold' : '' ?>"><?= $qty ?></td>
                                        <td><?= number_format($price, 2) ?></td>
                                        <td class="text-primary fw-bold"><?= number_format($sub, 2) ?></td>
                                        <input type="hidden" name="product_id[]" value="<?= $p_id ?>">
                                        <input type="hidden" name="qty[]" value="<?= $qty ?>">
                                        <input type="hidden" name="p_prices[]" value="<?= $price ?>">
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="temp_total" value="<?= $temp_total ?>">
                        
                        <div class="p-3 bg-light text-center border-top">
                            <h3 class="mb-3">إجمالي القائمة: <?= number_format($temp_total, 2) ?> ج.س</h3>
                            
                            <?php if ($stock_error): ?>
                                <div class="alert alert-danger fw-bold">⚠️ عذراً، لا يمكن الاستمرار. توجد أصناف كميتها المطلوبة أكبر من المتوفر في المخزن.</div>
                                <button type="button" onclick="window.history.back()" class="btn btn-secondary btn-lg w-100">رجوع لتعديل الكميات</button>
                            <?php else: ?>
                                <button type="submit" name="go_to_confirm" class="btn btn-info text-white btn-lg w-100 fw-bold">تابع لصفحة التأكيد</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
