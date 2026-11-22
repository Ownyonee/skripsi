<?php
include "koneksi.php";

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM makanan WHERE id='$id'");

header("Location: makanan.php");
?>