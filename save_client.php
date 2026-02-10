<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات وتطهيرها
    // نركز هنا على الاسم (Name) لأنه العمود الفقري للفاتورة
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // التحقق من أن الاسم ليس فارغاً
    if (empty(trim($name))) {
        echo "<script>
                alert('⚠️ خطأ: يجب كتابة اسم العميل ليظهر في الفاتورة!');
                window.location.href = 'add_client.php';
              </script>";
        exit;
    }

    // إدخال بيانات العميل (بدون فحص تكرار الهاتف بناءً على طلبك)
    $sql = "INSERT INTO customers (name, phone, address) VALUES ('$name', '$phone', '$address')";
    
    if (mysqli_query($conn, $sql)) {
        // رسالة نجاح واضحة تظهر الاسم الذي تم حفظه
        echo "<div dir='rtl' style='text-align:center; margin-top:100px; font-family:tahoma;'>
                <div style='display:inline-block; padding:35px; border-radius:20px; border:3px solid #3498db; background-color:#f0f7ff; shadow: 0 10px 20px rgba(0,0,0,0.1);'>
                    <h1 style='color:#2980b9; margin-bottom:20px;'>✅ تم تسجيل العميل بنجاح</h1>
                    <p style='font-size:24px; font-weight:900; color:#2c3e50;'>الاسم: $name</p>
                    <p style='font-size:18px; color:#7f8c8d;'>سيكون هذا الاسم متاحاً الآن في قائمة استخراج الفواتير.</p>
                    <div style='margin-top:20px;'><i class='fa fa-spinner fa-spin'></i> جاري العودة...</div>
                </div>
              </div>";
        
        // العودة لصفحة الإضافة بعد ثانية ونصف
        header("refresh:1.5; url=add_client.php");
    } else {
        echo "حدث خطأ فني أثناء الحفظ: " . mysqli_error($conn);
    }
} else {
    header("Location: add_client.php");
}
?>
