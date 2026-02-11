<?php
/**
 * إعدادات الاتصال بقاعدة بيانات نظام أبو حريرة المحاسبي
 * تم التعديل بناءً على بيانات Railway الرسمية (فبراير 2026)
 */

// بيانات الاتصال التي زودتني بها
$host     = 'mysql.railway.internal'; 
$user     = 'root';                  
$password = 'ptUdBSoIyfsPheQnkPCOAOUotEgpvWMg'; 
$dbname   = 'railway';               
$port     = '3306';                  

// إنشاء الاتصال باستخدام mysqli
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// فحص الاتصال
if (!$conn) {
    // في حالة فشل الاتصال، عرض رسالة توضح السبب
    die("خطأ في الاتصال بقاعدة بيانات نظام أبو حريرة: " . mysqli_connect_error());
}

// ضبط الترميز لدعم اللغة العربية بشكل صحيح (UTF-8)
mysqli_set_charset($conn, "utf8mb4");

// ضبط التوقيت المحلي للسودان لضمان دقة التقارير والفواتير
date_default_timezone_set('Africa/Khartoum');

// تفعيل عرض الأخطاء (مفيد جداً للتأكد من عمل الجداول الجديدة)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// الاتصال ناجح وجاهز للعمل
?>
