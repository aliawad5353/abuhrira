<?php 
include 'config.php';
$client_id = $_POST['client_id'];
$store_id = $_POST['store_id'];
$client_res = mysqli_query($conn, "SELECT name FROM clients WHERE id = $client_id");
$client_data = mysqli_fetch_assoc($client_res);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعبئة الفاتورة - المرحلة 2</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .invoice-box { background: white; padding: 30px; border-radius: 12px; max-width: 1100px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .header-info { display: flex; justify-content: space-between; background: #ebf2f7; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .flex-row { display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-end; }
        input, select { height: 45px; border: 2px solid #ddd; border-radius: 6px; padding: 0 10px; font-weight: bold; }
        .col-qty { width: 100px; } .col-product { flex-grow: 2; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-add { background: #3498db; color: white; } .btn-save { background: #27ae60; color: white; width: 100%; font-size: 20px; }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header-info">
        <span>تاريخ اليوم: <?php echo date('Y-m-d'); ?></span>
        <span>العميل: <?php echo $client_data['name']; ?></span>
        <div style="display:flex; align-items:center; gap:10px;">
            <span>تغيير المخزن:</span>
            <select onchange="window.location.href='create_invoice_step1.php?client_id=<?php echo $client_id; ?>'">
                <option>اختر مخزن</option>
                <option>الرئيسي</option><option>الفرعي</option><option>المعرض</option>
            </select>
        </div>
    </div>

    <form action="create_invoice_step3.php" method="POST">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        <input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
        
        <div id="items-area">
            <div class="flex-row item-row">
                <div style="display:flex; flex-direction:column;">
                    <label>الكمية</label>
                    <input type="number" name="qty[]" class="col-qty" required min="1">
                </div>
                <div style="display:flex; flex-direction:column; flex-grow:1;">
                    <label>الصنف (الكود - الاسم - السعر - المتوفر)</label>
                    <select name="product_id[]" class="select2-ajax" required>
                        <option value="">ابحث هنا...</option>
                        <?php
                        // جلب المتوفر والسعر من المخزن المختار
                        $prods = mysqli_query($conn, "SELECT p.*, sb.quantity, sb.price FROM products p 
                                 INNER JOIN stock_balances sb ON p.id = sb.product_id 
                                 WHERE sb.store_id = $store_id AND sb.quantity > 0");
                        while($p = mysqli_fetch_assoc($prods)) {
                            echo "<option value='".$p['id']."'>".$p['product_code']." - ".$p['product_name']." | متوفر: ".$p['quantity']." | السعر: ".$p['price']."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-add" onclick="addRow()">+ اضف صنف</button>
        <button type="submit" class="btn btn-save" style="margin-top:20px;">حفظ ومراجعة الفاتورة</button>
    </form>
</div>

<script>
    $(document).ready(function() { $('.select2-ajax').select2({ dir: "rtl" }); });
    function addRow() {
        let row = $('.item-row:first').clone();
        row.find('input').val('');
        row.find('.select2-container').remove();
        $('#items-area').append(row);
        $('.select2-ajax').select2({ dir: "rtl" });
    }
</script>
</body>
</html>
