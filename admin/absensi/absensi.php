<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Data Absensi";
include('../layouts/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT*FROM absensi ORDER BY id DESC");

?>

<div class="page-body">
    <div class="container-xl">
    <div class="row row-deck row-cards">

    <table class="table-bordered">
    <tr class="text-center">
        <th>NO</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Deskripsi</th>
        <th>File</th>
        <th>Status</th>
    </tr>

    <?php if (mysqli_num_rows($result) === 0) { ?>
        <tr>
            <td colspan="7">Tidak ada data</td>
        </tr>
    <?php } else {?>

        <?php $no = 1; 
        while($row = mysqli_fetch_array($result)) : ?>

            <tr class="text-center">
                <td><?= $no++; ?></td>
                <td><?= date('d F Y', strtotime($row['tanggal'])) ?></td>
                <td><?= $row['keterangan'] ?></td>
                <td><?= $row['deskripsi'] ?></td>

                <td>
                    <a target="_blank" href="<?= base_url('karyawan/absensi/file/' . $row['file']) ?>"
                        class="badge badge-pill bg-info">Lihat</a>
                    <a target="_blank" href="<?= base_url('karyawan/absensi/file/' . $row['file']) ?>" 
                        download class="badge badge-pill bg-info">Unduh</a>
                </td>
                
                <td><?php if($row['status_pengajuan'] == 'PENDING') : ?>
                        <a class="badge badge-pill bg-warning" href="<?= base_url('admin/absensi/detail.php?id='. $row['id']) ?>">PENDING</a>

                    <?php elseif($row['status_pengajuan'] == 'APPROVED') : ?>
                        <a class="badge badge-pill bg-success" href="<?= base_url('admin/absensi/detail.php?id='. $row['id']) ?>">APPROVED</a>

                        <?php elseif($row['status_pengajuan'] == 'REJECTED') : ?>
                            <a class="badge badge-pill bg-danger" href="<?= base_url('admin/absensi/detail.php?id='. $row['id']) ?>">REJECTED</a>

                        <?php endif; ?>
                </td>

            </tr>

        <?php endwhile; ?>
    <?php } ?>
  </table>

    </div>
</div>
</div>

<?php include('../layouts/footer.php');?>