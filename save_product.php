<?php
require_once 'config.php';

// التأكد من أن البيانات قادمة عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // استلام البيانات وتطهيرها من الثغرات
    $product_code = mysqli_real_escape_string($conn, $_POST['product_code']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);

    // 1. التحقق من عدم تكرار كود الصنف
    $check_query = "SELECT id FROM products WHERE product_code = '$product_code'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // إذا كان الكود موجود مسبقاً
        echo "<script>
                alert('خطأ: كود الصنف هذا مسجل مسبقاً لصنف آخر!');
                window.location.href = 'add_product.php';
              </script>";
    } else {
        // 2. إدخال الصنف الجديد
        $sql = "INSERT INTO products (product_code, product_name) VALUES ('$product_code', '$product_name')";
        
        if (mysqli_query($conn, $sql)) {
            // نجاح العملية
            echo "<div dir='rtl' style='text-align:center; margin-top:50px; font-family:tahoma;'>
                    <h2 style='color:green;'>✅ تم حفظ الصنف بنجاح!</h2>
                    <p>جاري إعادتك لصفحة الإضافة...</p>
                  </div>";
            
            // تحويل المستخدم بعد 2 ثانية
            header("refresh:2; url=add_product.php");
        } else {
            // فشل العملية
            echo "خطأ في الحفظ: " . mysqli_error($conn);
        }
    }
} else {
    // منع الدخول المباشر للملف
    header("Location: add_product.php");
}
?>
