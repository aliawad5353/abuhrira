<?php 
include 'config.php'; 
// الترقيم
$limit = 10; $page = isset($_GET['page']) ? $_GET['page'] : 1; $start = ($page - 1) * $limit;

$sql = "SELECT d.*, c.name FROM discounts_log d JOIN clients c ON d.client_id = c.id ORDER BY d.id DESC";
$result = mysqli_query($conn, $sql . " LIMIT $start, $limit");

$pages = ceil(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM discounts_log")) / $limit);
?>

<div class="debt-container">
    <h2>سجل الخصومات والسداد</h2>
    <div class="search-area">
        <input type="text" id="discSearch" placeholder="ابحث عن تفاصيل خصومات عميل معين..." onkeyup="filterDiscs()">
    </div>
    <table id="discTable">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>اسم العميل</th>
                <th>مبلغ الخصم</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['discount_date']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td style="color:#27ae60;">- <?php echo number_format($row['discount_amount'], 2); ?> ج.س</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php for($i=1; $i<=$pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>
