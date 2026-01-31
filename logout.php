<?php
// بدء الجلسة للوصول إليها
session_start();

// مسح جميع متغيرات الجلسة
$_SESSION = array();

// تدمير الجلسة نهائياً من السيرفر
session_destroy();

// توجيه المستخدم فوراً إلى صفحة تسجيل الدخول (login.php)
header("Location: login.php");
exit;
?>
