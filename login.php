<?php 
// 1. بدء الجلسة لضمان أمن النظام
session_start(); 

// 2. استدعاء ملف الاتصال بقاعدة البيانات
require_once 'config.php'; 

// 3. منع المستخدم من رؤية صفحة الدخول إذا كان مسجلاً بالفعل
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // بيانات الدخول الخاصة بك
    $validUser = 'aliawad';
    $validPass = '19821982';

    if ($username === $validUser && $password === $validPass) {
        // تخزين بيانات المستخدم في الجلسة
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $validUser;
        $_SESSION['role'] = 'admin';
        
        // التوجيه لصفحة اللوحة الرئيسية (dashboard)
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ اسم المستخدم أو كلمة المرور غير صحيحة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول - شركة أبو حريرة</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
  <style>
    body {
      background: #2c3e50; 
      background-size: cover;
      font-family: 'Tahoma', sans-serif;
    }
    .login-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 16px;
      width: 450px;
      box-shadow: 0 12px 28px rgba(0,0,0,0.25);
      text-align: center;
    }
    /* تنسيق الشعار كما في كودك */
    .logo { width: 120px; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; text-align: right; }
    .btn-login { width: 100%; height: 50px; font-size: 18px; font-weight: bold; }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card">
    <img src="assets/logo.png" alt="شعار الشركة" class="logo">
    <h3>شركة أبو حريرة للأحذية</h3>
    <h4>تسجيل الدخول</h4>

    <?php if ($error): ?>
        <div class='alert alert-danger'><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="form-group">
        <label>اسم المستخدم</label>
        <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم" required>
      </div>
      <div class="form-group">
        <label>كلمة المرور</label>
        <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
      </div>
      <button type="submit" class="btn btn-warning btn-login">دخول النظام</button>
    </form>
  </div>
</div>

</body>
</html>