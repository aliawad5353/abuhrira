<?php
include 'dashboard.php'; // لاستدعاء الهيدر والشريط الجانبي
require_once 'config.php';

// جلب المخازن للقائمة المنسدلة
$warehouses = mysqli_query($conn, "SELECT * FROM warehouses");
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card p-5 shadow-lg" style="border-radius: 20px; border: 2px solid #2c3e50;">
            <h2 class="text-center mb-5" style="font-weight: 900; color: #2c3e50; text-decoration: underline;">
                شراء بضاعة جديدة - LUOFU
            </h2>

            <form action="save_purchase.php" method="POST" id="purchaseForm">
                
                <div class="row mb-5 align-items-end">
                    <div class="col-md-4">
                        <label class="d-block mb-2 fs-5">التاريخ:</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="day" class="form-control text-center fw-bold" value="<?php echo date('d'); ?>" min="1" max="31">
                            <input type="number" name="month" class="form-control text-center fw-bold" value="<?php echo date('m'); ?>" min="1" max="12">
                            <input type="number" name="year" class="form-control text-center fw-bold" value="2025" min="2020" max="2040">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="mb-2 fs-5">المورد:</label>
                        <select name="supplier" class="form-control fw-bold bg-light">
                            <option>شركة ابوحريره للاحذيه</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="mb-2 fs-5">اختر المخزن:</label>
                        <select name="warehouse_id" class="form-control fw-bold border-primary" required>
                            <option value="">--- اختر المخزن ---</option>
                            <?php while($row = mysqli_fetch_assoc($warehouses)): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <hr style="border: 2px solid #eee;" class="mb-5">

                <div id="items_container">
                    <div class="row mb-4 item-row align-items-center bg-white p-3 rounded shadow-sm border">
                        <div class="col-md-2">
                            <label class="small fw-bold">الكمية</label>
                            <input type="number" name="qty[]" class="form-control fw-bold text-danger" placeholder="0" required>
                        </div>
                        <div class="col-md-7">
                            <label class="small fw-bold">الصنف ()</label>
                            <select name="product_id[]" class="form-control select2-products" required>
                                <option value="">ااختر الصنف...</option>
                                <?php 
                                $products = mysqli_query($conn, "SELECT * FROM products");
                                while($p = mysqli_fetch_assoc($products)): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['product_code'] . " - " . $p['product_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">السعر</label>
                            <input type="number" step="0.01" name="price[]" class="form-control fw-bold text-success" placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4">
                        <button type="button" onclick="addItem()" class="btn btn-primary btn-lg w-100 fw-bold shadow">
                            <i class="fa fa-plus-circle"></i> إضافة صنف آخر
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" onclick="removeItem()" class="btn btn-danger btn-lg w-100 fw-bold shadow">
                            <i class="fa fa-trash"></i> حذف آخر صنف
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="saveBtn" class="btn btn-success btn-lg w-100 fw-bold shadow">
                            <i class="fa fa-save"></i> حفظ المشتريات
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    initSelect2();
});

function initSelect2() {
    $('.select2-products').select2({
        dir: "rtl",
        width: '100%'
    });
}

function addItem() {
    let container = document.getElementById('items_container');
    let firstRow = document.querySelector('.item-row');
    let newRow = firstRow.cloneNode(true);
    
    // مسح القيم في الصف الجديد
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    
    // مسح Select2 القديم وإعادة بنائه
    newRow.querySelector('.select2-container').remove();
    
    container.appendChild(newRow);
    initSelect2();
}

function removeItem() {
    let rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        rows[rows.length - 1].remove();
    }
}

// منع تكرار الحفظ
document.getElementById('purchaseForm').onsubmit = function() {
    let btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري حفظ البيانات...';
};
</script>

<style>
    /* تضخيم الخطوط والمربعات بناءً على طلبك */
    .form-control { border-radius: 10px !important; padding: 12px !important; border: 2px solid #ced4da !important; }
    label { font-weight: 800 !important; font-size: 1.1rem !important; }
    .select2-selection { height: 50px !important; border: 2px solid #ced4da !important; border-radius: 10px !important; }
    .select2-selection__rendered { line-height: 50px !important; font-weight: bold !important; font-size: 18px !important; }
</style>