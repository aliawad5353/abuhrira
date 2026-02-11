<?php
/**
 * إعدادات الاتصال بقاعدة بيانات نظام أبو حريرة المحاسبي
 * تم التعديل بناءً على بيانات Railway الداخلية لضمان السرعة والأمان
 */

// جلب القيم من المتغيرات البيئية أو استخدام القيم المباشرة من الصورة
$host     = getenv('MYSQLHOST')     ?: 'mysql.railway.internal'; //
$user     = getenv('MYSQLUSER')     ?: 'root';                  //
$password = getenv('MYSQLPASSWORD') ?: 'ptUdBSoIyfsPheQnkPCOAOUotEgpvWMg'; // القيمة الظاهرة في صورتك
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';               //
$port     = getenv('MYSQLPORT')     ?: '3306';                  // المنفذ الداخلي

// محاولة إنشاء الاتصال
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// فحص الاتصال
if (!$conn) {
    // في حالة فشل الاتصال الداخلي، يتم عرض رسالة خطأ واضحة
    die("خطأ في الاتصال بقاعدة بيانات أبو حريرة: " . mysqli_connect_error());
}

// ضبط الإعدادات الأساسية للغة والتوقيت
mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Khartoum');

// تفعيل عرض الأخطاء مؤقتاً للتأكد من عمل الجداول
ini_set('display_errors', 1);
error_reporting(E_ALL);

// الاتصال ناجح، يمكنك الآن العمل على جداولك (invoices, users, etc.)
?>
