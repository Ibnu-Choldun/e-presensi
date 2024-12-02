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

$judul = "Edit Pengajuan Absensi";
include('../layouts/header.php');
include_once("../../config.php");

$absensi_id = $_GET['id'];
$id_karyawan = $_SESSION['id'];

// Fetch existing data for the specified attendance request
$result = mysqli_query($connection, "SELECT * FROM absensi WHERE id = '$absensi_id' AND id_karyawan = '$id_karyawan'");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    $_SESSION['validate'] = "Data tidak ditemukan atau akses ditolak.";
    header("Location: absensi.php");
    exit();
}

// Pre-populate data
$keterangan = $row['keterangan'];
$deskripsi  = $row['deskripsi'];
$tanggal_mulai = $row['tanggal_mulai'];
$tanggal_selesai = $row['tanggal_selesai'];
$old_file   = $row['file'];

if(isset($_POST['update'])) {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $keterangan = $_POST['keterangan'];
    $deskripsi = $_POST['deskripsi'];
    $file_name = $old_file;

    // Validation
    $message = '';
    if(empty($keterangan)) $message .= "Keterangan harus diisi.<br>";
    if(empty($tanggal_mulai)) $message .= "Tanggal mulai harus diisi.<br>";
    if(empty($tanggal_selesai)) $message .= "Tanggal selesai harus diisi.<br>";
    if ($tanggal_mulai > $tanggal_selesai) $message .= "Tanggal mulai tidak boleh lebih dari tanggal selesai.<br>";
    if(empty($deskripsi)) $message .= "Deskripsi harus diisi.<br>";

    // File handling
    if(isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $file = $_FILES['file'];
        $tmp_file = $file['tmp_name'];
        $file_size = $file['size'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $approve_extension = ["jpg", "jpeg", "png", "docx", "pdf"];
        $max_file = 10 * 1024 * 1024; // 10MB

        if(!in_array(strtolower($extension), $approve_extension)) {
            $message .= "Hanya file JPG, JPEG, PNG, DOCX, dan PDF yang diizinkan.<br>";
        } elseif($file_size > $max_file) {
            $message .= "Ukuran file melebihi batas maksimum 10 MB.<br>";
        } else {
            $file_name = 'surat_' . $id_karyawan . '_' . date('Ymd') . '.' . $extension;
            $dir = '../absensi/file/';
            $file_path = $dir . $file_name;

            if (!move_uploaded_file($tmp_file, $file_path)) {
                $message .= "Gagal mengunggah file.<br>";
            } else {
                // Delete old file if a new file is uploaded
                if ($old_file && file_exists($dir . $old_file)) {
                    unlink($dir . $old_file);
                }
            }
        }
    }

    if(!empty($message)) {
        $_SESSION['validate'] = $message;
    } else {
        $update_result = mysqli_query($connection, "UPDATE absensi SET 
            keterangan = '$keterangan', deskripsi = '$deskripsi', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = '$tanggal_selesai', file = '$file_name' 
            WHERE id = '$absensi_id'");

        $_SESSION['success'] = 'Data berhasil diupdate';
        header("Location: absensi.php");
        exit();
    }
}
?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-body">
        <form action="<?= base_url('karyawan/absensi/edit.php?id=' . $absensi_id) ?>" method="POST" enctype="multipart/form-data">
          <?php if(isset($_SESSION['validate'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['validate'] ?></div>
            <?php unset($_SESSION['validate']); ?>
          <?php endif; ?>

          <div class="mb-3">
            <label for="">Keterangan</label>
            <select name="keterangan" class="form-control">
              <option value="">----Pilih Keterangan-----</option>
              <option <?= ($keterangan == 'Cuti') ? 'selected' : '' ?> value="Cuti">Cuti</option>
              <option <?= ($keterangan == 'Izin') ? 'selected' : '' ?> value="Izin">Izin</option>
              <option <?= ($keterangan == 'Sakit') ? 'selected' : '' ?> value="Sakit">Sakit</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="10"><?= $deskripsi ?></textarea>
          </div>

          <div class="mb-3">
            <label for="">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tanggal_mulai ?>">
          </div>

          <div class="mb-3">
            <label for="">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="form-control" value="<?= $tanggal_selesai ?>">
          </div>

          <div class="mb-3">
            <label for="">Surat Keterangan</label>
            <input type="file" name="file" class="form-control">
            <?php if ($old_file): ?>
              <small>File saat ini: <?= $old_file ?></small>
            <?php endif; ?>
          </div>

          <button class="form-control btn btn-success" name="update">UPDATE</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('../layouts/footer.php'); ?>
