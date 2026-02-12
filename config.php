<?php
$host     = 'mysql.railway.internal';
$user     = 'root';
$password = 'ptUdBSoIyfsPheQnkPCOAOUotEgpvWMg';
$dbname   = 'railway';
$port     = '3306';

$conn = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("فشل الاتصال بالقاعدة: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
