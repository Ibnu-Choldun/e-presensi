<?php

session_start();
require_once ('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM karyawan WHERE id=$id");

$_SESSION['success'] = 'Data berhasil dihapus';
header("Location: karyawan.php");
exit();

include('../layouts/footer.php');

?>