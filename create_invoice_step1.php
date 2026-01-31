<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config.php'; // ثم يأتي كود الاتصال وقاعدة البيانات بعد ذلك
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>استخراج فاتورة - الخطوة 1</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .step-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 450px; text-align: center; border-top: 10px solid #2980b9; }
        h2 { color: #2c3e50; margin-bottom: 30px; }
        select { width: 100%; height: 50px; font-size: 18px; font-weight: bold; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 25px; padding: 0 10px; }
        .btn-continue { width: 100%; padding: 15px; background-color: #2980b9; color: white; border: none; border-radius: 8px; font-size: 20px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="step-card">
    <h2>اختيار المخزن</h2>
    <form action="create_invoice_step2.php" method="POST">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        <select name="store_id" required>
            <option value="">-- اختر المخزن المسحوب منه --</option>
            <?php
            $stores = mysqli_query($conn, "SELECT * FROM stores");
            while($s = mysqli_fetch_assoc($stores)) {
                echo "<option value='".$s['id']."'>".$s['store_name']."</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn-continue">استمر</button>
    </form>
</div>
</body>
</html>
