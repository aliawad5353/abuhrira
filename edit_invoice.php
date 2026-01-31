<?php 
include 'config.php'; 
$inv_id = $_GET['id'];

// جلب بيانات الفاتورة والعميل والمخزن (نحتاج المخزن لإرجاع البضاعة إليه)
$inv_query = $conn->query("SELECT i.*, c.name as customer_name FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.id = $inv_id");
$invoice = $inv_query->fetch_assoc();

// جلب الأصناف الحالية في الفاتورة
$details = $conn->query("SELECT d.*, p.product_name, p.product_code FROM invoice_items d JOIN products p ON d.product_id = p.id WHERE d.invoice_id = $inv_id");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل فاتورة رقم <?php echo $inv_id; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f7f6; padding: 20px; }
        .edit-card { background: #fff; padding: 30px; border-radius: 15px; border-top: 10px solid #d32f2f; max-width: 1000px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .row { display: flex; gap: 15px; margin-bottom: 15px; padding: 15px; background: #fafafa; border: 1px solid #eee; border-radius: 8px; align-items: flex-end; }
        label { font-weight: bold; display: block; margin-bottom: 8px; font-size: 16px; }
        input, select { height: 45px; padding: 5px 10px; border: 2px solid #ddd; border-radius: 5px; font-weight: bold; font-size: 16px; }
        .btn-save { background: #27ae60; color: white; padding: 18px; width: 100%; border: none; font-size: 22px; cursor: pointer; border-radius: 10px; font-weight: bold; margin-top: 20px; }
        .btn-add { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 20px; }
        .btn-del { background: #e74c3c; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

<div class="edit-card">
    <h2 style="color: #2c3e50;">تعديل الفاتورة رقم: <?php echo $inv_id; ?></h2>
    <div style="background: #eef2f3; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        <span>العميل: <?php echo $invoice['customer_name']; ?></span> | 
        <span>المخزن: (سيتم تحديث كميات هذا المخزن تلقائياً)</span>
    </div>

    <form action="update_invoice_action.php" method="POST">
        <input type="hidden" name="invoice_id" value="<?php echo $inv_id; ?>">
        <input type="hidden" name="store_id" value="<?php echo $invoice['store_id']; ?>">
        
        <div id="items_list">
            <?php while($item = $details->fetch_assoc()): ?>
            <div class="row item-row">
                <div style="width: 120px;">
                    <label>الكمية</label>
                    <input type="number" name="qty[]" value="<?php echo $item['quantity']; ?>" style="width: 100%;" required>
                </div>
                <div style="flex-grow: 1;">
                    <label>الصنف</label>
                    <select name="product_id[]" class="search-select" style="width: 100%;">
                        <?php
                        $all_items = $conn->query("SELECT * FROM products");
                        while($opt = $all_items->fetch_assoc()) {
                            $selected = ($opt['id'] == $item['product_id']) ? 'selected' : '';
                            echo "<option value='{$opt['id']}' $selected>{$opt['product_code']} - {$opt['product_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="button" class="btn-del" onclick="$(this).parent().remove()"><i class="fas fa-trash"></i> حذف</button>
            </div>
            <?php endwhile; ?>
        </div>

        <button type="button" class="btn-add" onclick="addNewRow()">➕ إضافة صنف جديد للفاتورة</button>
        
        <button type="submit" class="btn-save" onclick="this.disabled=true; this.innerText='جاري معالجة وتحديث المخزن...'; this.form.submit();">
            حفظ التعديلات وتحديث الكميات في المخزن
        </button>
    </form>
</div>

<script>
    $(document).ready(function() { $('.search-select').select2({ dir: "rtl" }); });

    function addNewRow() {
        var row = $('.item-row:first').clone();
        row.find('input').val('');
        row.find('span.select2').remove();
        row.find('select').removeAttr('data-select2-id').select2({ dir: "rtl" });
        $('#items_list').append(row);
    }
</script>
</body>
</html>
