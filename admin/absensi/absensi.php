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

// Get the search parameters from the GET request
$search_date = isset($_GET['search_date']) ? $_GET['search_date'] : '';
$search_employee = isset($_GET['search_employee']) ? $_GET['search_employee'] : '';

// Base query
$query = "SELECT absensi.*, karyawan.nama 
          FROM absensi
          JOIN karyawan ON absensi.id_karyawan = karyawan.id";

// Add filters if provided
if ($search_date) {
    $query .= " WHERE ('$search_date' BETWEEN absensi.tanggal_mulai AND absensi.tanggal_selesai)";
}

if ($search_employee) {
    if ($search_date) {
        $query .= " AND absensi.id_karyawan = '$search_employee'";
    } else {
        $query .= " WHERE absensi.id_karyawan = '$search_employee'";
    }
}

// Order the results
$query .= " ORDER BY absensi.id DESC";

// Execute the query
$result = mysqli_query($connection, $query);
?>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
        
            <!-- Search Form -->
            <form method="GET" class="mb-4">
              <div class="row">
                <div class="col-md-4">
                  <label for="search_date" class="form-label">Tanggal Absen</label>
                  <input type="date" id="search_date" name="search_date" class="form-control" value="<?= isset($_GET['search_date']) ? $_GET['search_date'] : '' ?>">
                </div>
                <div class="col-md-4">
                  <label for="search_employee" class="form-label">Nama Karyawan</label>
                  <select id="search_employee" name="search_employee" class="form-control">
                    <option value="">Semua Karyawan</option>
                    <?php
                      // Query to get all employees
                      $employee_query = mysqli_query($connection, "SELECT id, nama FROM karyawan");
                      while ($employee = mysqli_fetch_assoc($employee_query)) {
                        echo '<option value="' . $employee['id'] . '"';
                        if (isset($_GET['search_employee']) && $_GET['search_employee'] == $employee['id']) {
                          echo ' selected';
                        }
                        echo '>' . $employee['nama'] . '</option>';
                      }
                    ?>
                  </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
              </div>
            </form>

            <!-- Data Table -->
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

<?php include('../layouts/footer.php'); ?>
