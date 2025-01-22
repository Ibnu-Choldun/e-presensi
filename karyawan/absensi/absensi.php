<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
  exit();
} else if($_SESSION['peran'] != 'Karyawan'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
  exit();
}

$judul = "Absensi";
include('../layouts/header.php');
include_once("../../config.php");

$id = $_SESSION['id'];
$result = mysqli_query($connection, "
    SELECT a.*, k.nama 
    FROM absensi a
    JOIN karyawan k ON a.id_karyawan = k.id
    WHERE a.id_karyawan = '$id'
    ORDER BY a.id DESC
");
?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">

  <a href="<?= base_url('karyawan/absensi/create.php') ?>" class="btn btn-success">[+] Tambah Data</a>

  <div class="row row-deck row-cards mt-1">

  <table class="table-bordered">
    <tr class="text-center">
        <th>NO</th>
        <th>Nama</th>
        <th>Tgl. Mulai</th>
        <th>Tgl. Selesai</th>
        <th>Keterangan</th>
        <th>Deskripsi</th>
        <th>File</th>
        <th>Status</th>
        <th>Aksi</th>
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
                <td><?= $row['nama']; ?></td>
                <td><?= date('d F Y', strtotime($row['tanggal_mulai'])) ?></td>
                <td><?= date('d F Y', strtotime($row['tanggal_selesai'])) ?></td>
                <td><?= $row['keterangan'] ?></td>
                <td><?= $row['deskripsi'] ?></td>

                <td>
                    <a target="_blank" href="<?= base_url('karyawan/absensi/file/' . $row['file']) ?>"
                        class="badge badge-pill bg-info">Lihat</a>
                    <a target="_blank" href="<?= base_url('karyawan/absensi/file/' . $row['file']) ?>" 
                        download class="badge badge-pill bg-info">Unduh</a>
                </td>
                
                <td><?= $row['status_pengajuan'] ?></td>

                <td>
                    <?php if ($row['status_pengajuan'] !== 'APPROVED') { ?>
                        <a href="<?= base_url('karyawan/absensi/edit.php?id=' . $row['id']) ?>" class="badge badge-pill bg-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                        </a>

                        <a href="<?= base_url('karyawan/absensi/delete.php?id=' . $row['id']) ?>" class="badge badge-pill bg-danger button-delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </a>
                    <?php } else { ?>
                        <span class="badge badge-pill bg-light">ACC</span>
                    <?php } ?>
                </td>
            </tr>

        <?php endwhile; ?>
    <?php } ?>
  </table>

  </div>
  </div>
</div>

<?php include('../layouts/footer.php');?>
