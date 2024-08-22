<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "presensigps";

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);


if(!$connection) 
{
    echo "Koneksi ke database gagal" . mysqli_connect_error();
}

//timezone
date_default_timezone_set('Asia/Jakarta');

function base_url($url = null)
{
    $base_url = 'http://localhost/e-presensi';
    if ($url != null) {
        return $base_url . '/'. $url;
    } else {
        return $base_url;
    }
}
?>
