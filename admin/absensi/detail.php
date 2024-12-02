<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Detail Absensi";
include('../layouts/header.php');
require_once('../../config.php');

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $status_pengajuan = $_POST['status_pengajuan'];

    $result = mysqli_query($connection, "UPDATE absensi SET status_pengajuan = '$status_pengajuan' WHERE id = $id");

    $_SESSION['success'] = 'Status berhasil diupdate';
            header("Location: absensi.php");
            exit();
}

$id = $_GET['id'];
$result = mysqli_query($connection, "
    SELECT absensi.*, karyawan.nama AS nama_karyawan 
    FROM absensi 
    JOIN karyawan ON absensi.id_karyawan = karyawan.id 
    WHERE absensi.id = '$id'
");

$result = mysqli_query($connection, "
    SELECT absensi.*, karyawan.nama AS nama_karyawan 
    FROM absensi 
    JOIN karyawan ON absensi.id_karyawan = karyawan.id 
    WHERE absensi.id = '$id'
");
while($row = mysqli_fetch_array($result))
{
    $nama_karyawan = $row['nama_karyawan'];
    $keterangan = $row['keterangan'];
    $tanggal_mulai    = $row['tanggal_mulai'];
    $tanggal_selesai  = $row['tanggal_selesai'];
    $status_pengajuan = $row['status_pengajuan'];
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">

            <form action="" method="POST">

                <div class="mb-3">
                    <label for="">Nama</label>
                    <input type="text" name="nama_karyawan" class="form-control" value="<?= $nama_karyawan; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tanggal_mulai; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="">Tanggal Selesai</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="<?= $keterangan; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="">Status Pengajuan</label>
                    <select name="status_pengajuan" class="form-control">
                        <option value="" <?= empty($status_pengajuan) ? 'selected' : 'disabled' ?>>----Pilih Status-----</option>
                        <option <?= ($status_pengajuan == 'PENDING') ? 'selected' : '' ?> value="PENDING">PENDING</option>
                        <option <?= ($status_pengajuan == 'APPROVED') ? 'selected' : '' ?> value="APPROVED">APPROVED</option>
                        <option <?= ($status_pengajuan == 'REJECTED') ? 'selected' : '' ?> value="REJECTED">REJECTED</option>
                    </select>
                </div>


                <input type="hidden" name="id" value="<?= $id ?>">

                <button type="submit" class="form-control btn btn-success" name="update">UPDATE</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php');?>