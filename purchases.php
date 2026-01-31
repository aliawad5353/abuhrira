<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>شراء بضاعة - شركة أبو حريرة</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .purchase-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 1100px; margin: auto; }
        h2 { border-bottom: 3px solid #2c3e50; padding-bottom: 10px; font-weight: bold; }
        
        /* تنسيق التاريخ المجزأ والمربعات */
        .flex-row { display: flex; gap: 20px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 8px; font-size: 16px; }
        
        input, select { height: 45px; border: 2px solid #ddd; border-radius: 6px; padding: 5px 10px; font-weight: bold; font-size: 16px; }
        
        /* التاريخ: 3 مربعات صغيرة */
        .date-input { width: 80px; text-align: center; }
        
        /* الصنف طويل والكمية صغيرة */
        .col-qty { width: 100px; }
        .col-product { flex-grow: 2; min-width: 300px; }
        .col-price { width: 150px; }

        /* الأزرار الملونة والكبيرة */
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-add { background-color: #3498db; color: white; }
        .btn-del { background-color: #e74c3c; color: white; }
        .btn-save { background-color: #27ae60; color: white; width: 100%; font-size: 20px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="purchase-card">
    <h2><i class="fas fa-shopping-cart"></i> شراء بضاعة</h2>
    
    <form action="save_purchase.php" method="POST">
        <div class="flex-row">
            <div class="form-group">
                <label>التاريخ (يوم/شهر/سنة):</label>
                <div style="display: flex; gap: 5px;">
                    <input type="number" name="day" class="date-input" value="<?php echo date('d'); ?>" min="1" max="31">
                    <input type="number" name="month" class="date-input" value="<?php echo date('m'); ?>" min="1" max="12">
                    <input type="number" name="year" class="date-input" value="<?php echo date('Y'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>المورد:</label>
                <select name="supplier" style="width: 250px;">
                    <option>شركة ابوحريره للاحذيه</option>
                </select>
            </div>

            <div class="form-group">
                <label>اختر مخزن:</label>
                <select name="store_id" required style="width: 200px;">
                    <option value="">-- اختر المخزن --</option>
                    <?php
                    $stores = mysqli_query($conn, "SELECT * FROM stores");
                    while($row = mysqli_fetch_assoc($stores)) {
                        echo "<option value='".$row['id']."'>".$row['store_name']."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <hr>

        <div id="items-container">
            <div class="flex-row item-row">
                <div class="form-group col-qty">
                    <label>الكمية:</label>
                    <input type="number" name="qty[]" required min="1">
                </div>
                
                <div class="form-group col-product">
                    <label>الصنف (بحث بالكود أو الاسم):</label>
                    <select name="product_id[]" class="select2-ajax" required>
                        <option value="">ابحث عن صنف...</option>
                        <?php
                        $prods = mysqli_query($conn, "SELECT * FROM products");
                        while($p = mysqli_fetch_assoc($prods)) {
                            echo "<option value='".$p['id']."'>".$p['product_code']." - ".$p['product_name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-price">
                    <label>السعر:</label>
                    <input type="number" name="price[]" step="0.01" required>
                </div>
            </div>
        </div>

        <div class="flex-row">
            <button type="button" class="btn btn-add" onclick="addRow()">+ إضافة صنف</button>
            <button type="button" class="btn btn-del" onclick="removeRow()">- حذف صنف</button>
        </div>

        <button type="submit" class="btn btn-save" onclick="this.disabled=true; this.form.submit();">حفظ المشتريات وإدراجها في المخزن</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.select2-ajax').select2({ dir: "rtl" });
    });

    function addRow() {
        let row = $('.item-row:first').clone();
        row.find('input').val(''); // تفريغ الحقول
        row.find('.select2-container').remove(); // إزالة تنسيق سلكت القديم ليتم بناؤه من جديد
        $('#items-container').append(row);
        $('.select2-ajax').select2({ dir: "rtl" }); // إعادة تفعيل البحث الذكي
    }

    function removeRow() {
        if ($('.item-row').length > 1) {
            $('.item-row:last').remove();
        }
    }
</script>

</body>
</html>
