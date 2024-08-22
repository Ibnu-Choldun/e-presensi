<?php
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if($_SESSION['peran'] != 'Karyawan') {
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

include_once("../../config.php");

$file_foto = $_POST['foto'];
$id_karyawan = $_POST['id'];
$tanggal_masuk = $_POST['tanggal_masuk'];
$jam_masuk = $_POST['jam_masuk'];
$lokasi_masuk = $_POST['lokasi_masuk'];

// Tentukan batas waktu terlambat
$batas_waktu_terlambat = "08:00:00";

// Periksa apakah waktu masuk melewati batas waktu terlambat
$status_kehadiran = (strtotime($jam_masuk) > strtotime($batas_waktu_terlambat)) ? 'late' : 'on_time';

$photo = str_replace('data:image/jpeg;base64,', '', $file_foto);
$photo = str_replace(' ', '+', $photo);
$data = base64_decode($photo);
// Ensure the 'foto/masuk/' directory exists
$folder_path = '../presensi/foto/masuk/';

// Create a unique file name to avoid overwriting existing files
$nama_file = 'masuk_'. $id_karyawan . '_' . date('Y-m-d') . '.png';
file_put_contents($folder_path. $nama_file, $data);;

$result = mysqli_query($connection, "INSERT INTO presensi(id_karyawan, tanggal_masuk, jam_masuk, foto_masuk, lokasi_masuk) VALUES ('$id_karyawan', '$tanggal_masuk', '$jam_masuk', '$nama_file', '$lokasi_masuk')");

if($result) {
    $_SESSION['success'] = "Presensi berhasil";
} else {
    $_SESSION['gagal'] = "Presensi gagal";
}
?>
