<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المخزن الرئيسي - شركة أبوحريرة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root { --side-bg: #263544; --accent: #f39c12; --main-bg: #f5f6f8; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--main-bg); display: flex; height: 100vh; overflow: hidden; }
        
        /* القائمة الجانبية (نفس التصميم السابق) */
        .sidebar { width: 70px; background: var(--side-bg); transition: 0.3s; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar:hover { width: 250px; }
        .sidebar a { display: flex; align-items: center; padding: 15px 25px; color: #bdc3c7; text-decoration: none; white-space: nowrap; font-weight: bold; }
        .sidebar a i { min-width: 35px; font-size: 20px; }
        .sidebar .nav-text { opacity: 0; transition: 0.2s; margin-right: 10px; }
        .sidebar:hover .nav-text { opacity: 1; }

        .content-area { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-bar { background: #fff; height: 60px; display: flex; align-items: center; padding: 0 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); justify-content: space-between; }
        
        /* التبويبات العلوية */
        .tabs-container { padding: 20px 40px 0; display: flex; gap: 10px; background: #fff; border-bottom: 2px solid #ddd; }
        .tab-btn { padding: 12px 25px; border: none; background: #eee; cursor: pointer; font-weight: bold; font-size: 16px; border-radius: 8px 8px 0 0; transition: 0.3s; }
        .tab-btn.active { background: var(--side-bg); color: #fff; }

        .workspace { padding: 30px; }
        .form-card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: none; }
        .form-card.active { display: block; }

        /* تنسيق الحقول المحاكي للصورة */
        .row-inputs { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; align-items: flex-end; }
        .field-group { display: flex; flex-direction: column; gap: 8px; }
        label { font-weight: 900; color: #333; font-size: 16px; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-weight: bold; font-size: 16px; }
        .small-input { width: 100px; }
        .med-input { width: 250px; }

        /* الأزرار */
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: #fff; display: flex; align-items: center; gap: 8px; }
        .btn-save { background: #008cba; } /* لون زر حفظ في الصورة */
        .btn-add { background: #28a745; }  /* زر إضافة صنف أخضر */
        .btn-delete { background: #dc3545; } /* زر حذف أحمر */

        /* تخصيص Select2 */
        .select2-container--default .select2-selection--single { height: 42px; padding: 5px; font-weight: bold; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div style="padding: 20px; text-align: center; color: #fff;"><i class="fas fa-boxes fa-2x"></i></div>
        <a href="dashboard.php"><i class="fas fa-home"></i> <span class="nav-text">الرئيسية</span></a>
        <a href="main_store.php" style="color:#fff; background:#2c3e50;"><i class="fas fa-warehouse"></i> <span class="nav-text">إدارة المخازن</span></a>
        <a href="customers.php"><i class="fas fa-users"></i> <span class="nav-text">العملاء</span></a>
        <a href="reports.php"><i class="fas fa-file-contract"></i> <span class="nav-text">التقارير</span></a>
        <a href="logout.php" style="margin-top:auto; color:#e74c3c;"><i class="fas fa-sign-out-alt"></i> <span class="nav-text">خروج</span></a>
    </div>

    <div class="content-area">
        <div class="top-bar">
            <h2 style="margin:0; font-weight:900;">إدارة المخزن الرئيسي</h2>
            <div style="font-weight:bold;"><?php echo date('Y/m/d'); ?></div>
        </div>

        <div class="tabs-container">
            <button class="tab-btn active" onclick="openTab(event, 'add-item')">إضافة صنف</button>
            <button class="tab-btn" onclick="openTab(event, 'add-stock')">إضافة بضاعة</button>
            <button class="tab-btn" onclick="openTab(event, 'transfer-stock')">تحويل بضاعة</button>
        </div>

        <div class="workspace">
            <div id="add-item" class="form-card active">
                <h3 style="border-bottom: 2px solid var(--side-bg); padding-bottom: 10px;">تسجيل صنف جديد</h3>
                <form id="form-new-item">
                    <div class="row-inputs">
                        <div class="field-group">
                            <label>كود الصنف</label>
                            <input type="text" name="item_code" class="med-input" placeholder="ادخل كود الصنف" required>
                        </div>
                        <div class="field-group">
                            <label>اسم الصنف</label>
                            <select name="item_name" class="med-input searchable-select" style="width: 350px;">
                                <option value="">--- اختر نوع الصنف ---</option>
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
                    </div>
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> حفظ الصنف</button>
                </form>
            </div>

            <div id="add-stock" class="form-card">
                <h3 style="border-bottom: 2px solid var(--side-bg); padding-bottom: 10px;">إضافة بضاعة للمخزن</h3>
                <div class="row-inputs">
                    <div class="field-group">
                        <label>التاريخ</label>
                        <input type="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="field-group">
                        <label>رقم الحاوية</label>
                        <input type="text" placeholder="رقم الحاوية">
                    </div>
                </div>
                
                <div id="stock-items-container">
                    <div class="row-inputs item-row">
                        <div class="field-group">
                            <label>الكمية</label>
                            <input type="number" class="small-input" placeholder="0">
                        </div>
                        <div class="field-group">
                            <label>الصنف (بحث بالكود أو الاسم)</label>
                            <select class="searchable-select" style="width: 400px;">
                                <option value="">اختر الصنف...</option>
                                </select>
                        </div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button class="btn btn-add" onclick="addRow('stock-items-container')"><i class="fas fa-plus"></i> إضافة صنف</button>
                    <button class="btn btn-delete" onclick="removeLastRow('stock-items-container')"><i class="fas fa-trash"></i> حذف صنف</button>
                    <button class="btn btn-save"><i class="fas fa-check-circle"></i> حفظ البضاعة</button>
                </div>
            </div>

            <div id="transfer-stock" class="form-card">
                <h3 style="border-bottom: 2px solid var(--side-bg); padding-bottom: 10px;">تحويل بضاعة للمخزن الفرعي</h3>
                <div class="row-inputs">
                    <div class="field-group">
                        <label>التاريخ</label>
                        <input type="date" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>
                    <div class="field-group">
                        <label>من</label>
                        <select disabled><option>المخزن الرئيسي</option></select>
                    </div>
                    <div class="field-group">
                        <label>إلى (الجهة المحول لها)</label>
                        <select class="med-input">
                            <option>المخزن الفرعي</option>
                            <option>المعرض</option>
                        </select>
                    </div>
                </div>

                <div id="transfer-items-container">
                    <div class="row-inputs item-row">
                        <div class="field-group">
                            <label>الكمية</label>
                            <input type="number" class="small-input" placeholder="0">
                        </div>
                        <div class="field-group">
                            <label>الصنف (كود - اسم - متوفر)</label>
                            <select class="searchable-select" style="width: 450px;">
                                <option value="">ابحث عن صنف...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button class="btn btn-add" onclick="addRow('transfer-items-container')"><i class="fas fa-plus"></i> إضافة صنف</button>
                    <button class="btn btn-delete" onclick="removeLastRow('transfer-items-container')"><i class="fas fa-trash"></i> حذف صنف</button>
                    <button class="btn btn-save"><i class="fas fa-truck"></i> حفظ التحويل</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // تفعيل البحث المتقدم في القوائم
            initSelect2();
        });

        function initSelect2() {
            $('.searchable-select').select2({
                dir: "rtl",
                language: "ar",
                placeholder: "اختر أو ابحث...",
                allowClear: true
            });
        }

        function openTab(evt, tabName) {
            $(".form-card").removeClass("active");
            $(".tab-btn").removeClass("active");
            $("#" + tabName).addClass("active");
            $(evt.currentTarget).addClass("active");
        }

        function addRow(containerId) {
            let container = $("#" + containerId);
            let newRow = container.find(".item-row:first").clone();
            newRow.find("input").val(""); // مسح القيم في السطر الجديد
            // تدمير Select2 القديم وإعادة بنائه للسطر الجديد ليعمل البحث
            newRow.find(".select2-container").remove();
            newRow.find("select").removeClass("select2-hidden-accessible").removeAttr("data-select2-id");
            container.append(newRow);
            initSelect2();
        }

        function removeLastRow(containerId) {
            let rows = $("#" + containerId + " .item-row");
            if (rows.length > 1) {
                rows.last().remove();
            }
        }
    </script>
</body>
</html>