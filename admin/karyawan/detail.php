<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Detail Karyawan";
include('../layouts/header.php');
require_once('../../config.php');

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT users.id_karyawan, users.username, users.password, users.status, users.peran, karyawan. * FROM users JOIN karyawan ON users.id_karyawan = karyawan.id WHERE karyawan.id=$id");
?>

<?php  while($karyawan= mysqli_fetch_array($result)) : ?>

<!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            
        <div class="row row-deck row-cards mt-1">
            <div class="card">
                <div class="card-body">

                <table class="table">

                <img src="<?= base_url('assets/img/foto_karyawan/'.$karyawan['foto']) ?>" alt="" width="100%">

                    <tr>
                        <td>NIK</td>
                        <td>: <?= $karyawan['nik'] ?></td>
                    </tr>

                    <tr>
                        <td>Nama</td>
                        <td>: <?= $karyawan['nama'] ?></td>
                    </tr>

                    <tr>
                        <td>Nama Pengguna</td>
                        <td>: <?= $karyawan['username'] ?></td>
                    </tr>

                    <tr>
                        <td>NO HP</td>
                        <td>: <?= $karyawan['no_hp'] ?></td>
                    </tr>

                    <tr>
                        <td>Alamat</td>
                        <td>: <?= $karyawan['alamat'] ?></td>
                    </tr>

                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>: <?= $karyawan['jenis_kelamin'] ?></td>
                    </tr>

                    <tr>
                        <td>Jabatan</td>
                        <td>: <?= $karyawan['jabatan'] ?></td>
                    </tr>

                    <tr>
                        <td>Status</td>
                        <td>: <?= $karyawan['status'] ?></td>
                    </tr>

                    <tr>
                        <td>Peran</td>
                        <td>: <?= $karyawan['peran'] ?></td>
                    </tr>

                    <tr>
                        <td>Alamat</td>
                        <td>: <?= $karyawan['alamat'] ?></td>
                    </tr>
                    
                </table>

                </div>
            </div>

        </div>
    </div>
</div>

<?php endwhile; ?>

    <?php include('../layouts/footer.php');?>

