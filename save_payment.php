<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. استلام البيانات مع التأمين ضد القيم الفارغة [cite: 2026-01-27]
    $customer_id     = intval($_POST['customer_id']);
    $discount_amount = floatval($_POST['discount_amount']);
    $payment_date    = !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
    $notes           = "سداد مديونية نقدي";

    if ($customer_id > 0 && $discount_amount > 0) {
        // 2. الحفظ في جدول الخصومات [cite: 2026-01-27]
        $sql = "INSERT INTO discounts_log (customer_id, discount_amount, discount_date, notes) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "idss", $customer_id, $discount_amount, $payment_date, $notes);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('✅ تم تسجيل الخصم بنجاح بقيمة $discount_amount ج.س');
                    // التحويل مباشرة لتقرير المديونية الخاص بك [cite: 2026-02-03]
                    window.location.href = 'report_debt.php'; 
                  </script>";
        } else {
            echo "❌ خطأ في قاعدة البيانات: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('⚠️ مبلغ السداد غير صحيح.'); window.location.href = 'report_debt.php';</script>";
    }
} else {
    header("Location: report_debt.php");
}
?>
