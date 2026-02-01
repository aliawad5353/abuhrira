<?php
// إعدادات شركة أبو حريرة - اتصال مباشر ونهائي
$conn = mysqli_connect("mysql.railway.internal", "root", "DhFlqmPwLsQTNJpjadlexdmsfTyCfMxu", "railway", "3306");

if (!$conn) {
    die("فشل الاتصال: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Khartoum');
?>
