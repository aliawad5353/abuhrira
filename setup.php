<?php
// 1. استدعاء الاتصال (تأكد أن config.php يحتوي على كلمة السر الجديدة)
require_once 'config.php';

echo "<h3>جاري تهيئة نظام شركة أبو حريرة...</h3>";

// 2. مصفوفة الأوامر (تنظيف ثم إنشاء ثم إدخال بيانات)
$sql_commands = [
    // إيقاف الفحص ومسح الجداول القديمة
    "SET FOREIGN_KEY_CHECKS = 0",
    "DROP TABLE IF EXISTS `payments`, `اصناف_الفواتير`, `invoices`, `transfers`, `المشتريات`, `الاصناف`, `المخازن`, `users` ",
    "SET FOREIGN_KEY_CHECKS = 1",

    // إنشاء جدول المستخدمين
    "CREATE TABLE `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` VARCHAR(20) DEFAULT 'admin'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول المخازن
    "CREATE TABLE `المخازن` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `اسم_المخزن` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول الاصناف
    "CREATE TABLE `الاصناف` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `كود_الصنف` VARCHAR(100) UNIQUE,
        `اسم_الصنف` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول المشتريات
    "CREATE TABLE `المشتريات` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `تاريخ_الشراء` DATE NOT NULL,
        `مخزن_id` INT,
        `صنف_id` INT,
        `الكمية` INT NOT NULL,
        `سعر_الوحدة` DECIMAL(10,2),
        FOREIGN KEY (`مخزن_id`) REFERENCES `المخازن`(`id`),
        FOREIGN KEY (`صنف_id`) REFERENCES `الاصناف`(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول التحويلات
    "CREATE TABLE `transfers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `تاريخ_التحويل` DATE NOT NULL,
        `من_مخزن` INT,
        `الى_مخزن` INT,
        `صنف_id` INT,
        `الكمية` INT,
        FOREIGN KEY (`من_مخزن`) REFERENCES `المخازن`(`id`),
        FOREIGN KEY (`الى_مخزن`) REFERENCES `المخازن`(`id`),
        FOREIGN KEY (`صنف_id`) REFERENCES `الاصناف`(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول العملاء
    "CREATE TABLE `customers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20),
        `address` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إنشاء جدول الفواتير
    "CREATE TABLE `invoices` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_id` INT,
        `invoice_date` DATE NOT NULL,
        `total_amount` DECIMAL(10,2) DEFAULT 0.00,
        FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // إدخال البيانات الأساسية (المستخدم والمخازن الثلاثة)
    "INSERT INTO `users` (`username`, `password`, `role`) VALUES ('aliawad', '19821982', 'admin')",
    "INSERT INTO `المخازن` (`اسم_المخزن`) VALUES ('المخزن الرئيسي'), ('المخزن الفرعي'), ('المعرض')"
];

// 3. تنفيذ الأوامر واحداً تلو الآخر
foreach ($sql_commands as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ تم تنفيذ: " . substr($sql, 0, 30) . "... بنجاح<br>";
    } else {
        echo "❌ خطأ في: " . $conn->error . "<br>";
    }
}

echo "<h4>🎉 اكتمل التجهيز! يمكنك الآن تسجيل الدخول بـ aliawad.</h4>";
?>
