<?php
/**
 * إعدادات الاتصال بقاعدة بيانات نظام أبو حريرة المحاسبي
 * تم التعديل بناءً على بيانات Railway الداخلية لضمان السرعة والأمان
 */

// جلب القيم من المتغيرات البيئية لـ Railway (الأكثر أماناً)
$host     = getenv('MYSQLHOST')     ?: 'mysql.railway.internal'; 
$user     = getenv('MYSQLUSER')     ?: 'root';                  
$password = getenv('MYSQLPASSWORD') ?: 'ptUdBSoIyfsPheQnkPCOAOUotEgpvWMg'; 
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';               
$port     = getenv('MYSQLPORT')     ?: '3306';                  

// إنشاء الاتصال باستخدام mysqli
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// فحص الاتصال
if (!$conn) {
    die("خطأ في الاتصال بقاعدة بيانات أبو حريرة: " . mysqli_connect_error());
}

// ضبط الإعدادات الأساسية للغة والتوقيت
mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Khartoum');

// تفعيل عرض الأخطاء مؤقتاً (مفيد جداً في مرحلة التأسيس)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// الاتصال ناجح
?>
