<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $p_code = mysqli_real_escape_string($conn, $_POST['product_code']);
    $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);

    // التحقق من عدم تكرار كود الصنف
    $check = mysqli_query($conn, "SELECT id FROM products WHERE product_code = '$p_code'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('خطأ: كود الصنف موجود مسبقاً!'); window.history.back();</script>";
    } else {
        $query = "INSERT INTO products (product_code, product_name) VALUES ('$p_code', '$p_name')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('تم حفظ الصنف بنجاح'); window.location.href='add_product.php';</script>";
        } else {
            echo "خطأ في الحفظ: " . mysqli_error($conn);
        }
    }
}
?>
