<?php
/**
 * ملف إعدادات الاتصال بقاعدة البيانات - شركة أبو حريرة للأحذية
 * تم التحديث ليتطابق مع إعدادات Railway الحقيقية لعام 2026
 */

$host     = "mysql.railway.internal"; 
$user     = "root";                   
$password = "DhFlqmPwLsQTNJpjadlexdmsfTyCfMxu"; 
$dbname   = "railway";                
$port     = "3306";                   

// إنشاء الاتصال
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// التحقق من نجاح الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

// ضبط الترميز للغة العربية لضمان قراءة البيانات بشكل سليم
mysqli_set_charset($conn, "utf8mb4");

// تعيين المنطقة الزمنية للسودان
date_default_timezone_set('Africa/Khartoum');

?>
