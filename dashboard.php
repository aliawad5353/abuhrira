<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
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
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; }
        
        /* التنسيق المطلوب: خطوط كبيرة وعريضة */
        h2, h3, .nav-link, label, th { font-weight: 900 !important; }
        .nav-link { font-size: 18px; color: #ecf0f1 !important; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .nav-link:hover { background: #1a252f; color: #f1c40f !important; }

        /* الشريط الجانبي مثل الصورة */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; right: 0; top: 0; overflow-y: auto; }
        .main-content { margin-right: 260px; padding: 30px; }

        /* تنسيق المربعات والتباعد */
        .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 25px; border: none; }
        .form-control, .select2-container .select2-selection--single {
            height: 50px !important; font-size: 18px !important; font-weight: bold !important;
            margin-bottom: 15px !important; border: 2px solid #ddd !important; border-radius: 8px !important;
        }
        
        /* أيقونة السهم للقوائم المنسدلة */
        .has-submenu::after { content: '\f107'; font-family: 'Font Awesome 6 Free'; font-weight: 900; float: left; transition: 0.3s; }
        .collapsed .has-submenu::after { transform: rotate(-90deg); }
        .submenu { background: #34495e; list-style: none; padding: 0; }
        .submenu .nav-link { padding-right: 40px; font-size: 16px; background: #3e5871; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center p-3">
        <img src="assets/logo.png" style="width: 80px; border-radius: 50%;">
        <h5 class="text-white mt-2" style="font-weight:900;">شركة أبو حريرة</h5>
        <small class="text-warning">الوكيل الحصري LUOFU</small>
    </div>
    <hr class="text-secondary">
    
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="fa fa-home ms-2"></i> الرئيسية</a>
        
        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#stockMenu">
            <i class="fa fa-warehouse ms-2"></i> إدارة المخازن
        </a>
        <div class="collapse" id="stockMenu">
            <ul class="submenu">
                <li><a href="add_product.php" class="nav-link"> إضافة صنف جديد</a></li>
                <li><a href="purchases.php" class="nav-link"> المشتريات</a></li>
                <li><a href="transfers.php" class="nav-link"> التحويل بين المخازن</a></li>
            </ul>
        </div>

        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#clientMenu">
            <i class="fa fa-users ms-2"></i> العملاء
        </a>
        <div class="collapse" id="clientMenu">
            <ul class="submenu">
                <li><a href="add_client.php" class="nav-link"> تسجيل عميل جديد</a></li>
                <li><a href="clients_list.php" class="nav-link"> قائمة العملاء</a></li>
                <li><a href="invoices_list.php" class="nav-link"> الفواتير</a></li>
            </ul>
        </div>

        <a class="nav-link has-submenu" data-bs-toggle="collapse" href="#reportMenu">
            <i class="fa fa-chart-bar ms-2"></i> التقارير
        </a>
        <div class="collapse" id="reportMenu">
            <ul class="submenu">
                <li><a href="report_stock.php" class="nav-link"> تقرير المخازن</a></li>
                <li><a href="report_sub_stock.php" class="nav-link"> تقرير المخزن الفرعي</a></li>
                <li><a href="report_debt.php" class="nav-link"> المديونية</a></li>
                <li><a href="report_discounts.php" class="nav-link"> سجل الخصومات</a></li>
            </ul>
        </div>

        <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out-alt ms-2"></i> تسجيل الخروج</a>
    </nav>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12 bg-white p-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                <h3 class="m-0 text-primary">لوحة التحكم الرئيسية</h3>
                <span class="badge bg-dark p-2" style="font-size: 16px;">التاريخ: <?php echo date('Y/m/d'); ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center mt-5">
                <h1 style="font-size: 50px; color: #ccc; font-weight: 900;">LUOFU SUDAN</h1>
                <p class="text-muted">مرحباً بك في نظام الإدارة الحصري لشركة أبو حريرة</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>