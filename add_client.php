<?php
include 'dashboard.php';
?>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card p-5 shadow-lg" style="border-radius: 25px; border: 2px solid #2c3e50; margin-top: 30px;">
                    <h2 class="text-center mb-5" style="font-weight: 900; color: #2c3e50;">
                        <i class="fa fa-user-plus ms-2"></i> تسجيل عميل جديد
                    </h2>

                    <form action="save_client.php" method="POST" id="clientForm">
                        
                        <div class="form-group mb-5">
                            <label class="mb-3 fs-4">اسم العميل بالكامل:</label>
                            <input type="text" name="name" class="form-control fw-bold" placeholder="أدخل اسم العميل هنا..." required style="height: 60px; font-size: 20px;">
                        </div>

                        <div class="form-group mb-5">
                            <label class="mb-3 fs-4">رقم الهاتف:</label>
                            <input type="text" name="phone" class="form-control fw-bold" placeholder="00249..." required style="height: 60px; font-size: 20px;">
                        </div>

                        <div class="form-group mb-5">
                            <label class="mb-3 fs-4">السكن / العنوان:</label>
                            <input type="text" name="address" class="form-control fw-bold" placeholder="المدينة - الحي - الشارع" required style="height: 60px; font-size: 20px;">
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" id="saveBtn" class="btn btn-warning btn-lg w-100" style="font-weight: 900; height: 70px; font-size: 24px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                                <i class="fa fa-save ms-2"></i> حفظ بيانات العميل
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// منع تكرار الحفظ عند بطء الشبكة
document.getElementById('clientForm').onsubmit = function() {
    var btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري الحفظ...';
};
</script>