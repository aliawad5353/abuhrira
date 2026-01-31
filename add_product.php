<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة صنف جديد - شركة أبو حريرة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .form-container { background: white; max-width: 800px; margin: 30px auto; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-right: 8px solid #2c3e50; }
        h2 { color: #2c3e50; font-weight: bold; font-size: 28px; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 25px; display: flex; flex-direction: column; }
        label { font-weight: bold; font-size: 18px; color: #34495e; margin-bottom: 10px; }
        
        /* تنسيق المربعات كما طلبت (ارتفاع موحد ومساحات كافية) */
        input[type="text"], select {
            height: 50px; padding: 10px 15px; font-size: 18px; font-weight: bold; border: 2px solid #dcdde1; border-radius: 8px; transition: 0.3s;
        }
        input:focus, select:focus { border-color: #3498db; outline: none; box-shadow: 0 0 8px rgba(52, 152, 219, 0.2); }
        
        /* زر الحفظ العريض والملون */
        .btn-save {
            background-color: #27ae60; color: white; border: none; padding: 15px 40px; font-size: 22px; font-weight: bold; border-radius: 10px; cursor: pointer; transition: 0.3s; margin-top: 10px; width: 200px;
        }
        .btn-save:hover { background-color: #219150; transform: translateY(-2px); }
        .btn-save:disabled { background-color: #95a5a6; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-plus-circle"></i> إضافة صنف جديد</h2>
    
    <form id="productForm" action="save_product.php" method="POST">
        <div class="form-group">
            <label>كود الصنف:</label>
            <input type="text" name="product_code" placeholder="أدخل كود الصنف هنا..." required>
        </div>

        <div class="form-group">
            <label>اسم الصنف:</label>
            <select name="product_name" required>
                <option value="">-- اختر نوع الصنف --</option>
                <option value="كرتونة أحذية لوفو 2 دسته نسائي">كرتونة أحذية لوفو 2 دسته نسائي</option>
                <option value="كرتونة أحذية لوفو 4 دسته نسائي">كرتونة أحذية لوفو 4 دسته نسائي</option>
                <option value="كرتونة أحذية لوفو 2 دسته رجالي">كرتونة أحذية لوفو 2 دسته رجالي</option>
                <option value="كرتونة أحذية لوفو 4 دسته رجالي">كرتونة أحذية لوفو 4 دسته رجالي</option>
                <option value="كرتونة أحذية لوفو 2 دسته اطفالي">كرتونة أحذية لوفو 2 دسته اطفالي</option>
                <option value="كرتونة أحذية لوفو 4 دسته اطفالي">كرتونة أحذية لوفو 4 دسته اطفالي</option>
                <option value="كرتونة أحذية لوفو 2 دسته صبياني">كرتونة أحذية لوفو 2 دسته صبياني</option>
                <option value="كرتونة أحذية لوفو 4 دسته صبياني">كرتونة أحذية لوفو 4 دسته صبياني</option>
            </select>
        </div>

        <button type="submit" id="saveBtn" class="btn-save" onclick="this.disabled=true; this.value='جاري الحفظ...'; this.form.submit();">
            حفظ الصنف
        </button>
    </form>
</div>

</body>
</html>
