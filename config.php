<?php
$host     = "mysql.railway.internal";
$user     = "root";
$password = "DhFlqmPwLsQTNJpjadlexdmsfTyCfMxu";
$dbname   = "railway";
$port     = "3306";

$conn = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Khartoum');
