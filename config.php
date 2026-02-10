<?php
// إعدادات الاتصال بقاعدة بيانات النظام المحاسبي - Railway
$host     = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user     = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: 'DhFlqmPwLsQTNJpjadlexdmsfTyCfMxu';
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';
$port     = getenv('MYSQLPORT') ?: '3306';

// إنشاء الاتصال
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// التحقق من الاتصال
if (!$conn) {
    die("خطأ في الاتصال بقاعدة بيانات النظام: " . mysqli_connect_error());
}

// ضبط الترميز لدعم اللغة العربية في الفواتير والتقارير
mysqli_set_charset($conn, "utf8mb4");

// ضبط التوقيت المحلي للسودان (ضروري لضبط وقت الفواتير والمشتريات)
date_default_timezone_set('Africa/Khartoum');

// تفعيل عرض الأخطاء مؤقتاً لمساعدتنا في اكتشاف أي مشكلة برمجية أخرى
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
