<?php
session_start();
// حماية الصفحة: إذا لم يسجل دخول، ارجعه لصفحة login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - شركة أبو حريرة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --active-color: #3498db;
            --text-color: #ecf0f1;
        }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        
        /* الشريط الجانبي - Sidebar */
        .sidebar { width: 280px; background-color: var(--sidebar-bg); height: 100vh; position: fixed; right: 0; color: var(--text-color); overflow-y: auto; box-shadow: -2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 25px 20px; text-align: center; font-size: 22px; font-weight: bold; border-bottom: 1px solid #34495e; background: #1a252f; }
        
        /* التبويبات المنسدلة */
        .menu-item { border-bottom: 1px solid #34495e; }
        .menu-btn { width: 100%; padding: 18px 20px; background: none; border: none; color: white; text-align: right; font-size: 17px; font-weight: bold; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; }
        .menu-btn:hover { background: #34495e; color: var(--active-color); }
        .submenu { background: #1a252f; display: none; }
        .submenu a { display: block; padding: 12px 40px; color: #bdc3c7; text-decoration: none; font-weight: bold; font-size: 15px; border-bottom: 1px dotted #2c3e50; transition: 0.3s; }
        .submenu a:hover { color: white; background: var(--active-color); padding-right: 50px; }

        /* المحتوى الرئيسي */
        .main-content { margin-right: 280px; width: calc(100% - 280px); padding: 30px; }
        .header-bar { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .user-info { font-weight: bold; color: #2c3e50; }
        .logout-link { color: #e74c3c; text-decoration: none; font-weight: bold; }

        /* منطقة الترحيب */
        .welcome-box { background: white; padding: 60px; border-radius: 20px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .welcome-box img { width: 250px; margin-bottom: 20px; }
        .welcome-box h2 { color: #2c3e50; font-size: 28px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-shoe-prints"></i> شركة أبو حريرة
    </div>
    
    <div class="menu-item">
        <button class="menu-btn" onclick="toggleSub('m1')">
            <span><i class="fas fa-warehouse"></i> إدارة المخازن</span>
            <i class="fas fa-chevron-down"></i>
        </button>
        <div id="m1" class="submenu">
            <a href="add_product.php">إضافة صنف جديد</a>
            <a href="purchases.php">المشتريات (توريد)</a>
            <a href="transfer.php">تحويل بين المخازن</a>
        </div>
    </div>

    <div class="menu-item">
        <button class="menu-btn" onclick="toggleSub('m2')">
            <span><i class="fas fa-users"></i> قسم العملاء</span>
            <i class="fas fa-chevron-down"></i>
        </button>
        <div id="m2" class="submenu">
            <a href="add_customer.php">تسجيل عميل جديد</a>
            <a href="customers_list.php">قائمة العملاء والديون</a>
            <a href="invoices_manage.php">إدارة الفواتير والتعديل</a>
        </div>
    </div>

    <div class="menu-item">
        <button class="menu-btn" onclick="toggleSub('m3')">
            <span><i class="fas fa-chart-pie"></i> التقارير المالية والجرد</span>
            <i class="fas fa-chevron-down"></i>
        </button>
        <div id="m3" class="submenu">
            <a href="inventory_report_v2.php">تقرير المخازن وتعديل السعر</a>
            <a href="sub_store_report.php">تقرير المخزن الفرعي</a>
            <a href="debt_management.php">المديونية وسداد العملاء</a>
            <a href="discounts_log.php">سجل الخصومات</a>
            <a href="total_inventory_overview.php">البضاعة الكلية (موقف عام)</a>
        </div>
    </div>

    <div class="menu-item">
        <a href="logout.php" class="menu-btn" style="text-decoration: none; color: #ff7675;">
            <span><i class="fas fa-power-off"></i> تسجيل الخروج</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="header-bar">
        <div class="user-info">
            <i class="fas fa-user-circle"></i> مرحباً: <?php echo $_SESSION['username']; ?>
        </div>
        <div>
            نظام الإدارة المتكامل | 2026
        </div>
    </div>

    <div class="welcome-box">
        <img src="assets/logo.png" alt="LUOFU Sudan">
        <h2>مرحباً بك في لوحة تحكم شركة أبو حريرة</h2>
        <p style="color: #7f8c8d; font-size: 18px;">الوكيل الحصري لأحذية LUOFU بالسودان</p>
        <hr style="width: 100px; border: 2px solid var(--active-color); margin: 20px auto;">
        <p>استخدم القائمة الجانبية اليمنى لإدارة العمليات اليومية ومتابعة تقاريرك المالية.</p>
    </div>
</div>

<script>
    function toggleSub(id) {
        let sub = document.getElementById(id);
        // إغلاق أي قائمة مفتوحة أخرى (اختياري لجعل الشكل أرتب)
        document.querySelectorAll('.submenu').forEach(el => {
            if(el.id !== id) el.style.display = 'none';
        });
        sub.style.display = (sub.style.display === 'block') ? 'none' : 'block';
    }
</script>

</body>
</html>
