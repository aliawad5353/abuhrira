<?php
// إعدادات الاتصال بقاعدة بيانات النظام المحاسبي - Railway (نسخة معدلة)

// نستخدم getenv لجلب البيانات من Variables التي أضفتها أنت، وإذا لم يجدها يستخدم القيم اليدوية التي أرسلتها
$host     = getenv('MYSQLHOST') ?: 'abuhrira.railway.internal';
$user     = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '09d3fc02-8ab0-4e52-bac4-d74f9391fdfa'; // تم تحديثها بناءً على صورتك
$dbname   = getenv('MYSQLDATABASE') ?: 'railway';
$port     = getenv('MYSQLPORT') ?: '3306';

// إنشاء الاتصال
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// التحقق من الاتصال
if (!$conn) {
    // إذا فشل الاتصال الداخلي، نحاول الاتصال الخارجي كخطة بديلة (fallback)
    $host_external = 'happy-passion-production-abuhrira.up.railway.app'; 
    $conn = mysqli_connect($host_external, $user, $password, $dbname, $port);
    
    if (!$conn) {
        die("خطأ في الاتصال بقاعدة بيانات النظام: " . mysqli_connect_error());
    }
}

// ضبط الترميز لدعم اللغة العربية في الفواتير والتقارير
mysqli_set_charset($conn, "utf8mb4");

// ضبط التوقيت المحلي للسودان (ضروري لضبط وقت الفواتير والمشتريات)
date_default_timezone_set('Africa/Khartoum');

// تفعيل عرض الأخطاء لمساعدتنا في اكتشاف أي مشكلة في الجداول (مثل invoices أو users)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
