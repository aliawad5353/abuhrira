<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit; 
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نظام أبو حريرة - الوكيل الحصري</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --sidebar-bg: #2c3e50; --main-text: #333; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; margin: 0; overflow-x: hidden; }

        /* التنسيق المطلوب: خطوط كبيرة وعريضة */
        h2, h3, .nav-link, label, th { font-weight: 900 !important; }

        /* الشريط الجانبي الثابت */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; right: 0; top: 0; overflow-y: auto; z-index: 1000; }
        
        /* تعديل منطقة المحتوى وسحبها للأعلى بقوة كما طلبت */
        .main-content { margin-right: 260px; padding: 5px 25px; margin-top: -15px !important; }

        /* الهيدر المدمج (لوحة التحكم + الشركة + الترحيب) في سطر واحد */
        .top-bar { 
            background: #fff; 
            padding: 8px 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-right: 260px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-title-group { display: flex; align-items: center; gap: 12px; }
        .header-title-group h3 { font-size: 17px; margin: 0; color: #0d6efd; }
        .company-info-inline { font-size: 13px; font-weight: bold; color: #555; border-right: 2px solid #eee; padding-right: 12px; }
        .luofu-brand { color: #888; font-weight: 900; letter-spacing: 1px; }

        /* تنسيق الروابط والقوائم المنسدلة */
        .nav-link { font-size: 17px !important; color: #ecf0f1 !important; padding: 12px 20px; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .nav-link:hover { background: #34495e; }
        .submenu { background: #34495e; list-style: none; padding: 0; }
        .submenu .nav-link { padding-right: 45px; font-size: 15px !important; background: #3e5871; border-bottom: 1px solid #2c3e50; }
        
        /* إخفاء رسائل النجاح لتبقى الواجهة نظيفة */
        .alert-success, .tm-ejraa, #success-msg, .alert-info { display: none !important; }
        
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content, .top-bar { margin-right: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center p-3">
        <img src="assets/logo.png" style="width: 70px; border-radius: 50%;" onerror="this.style.display='none'">
        <h5 class="text-white mt-2" style="font-weight:900;">شركة أبو حريرة</h5>
        <small class="text-warning">الوكيل الحصري LUOFU</small>
    </div>
    <hr class="text-secondary my-1">
    
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="fa fa-home ms-2"></i> الرئيسية</a>
        
        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#stockMenu">
            <i class="fa fa-warehouse ms-2"></i> إدارة المخازن
        </a>
        <div class="collapse" id="stockMenu">
            <ul class="submenu">
                <li><a href="add_product.php" class="nav-link">إضافة صنف جديد</a></li>
                <li><a href="purchases.php" class="nav-link">المشتريات</a></li>
                <li><a href="transfers.php" class="nav-link">التحويل بين المخازن</a></li>
            </ul>
        </div>

        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#clientMenu">
            <i class="fa fa-users ms-2"></i> العملاء والفواتير
        </a>
        <div class="collapse" id="clientMenu">
            <ul class="submenu">
                <li><a href="add_client.php" class="nav-link">تسجيل عميل جديد</a></li>
                <li><a href="clients_list.php" class="nav-link">قائمة العملاء</a></li>
                <li><a href="invoices_list.php" class="nav-link">عرض الفواتير</a></li>
            </ul>
        </div>

        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#reportMenu">
            <i class="fa fa-chart-bar ms-2"></i> التقارير
        </a>
        <div class="collapse" id="reportMenu">
            <ul class="submenu">
                <li><a href="report_stock.php" class="nav-link">تقرير المخازن</a></li>
                <li><a href="report_sub_stock.php" class="nav-link">تقرير المخزن الفرعي</a></li>
                <li><a href="report_debt.php" class="nav-link">المديونية</a></li>
                <li><a href="report_discounts.php" class="nav-link">سجل الخصومات</a></li>
            </ul>
        </div>

        <a class="nav-link text-danger mt-3" href="logout.php"><i class="fa fa-sign-out-alt ms-2"></i> تسجيل الخروج</a>
    </nav>
</div>

<div class="top-bar">
    <div class="header-title-group">
        <h3>لوحة التحكم الرئيسية</h3>
        <div class="company-info-inline">
            <span class="luofu-brand">LUOFU SUDAN</span> 
            | مرحبا بك في نظام الإدارة الحصري لشركة أبو حريرة
        </div>
    </div>
    <span class="badge bg-dark p-2" style="font-size: 13px;">التاريخ: <?php echo date('Y/m/d'); ?></span>
</div>

<div class="main-content">
    <div class="container-fluid">
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
