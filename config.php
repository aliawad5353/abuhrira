<?php
/**
 * ملف إعدادات الاتصال بقاعدة البيانات - شركة أبو حريرة للأحذية
 * 2026-01-31
 */

// إعدادات الاتصال الخاصة بـ Railway
$host     = "mysql.railway.internal"; // المضيف الداخلي
$user     = "root";                   // اسم المستخدم
$password = "ptUdBSoIyfsPheQnkPCOAOUotEgpvWMg"; // كلمة المرور الخاصة بك
$dbname   = "railway";                // اسم قاعدة البيانات
$port     = "3306";                   // المنفذ الافتراضي

// إنشاء الاتصال
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// التحقق من نجاح الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

// ضبط الترميز للغة العربية لضمان قراءة وكتابة البيانات بشكل سليم
mysqli_set_charset($conn, "utf8mb4");

// تعيين المنطقة الزمنية (اختياري لضبط توقيت السودان/الخرطوم)
date_default_timezone_set('Africa/Khartoum');

?>
