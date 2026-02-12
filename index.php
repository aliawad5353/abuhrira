<?php
session_start();
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // استعلام بسيط جداً للتأكد من وجود المستخدم
    $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $message = "<h2 style='color:green;'>✅ نجح الاتصال! قاعدة البيانات تعمل والمستخدم موجود.</h2>";
    } else {
        $message = "<h2 style='color:red;'>❌ فشل: اسم المستخدم أو كلمة المرور غير صحيحة أو الجدول غير موجود.</h2>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<body>
    <h1>اختبار نظام أبو حريرة</h1>
    <form method="post">
        <input type="text" name="username" placeholder="اسم المستخدم" required><br><br>
        <input type="password" name="password" placeholder="كلمة المرور" required><br><br>
        <button type="submit">اختبار الدخول</button>
    </form>
    <hr>
    <?php echo $message; ?>
</body>
</html>
