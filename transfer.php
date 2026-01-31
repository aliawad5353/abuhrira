<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحويل بضاعة بين المخازن - شركة أبو حريرة</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .transfer-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 1100px; margin: auto; }
        h2 { border-bottom: 3px solid #2c3e50; padding-bottom: 10px; font-weight: bold; margin-bottom: 25px; }
        .flex-row { display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 8px; color: #2c3e50; }
        input, select { height: 45px; border: 2px solid #ddd; border-radius: 6px; padding: 5px 10px; font-weight: bold; font-size: 16px; }
        .date-input { width: 75px; text-align: center; }
        .col-qty { width: 120px; }
        .col-product { flex-grow: 2; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-add { background-color: #3498db; color: white; }
        .btn-del { background-color: #e74c3c; color: white; }
        .btn-save { background-color: #27ae60; color: white; width: 100%; font-size: 20px; margin-top: 20px; }
        .store-label { background: #ecf0f1; padding: 10px; border-radius: 5px; border: 2px solid #bdc3c7; font-weight: bold; min-width: 150px; text-align: center; }
    </style>
</head>
<body>

<div class="transfer-card">
    <?php if (!isset($_POST['from_store_id'])): ?>
        <h2>تحويل بضاعة بين المخازن</h2>
        <form method="POST">
            <div class="flex-row">
                <div class="form-group">
                    <label>اختر المخزن (المحول منه):</label>
                    <select name="from_store_id" required style="width: 300px;">
                        <option value="">-- اختر المخزن --</option>
                        <?php
                        $stores = mysqli_query($conn, "SELECT * FROM stores");
                        while($row = mysqli_fetch_assoc($stores)) {
                            echo "<option value='".$row['id']."'>".$row['store_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-add">استمر</button>
            </div>
        </form>

    <?php else: 
        $from_id = $_POST['from_store_id'];
        $store_res = mysqli_query($conn, "SELECT store_name FROM stores WHERE id = $from_id");
        $store_data = mysqli_fetch_assoc($store_res);
    ?>
        <h2>تحويل بضاعة بين المخازن</h2>
        
        <form action="save_transfer.php" method="POST">
            <input type="hidden" name="from_store_id" value="<?php echo $from_id; ?>">
            
            <div class="flex-row">
                <div class="form-group">
                    <label>تغيير المخزن:</label>
                    <select onchange="window.location.href='transfer.php'" class="date-input" style="width: 150px;">
                        <option>اختر مخزن</option>
                        <option value="1">الرئيسي</option>
                        <option value="2">الفرعي</option>
                        <option value="3">المعرض</option>
                    </select>
                </div>
                <button type="button" onclick="location.reload();" class="btn btn-add">استمر</button>
            </div>

            <div class="flex-row">
                <div class="form-group">
                    <label>التاريخ:</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="number" name="day" class="date-input" value="<?php echo date('d'); ?>">
                        <input type="number" name="month" class="date-input" value="<?php echo date('m'); ?>">
                        <input type="number" name="year" class="date-input" value="<?php echo date('Y'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>من:</label>
                    <div class="store-label"><?php echo $store_data['store_name']; ?></div>
                </div>

                <div class="form-group">
                    <label>إلى:</label>
                    <select name="to_store_id" required style="width: 250px;">
                        <option value="">اختر المخزن المحول له...</option>
                        <?php
                        $to_stores = mysqli_query($conn, "SELECT * FROM stores WHERE id != $from_id");
                        while($ts = mysqli_fetch_assoc($to_stores)) {
                            echo "<option value='".$ts['id']."'>".$ts['store_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <hr>

            <div id="transfer-items">
                <div class="flex-row item-row">
                    <div class="form-group col-qty">
                        <label>الكمية:</label>
                        <input type="number" name="qty[]" required min="1" placeholder="0">
                    </div>
                    <div class="form-group col-product">
                        <label>الصنف (مع المتوفر):</label>
                        <select name="product_id[]" class="select2-ajax" required>
                            <option value="">ابحث باسم الصنف أو الكود...</option>
                            <?php
                            // هنا نظهر فقط الأصناف المتوفرة في هذا المخزن
                            $prods = mysqli_query($conn, "SELECT p.*, sb.quantity FROM products p 
                                     JOIN stock_balances sb ON p.id = sb.product_id 
                                     WHERE sb.store_id = $from_id AND sb.quantity > 0");
                            while($p = mysqli_fetch_assoc($prods)) {
                                echo "<option value='".$p['id']."'>".$p['product_code']." - ".$p['product_name']." (متوفر: ".$p['quantity'].")</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex-row">
                <button type="button" class="btn btn-add" onclick="addRow()">+ إضافة صنف</button>
                <button type="button" class="btn btn-del" onclick="removeRow()">- حذف صنف</button>
            </div>

            <button type="submit" class="btn btn-save" onclick="this.disabled=true; this.form.submit();">حفظ التحويل وتحديث المخازن</button>
        </form>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('.select2-ajax').select2({ dir: "rtl" });
    });

    function addRow() {
        let row = $('.item-row:first').clone();
        row.find('input').val('');
        row.find('.select2-container').remove();
        $('#transfer-items').append(row);
        $('.select2-ajax').select2({ dir: "rtl" });
    }

    function removeRow() {
        if ($('.item-row').length > 1) { $('.item-row:last').remove(); }
    }
</script>

</body>
</html>
