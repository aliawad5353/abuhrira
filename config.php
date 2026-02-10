<?php
// استخدام متغيرات البيئة بدلاً من القيم الثابتة لضمان نجاح الاتصال
$host     = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user     = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: 'DhFlqmPwLsQTNJpjadlexdmsfTyCfMxu';
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';
$port     = getenv('MYSQLPORT') ?: '3306';

$conn = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("خطأ في الاتصال بقاعدة بيانات المحاسبة: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Khartoum');
?>
