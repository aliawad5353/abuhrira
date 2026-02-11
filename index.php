<?php 
// 1. بدء الجلسة
session_start(); 

// 2. استدعاء ملف الاتصال (الذي يحتوي على بيانات Railway)
require_once 'config.php'; 

// 3. إذا كان المستخدم مسجل دخول بالفعل، انقله للدشبورد
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // الاستعلام من جدول users الذي أنشأناه
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // تخزين بيانات الجلسة
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
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
    body { background: #2c3e50; font-family: 'Tahoma', sans-serif; }
    .login-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px; border-radius: 16px; width: 420px;
      box-shadow: 0 12px 28px rgba(0,0,0,0.25); text-align: center;
    }
    .logo { width: 100px; margin-bottom: 20px; border-radius: 50%; }
    .form-group { margin-bottom: 20px; text-align: right; }
    .btn-login { width: 100%; height: 50px; font-size: 18px; font-weight: bold; background-color: #f39c12; border: none; color: white; }
    .btn-login:hover { background-color: #e67e22; }
  </style>
</head>
<body>
<div class="login-container">
  <div class="login-card">
    <img src="assets/logo.png" alt="شعار الشركة" class="logo" onerror="this.src='https://via.placeholder.com/100?text=LUOFU'">
    <h3 class="mb-1">شركة أبو حريرة</h3>
    <p class="text-muted mb-4">نظام الإدارة الحصري - LUOFU</p>

    <?php if ($error): ?>
        <div class='alert alert-danger py-2'><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="form-group">
        <label class="fw-bold mb-1">اسم المستخدم</label>
        <input type="text" name="username" class="form-control text-center" placeholder="أدخل اسم المستخدم" required>
      </div>
      <div class="form-group">
        <label class="fw-bold mb-1">كلمة المرور</label>
        <input type="password" name="password" class="form-control text-center" placeholder="أدخل كلمة المرور" required>
      </div>
      <button type="submit" class="btn btn-login">دخول النظام</button>
    </form>
  </div>
</div>
</body>
</html>
