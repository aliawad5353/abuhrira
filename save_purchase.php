<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. تجميع التاريخ مع التحقق لضمان عدم وجود أصفار 0000-00-00 [cite: 2026-01-27]
    if (!empty($_POST['day']) && !empty($_POST['month']) && !empty($_POST['year'])) {
        $day   = intval($_POST['day']);
        $month = intval($_POST['month']);
        $year  = intval($_POST['year']);
        $entry_date = "$year-$month-$day";
    } else {
        // إذا لم يصل التاريخ من الصفحة السابقة، يتم استخدام تاريخ اليوم الحالي تلقائياً [cite: 2026-01-27]
        $entry_date = date('Y-m-d'); 
    }

    // 2. جلب بيانات المخزن والمورد
    $warehouse_id = intval($_POST['warehouse_id']);
    $supplier     = mysqli_real_escape_string($conn, $_POST['supplier'] ?? '');
    
    // 3. استلام مصفوفات الأصناف
    $product_ids = $_POST['product_id'] ?? [];
    $qtys        = $_POST['qty'] ?? [];
    $prices      = $_POST['price'] ?? [];

    $errors = [];
    $success_count = 0;

    // 4. الدوران على كل صنف تمت إضافته في الفاتورة [cite: 2026-01-27]
    foreach ($product_ids as $index => $p_id) {
        $p_id  = intval($p_id);
        $qty   = intval($qtys[$index]);
        $price = floatval($prices[$index]);

        if ($p_id > 0 && $qty > 0) {
            // التحقق من وجود الصنف في المخزن لتحديثه أو إضافته [cite: 2026-01-27]
            $check_stock = mysqli_query($conn, "SELECT id FROM stock_balances WHERE warehouse_id = $warehouse_id AND product_id = $p_id");

            if (mysqli_num_rows($check_stock) > 0) {
                // تحديث الكمية والسعر والتاريخ لضمان ظهوره في سجل الحركة [cite: 2026-01-27]
                $update_sql = "UPDATE stock_balances 
                               SET qty = qty + $qty, 
                                   price = $price, 
                                   entry_date = '$entry_date' 
                               WHERE warehouse_id = $warehouse_id AND product_id = $p_id";
                
                if (mysqli_query($conn, $update_sql)) {
                    $success_count++;
                } else {
                    $errors[] = "خطأ في تحديث الصنف رقم $p_id: " . mysqli_error($conn);
                }
            } else {
                // إضافة سجل جديد في حال لم يكن الصنف موجوداً في هذا المخزن مسبقاً [cite: 2026-01-27]
                $insert_sql = "INSERT INTO stock_balances (warehouse_id, product_id, qty, price, entry_date) 
                               VALUES ($warehouse_id, $p_id, $qty, $price, '$entry_date')";
                
                if (mysqli_query($conn, $insert_sql)) {
                    $success_count++;
                } else {
                    $errors[] = "خطأ في إضافة الصنف رقم $p_id: " . mysqli_error($conn);
                }
            }
        }
    }

    // 5. رسالة النتيجة النهائية
    if ($success_count > 0 && empty($errors)) {
        echo "<script>
                alert('✅ تم حفظ المشتريات بنجاح وتحديث أرصدة المخازن ($success_count صنف)');
                window.location.href = 'purchases.php';
              </script>";
    } elseif (!empty($errors)) {
        echo "<div dir='rtl' style='color:red; font-family:tahoma; padding:20px; background:#fff5f5; border:1px solid red; border-radius:10px;'>";
        echo "<h3>⚠️ حدثت بعض الأخطاء أثناء الحفظ:</h3><ul>";
        foreach ($errors as $error) { echo "<li>$error</li>"; }
        echo "</ul><a href='purchases.php' style='display:inline-block; margin-top:10px; padding:10px 20px; background:red; color:white; text-decoration:none; border-radius:5px;'>العودة للمشتريات</a></div>";
    } else {
        echo "<script>
                alert('⚠️ لم يتم إضافة أي أصناف صحيحة.');
                window.location.href = 'purchases.php';
              </script>";
    }
} else {
    header("Location: purchases.php");
}
?>
