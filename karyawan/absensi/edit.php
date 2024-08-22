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

$judul= "Edit Absensi";
include('../layouts/header.php');
include_once("../../config.php");

if(isset($_POST['update']))
{
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $deskripsi = $_POST['deskripsi'];
    $file_name = $_POST['old_file']; // Default gunakan file lama

    if($_FILES['new_file']['error'] === 0) // Cek jika ada file baru yang diunggah
    {
        $file = $_FILES['new_file'];
        $tmp_file = $file['tmp_name'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = 'surat_' . $id . '_' . date('Ymd') . '.' . $extension; // Nama file baru
        $file_size = $file['size'];
        $dir = '../absensi/file/';
        $file_path = $dir . $file_name;

        $approve_extension = ["jpg", "jpeg", "png", "docx", "pdf"];
        $max_file = 10 * 1024 * 1024; // 10MB

        if(!in_array(strtolower($extension), $approve_extension)) {
            $message = "Hanya file JPG, JPEG, PNG, DOCX, dan PDF yang diizinkan.<br>";
        } elseif($file_size > $max_file) {
            $message .= "Ukuran file melebihi batas maksimum 10 MB.<br>";
        } else {
            if (!move_uploaded_file($tmp_file, $file_path)) {
                $message .= "Gagal mengunggah file.<br>";
            }
        }
    }

    // Validasi form
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        if(empty($keterangan)) {
            $message .= "Keterangan harus diisi.<br>";
        }
        if(empty($tanggal)) {
            $message .= "Tanggal harus diisi.<br>";
        }
        if(empty($deskripsi)) {
            $message .= "Deskripsi harus diisi.<br>";
        }

        if(!empty($message)) {
            $_SESSION['validate'] = $message;
        } else {
            $result = mysqli_query($connection, "UPDATE absensi SET 
                keterangan = '$keterangan', deskripsi = '$deskripsi',
                tanggal = '$tanggal', file = '$file_name' WHERE id = '$id'");

            $_SESSION['success'] = 'Data berhasil diupdate';
            header("Location: absensi.php");
            exit();
        }
    }
}

$result = mysqli_query($connection, "SELECT * FROM absensi WHERE id = '$id'");
while($row = mysqli_fetch_array($result))
{
    $keterangan = $row['keterangan'];
    $deskripsi  = $row['deskripsi'];
    $file       = $row['file'];
    $tanggal    = $row['tanggal'];
}
?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">

  <div class="card">
    <div class="card-body">

      <form action="<?= base_url('karyawan/absensi/edit.php') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $id ?>">

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
          <label for="">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?= $tanggal; ?>">
        </div>

        <div class="mb-3">
          <label for="">Surat Keterangan</label>
          <input type="file" name="new_file" class="form-control">
          <input type="hidden" name="old_file" value="<?= $file ?>">
        </div>

        <button class="form-control btn btn-success" name="update">UPDATE</button>
      </form>
    </div>
  </div>

  </div>
</div>

<?php include('../layouts/footer.php');?>
