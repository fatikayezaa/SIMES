<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "simes_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>