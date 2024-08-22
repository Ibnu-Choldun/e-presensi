<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Data Jabatan";
include('../layouts/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT*FROM jabatan ORDER BY id ASC");
?>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">

            <a href="<?= base_url('admin/jabatan/create.php') ?>" class="btn btn-success">[+] Tambah Data</a>

            <div class="row row-deck row-cards mt-1">

            <table class="table table-bordered mt-1">
                <tr class="text-center">
                    <th>NO</th>
                    <th>Nama Jabatan</th>
                    <th>Aksi</th>
                </tr>

                <?php if(mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="3">Tidak ada data yang ditampilkan, silakan tambah data</td>
                    </tr>
                    <?php else: ?>

                <?php $no = 1;
                while ($jabatan = mysqli_fetch_array($result)): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $jabatan['jabatan'] ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('admin/jabatan/edit.php?id='.$jabatan['id']) ?>" class="badge bg-info"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg></a>

                        <a href="<?= base_url('admin/jabatan/delete.php?id='.$jabatan['id']) ?>" class="badge bg-danger button-delete"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </a>
                    </td>
                </tr>

                    <?php endwhile; ?>

                    <?php endif; ?>
            </table>

            </div>
          </div>
        </div>

<?php include('../layouts/footer.php');?>
        