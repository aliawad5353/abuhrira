<?php
// 1. بدء الجلسة والتحقق من الأمان (يجب أن يكون في أول سطر)
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. استدعاء ملف الاتصال بقاعدة البيانات
require_once 'config.php';

// 3. معالجة البيانات المرسلة (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id    = $_POST['client_id'];
    $store_id     = $_POST['store_id'];
    $total_amount = $_POST['total_amount'];
    $p_ids        = $_POST['p_ids'];
    $qtys         = $_POST['qtys'];
    $date_today   = date('Y-m-d');

    // هنا تضع باقي كود الإدخال في قاعدة البيانات (SQL Insert)
}
?>

    // 1. تسجيل الفاتورة في جدول الفواتير
    $query_invoice = "INSERT INTO invoices (client_id, store_id, total_amount, invoice_date) 
                      VALUES ('$client_id', '$store_id', '$total_amount', '$date_today')";
    
    if (mysqli_query($conn, $query_invoice)) {
        $invoice_id = mysqli_insert_id($conn); // الحصول على رقم الفاتورة الجديد

        // 2. خصم كل صنف من المخزن المختار
        for ($i = 0; $i < count($p_ids); $i++) {
            $pid = $p_ids[$i];
            $qty = $qtys[$i];

            // أمر التحديث (طرح الكمية المباعة من المتوفر)
            $update_stock = "UPDATE stock_balances 
                             SET quantity = quantity - $qty 
                             WHERE product_id = '$pid' AND store_id = '$store_id'";
            mysqli_query($conn, $update_stock);

            // حفظ تفاصيل الأصناف داخل الفاتورة للرجوع إليها لاحقاً
            $query_items = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price) 
                            VALUES ('$invoice_id', '$pid', '$qty', 
                            (SELECT price FROM stock_balances WHERE product_id = '$pid' AND store_id = '$store_id' LIMIT 1))";
            mysqli_query($conn, $query_items);
        }

        // 3. التحويل لصفحة العملاء بعد النجاح
        echo "<script>alert('تم تأكيد البيع وخصم الكميات من المخزن بنجاح'); window.location.href='customers_list.php';</script>";
    } else {
        echo "خطأ في تنفيذ العملية: " . mysqli_error($conn);
    }
}
?>
