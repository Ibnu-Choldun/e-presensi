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
$tanggal_pulang = $_POST['tanggal_pulang'];
$jam_pulang = $_POST['jam_pulang'];
$lokasi_pulang = $_POST['lokasi_pulang'];

// Debugging: Echo cdata
var_dump($id_karyawan, $tanggal_pulang, $lokasi_pulang);

$photo = str_replace('data:image/jpeg;base64,', '', $file_foto);
$photo = str_replace(' ', '+', $photo);
$data = base64_decode($photo);

// Ensure the 'foto/masuk/' directory exists
$folder_path = '../presensi/foto/pulang/';
// Create a unique file name to avoid overwriting existing files
$nama_file = 'pulang_'.$id_karyawan . '_' . date('Y-m-d') . '.png';
file_put_contents($folder_path. $nama_file, $data);

$query = "UPDATE presensi SET jam_pulang='$jam_pulang', foto_pulang='$nama_file', lokasi_pulang='$lokasi_pulang',tanggal_pulang='$tanggal_pulang' WHERE id_karyawan='$id_karyawan' AND tanggal_masuk='$tanggal_pulang'";
$result = mysqli_query($connection, $query);

if($result) {
    $_SESSION['success'] = "Presensi pulang berhasil";
} else {
    $_SESSION['gagal'] = "Presensi pulang gagal. Error: " . mysqli_error($connection);
}
?>
