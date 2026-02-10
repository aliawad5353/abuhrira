<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_wh = intval($_POST['from_warehouse']);
    $to_wh = intval($_POST['to_warehouse']);
    
    // 1. تجميع التاريخ مع التحقق لضمان عدم وجود أصفار [cite: 2026-02-03]
    if (!empty($_POST['day']) && !empty($_POST['month']) && !empty($_POST['year'])) {
        $day   = intval($_POST['day']);
        $month = intval($_POST['month']);
        $year  = intval($_POST['year']);
        $transfer_date = "$year-$month-$day";
    } else {
        // استخدام تاريخ اليوم في حال فشل الاستلام من الصفحة السابقة [cite: 2026-02-03]
        $transfer_date = date('Y-m-d'); 
    }
    
    $product_ids = $_POST['product_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];

    mysqli_begin_transaction($conn);

    try {
        // 2. تسجيل رأس عملية التحويل في جدول transfers مع التاريخ الصحيح [cite: 2026-01-27]
        $stmt_trans = mysqli_prepare($conn, "INSERT INTO transfers (from_warehouse_id, to_warehouse_id, transfer_date) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_trans, "iis", $from_wh, $to_wh, $transfer_date);
        mysqli_stmt_execute($stmt_trans);
        $transfer_id = mysqli_insert_id($conn);

        foreach ($product_ids as $index => $p_id) {
            $qty = intval($qtys[$index]);
            $p_id = intval($p_id);

            if ($qty <= 0) continue;

            // 3. التحقق من توفر الكمية في المخزن المصدر [cite: 2026-01-27]
            $check = mysqli_query($conn, "SELECT qty FROM stock_balances WHERE warehouse_id = $from_wh AND product_id = $p_id");
            $row = mysqli_fetch_assoc($check);

            if (!$row || $row['qty'] < $qty) {
                throw new Exception("الكمية المطلوبة لتحويل الصنف رقم $p_id غير متوفرة في المخزن المصدر!");
            }

            // 4. خصم الكمية من المخزن المصدر [cite: 2026-01-27]
            mysqli_query($conn, "UPDATE stock_balances SET qty = qty - $qty WHERE warehouse_id = $from_wh AND product_id = $p_id");

            // 5. إضافة الكمية للمخزن الهدف مع تحديث تاريخ الدخول (entry_date) لضمان ظهوره في التقارير [cite: 2026-02-03]
            $target_check = mysqli_query($conn, "SELECT id FROM stock_balances WHERE warehouse_id = $to_wh AND product_id = $p_id");
            if (mysqli_num_rows($target_check) > 0) {
                // تحديث الكمية وتاريخ الحركة في المخزن المستلم [cite: 2026-02-03]
                mysqli_query($conn, "UPDATE stock_balances SET qty = qty + $qty, entry_date = '$transfer_date' WHERE warehouse_id = $to_wh AND product_id = $p_id");
            } else {
                // إدراج سجل جديد للمخزن المستلم مع التاريخ [cite: 2026-01-27, 2026-02-03]
                mysqli_query($conn, "INSERT INTO stock_balances (warehouse_id, product_id, qty, entry_date) VALUES ($to_wh, $p_id, $qty, '$transfer_date')");
            }

            // 6. تسجيل تفاصيل التحويل في transfer_items [cite: 2026-01-27]
            mysqli_query($conn, "INSERT INTO transfer_items (transfer_id, product_id, qty) VALUES ($transfer_id, $p_id, $qty)");
        }

        mysqli_commit($conn);
        echo "<script>alert('✅ تم التحويل بنجاح وتحديث أرصدة المخازن'); window.location.href='transfers.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ خطأ: " . $e->getMessage() . "'); window.location.href='transfers.php';</script>";
    }
}
?>
