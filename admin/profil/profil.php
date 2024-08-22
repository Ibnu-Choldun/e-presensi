<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
  exit;
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
  exit;
}

$judul = "Profil";
include('../layouts/header.php');
require_once('../../config.php');

// Ambil data pengguna berdasarkan NIK yang ada di session
$nik = $_SESSION['nik']; // Pastikan NIK disimpan di session saat login

// Query untuk mendapatkan data dari tabel karyawan dan users
$query = "
    SELECT 
        karyawan.nik, karyawan.nama, karyawan.no_hp, karyawan.alamat, karyawan.jenis_kelamin, karyawan.jabatan, karyawan.foto,
        users.status, users.peran, users.username
    FROM 
        karyawan 
    JOIN 
        users 
    ON 
        karyawan.id = users.id_karyawan 
    WHERE 
        karyawan.nik = '$nik'
";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">

                <table class="table">

                <img src="<?= base_url('assets/img/foto_karyawan/'.$data['foto']) ?>" alt="" width="100%">

                    <tr>
                        <th>NIK</th>
                        <td>: <?php echo $data['nik']; ?></td>
                    </tr>
                    
                    <tr>
                        <th>Nama</th>
                        <td>: <?php echo $data['nama']; ?></td>
                    </tr>

                    <tr>
                        <th>No HP</th>
                        <td>: <?php echo $data['no_hp']; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: <?php echo $data['alamat']; ?></td>
                    </tr>

                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>: <?php echo $data['jenis_kelamin']; ?></td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>: <?php echo $data['jabatan']; ?></td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>: <?php echo $data['status']; ?></td>
                    </tr>
                    <tr>
                        <th>Peran</th>
                        <td>: <?php echo $data['peran']; ?></td>
                    </tr>

                    <tr>
                        <th>Nama Pengguna</th>
                        <td>: <?php echo $data['username']; ?></td>
                    </tr>

                </table>
                <a href="<?= base_url('admin/profil/editprofil.php') ?>" class="form-control btn btn-success mt-2" >Edit Profil</a>
                
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>
