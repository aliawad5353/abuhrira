<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config.php'; // ثم يأتي كود الاتصال وقاعدة البيانات بعد ذلك
?>
<!DOCTYPE html>
...

<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل عميل جديد - شركة أبو حريرة</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 600px; margin: auto; border-right: 8px solid #2980b9; }
        h2 { font-weight: bold; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; }
        label { font-weight: bold; font-size: 18px; margin-bottom: 8px; }
        input { height: 45px; border: 2px solid #ddd; border-radius: 8px; padding: 0 15px; font-size: 17px; font-weight: bold; }
        .btn-save { background-color: #27ae60; color: white; border: none; padding: 15px; font-size: 20px; font-weight: bold; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 10px; }
        .btn-save:disabled { background-color: #95a5a6; }
    </style>
</head>
<body>
<div class="form-card">
    <h2><i class="fas fa-user-plus"></i> تسجيل عميل جديد</h2>
    <form action="save_customer.php" method="POST">
        <div class="form-group">
            <label>اسم العميل:</label>
            <input type="text" name="customer_name" required placeholder="أدخل اسم العميل الكامل">
        </div>
        <div class="form-group">
            <label>رقم الهاتف:</label>
            <input type="text" name="phone" required placeholder="09xxxxxxx">
        </div>
        <div class="form-group">
            <label>السكن:</label>
            <input type="text" name="address" required placeholder="العنوان بالتفصيل">
        </div>
        <button type="submit" class="btn-save" onclick="this.disabled=true; this.form.submit();">حفظ العميل</button>
    </form>
</div>
</body>
</html>
