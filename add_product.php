<?php
include 'dashboard.php'; // لاستدعاء الشريط الجانبي والتنسيق
?>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card p-5">
                    <h2 class="text-center mb-5" style="color: #2c3e50; border-bottom: 3px solid #f1c40f; padding-bottom: 10px;">
                        إضافة صنف جديد - شركة أبو حريرة
                    </h2>

                    <form action="save_product.php" method="POST" id="productForm">
                        
                        <div class="form-group mb-4">
                            <label class="mb-2">كود الصنف:</label>
                            <input type="text" name="product_code" class="form-control" placeholder="أدخل كود الصنف هنا..." required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="mb-2">اسم الصنف (أحذية لوفو):</label>
                            <select name="product_name" class="form-control select2-products" required>
                                <option value="">--- اختر نوع الكرتونة ---</option>
                                <option>كرتونة أحذية لوفو 2 دسته نسائي</option>
                                <option>كرتونة أحذية لوفو 4 دسته نسائي</option>
                                <option>كرتونة أحذية لوفو 2 دسته رجالي</option>
                                <option>كرتونة أحذية لوفو 4 دسته رجالي</option>
                                <option>كرتونة أحذية لوفو 2 دسته اطفالي</option>
                                <option>كرتونة أحذية لوفو 4 دسته اطفالي</option>
                                <option>كرتونة أحذية لوفو 2 دسته صبياني</option>
                                <option>كرتونة أحذية لوفو 4 دسته صبياني</option>
                            </select>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" id="saveBtn" class="btn btn-warning btn-lg w-50" style="font-weight: 900; height: 60px; font-size: 22px;">
                                <i class="fa fa-save ms-2"></i> حفظ الصنف
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// منع تكرار الحفظ عند الضغط المزدوج أو بطء الشبكة
document.getElementById('productForm').onsubmit = function() {
    var btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = "جاري الحفظ والتحميل...";
};
</script>