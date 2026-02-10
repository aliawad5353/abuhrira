<?php
// استدعاء ملف الاتصال الذي أرفقته أنت
require_once 'config.php';

echo "<div dir='rtl' style='font-family:tahoma; padding:20px; line-height:2;'>";
echo "<h2 style='color:navy;'>جاري تهيئة نظام شركة أبو حريرة (LUOFU)...</h2>";
echo "<hr>";

// 1. إيقاف التحقق من القيود مؤقتاً للحذف النظيف
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

// 2. قائمة بجميع الجداول المطلوب مسحها وإنشاؤها
$tables = [
    'users', 'warehouses', 'products', 'customers', 
    'stock_balances', 'invoices', 'invoice_items', 
    'transfers', 'transfer_items', 'discounts_log'
];

foreach ($tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS $table");
    echo "🗑️ تم حذف الجدول القديم (إن وجد): $table <br>";
}

// 3. تعريف استعلامات الإنشاء
$queries = [
    // جدول المستخدمين (ليعمل كود login الخاص بك)
    "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'admin'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    // جدول المخازن
    "CREATE TABLE warehouses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول الأصناف
    "CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_code VARCHAR(50) UNIQUE,
        product_name VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول العملاء
    "CREATE TABLE customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        phone VARCHAR(20),
        address TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول مخزون الأصناف (المشتريات والرصيد)
    "CREATE TABLE stock_balances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        warehouse_id INT,
        product_id INT,
        qty INT DEFAULT 0,
        price DECIMAL(15,2) DEFAULT 0.00,
        entry_date DATE,
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول الفواتير (الرأس)
    "CREATE TABLE invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        warehouse_id INT,
        invoice_date DATE,
        total_amount DECIMAL(15,2) DEFAULT 0.00,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول تفاصيل الفواتير (الأصناف داخل الفاتورة)
    "CREATE TABLE invoice_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT,
        product_id INT,
        qty INT,
        unit_price DECIMAL(15,2),
        total_price DECIMAL(15,2),
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول التحويلات بين المخازن
    "CREATE TABLE transfers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        from_warehouse_id INT,
        to_warehouse_id INT,
        transfer_date DATE,
        FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
        FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // تفاصيل التحويلات
    "CREATE TABLE transfer_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transfer_id INT,
        product_id INT,
        qty INT,
        FOREIGN KEY (transfer_id) REFERENCES transfers(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // جدول سجل الخصومات والمديونية
    "CREATE TABLE discounts_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        discount_amount DECIMAL(15,2),
        discount_date DATE,
        notes TEXT,
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

// 4. تنفيذ استعلامات الإنشاء
foreach ($queries as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "✅ تم إنشاء جدول بنجاح... <br>";
    } else {
        die("❌ خطأ في إنشاء الجداول: " . mysqli_error($conn));
    }
}

// 5. إضافة البيانات الأساسية (المستخدم والمخازن)
mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('aliawad', '19821982', 'admin')");
mysqli_query($conn, "INSERT INTO warehouses (name) VALUES ('المخزن الرئيسي'), ('المخزن الفرعي'), ('المعرض')");

echo "<hr><h3 style='color:green;'>🎉 تمت العملية بنجاح! قاعدة البيانات جاهزة الآن.</h3>";
echo "<a href='login.php' style='display:inline-block; padding:10px 20px; background:navy; color:white; text-decoration:none; border-radius:5px;'>انتقل لتسجيل الدخول</a>";

// تفعيل القيود مرة أخرى
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
?>