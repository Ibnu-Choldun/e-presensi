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

$judul= "Pengajuan Absensi";
include('../layouts/header.php');
include_once("../../config.php");

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT nama FROM karyawan WHERE id = '$id'");
$row = mysqli_fetch_assoc($result);
$nama_karyawan = $row['nama'];

if(isset($_POST['submit']))
{
    $id_karyawan = $_POST['id_karyawan'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $keterangan = $_POST['keterangan'];
    $deskripsi = $_POST['deskripsi'];
    $status_pengajuan = 'PENDING';

    // Validasi form
    $message = '';
    if(empty($keterangan)) {
        $message .= "Keterangan harus diisi.<br>";
    }
    if(empty($tanggal_mulai)) {
        $message .= "Tanggal mulai harus diisi.<br>";
    }
    if(empty($tanggal_selesai)) {
        $message .= "Tanggal selesai harus diisi.<br>";
    }
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai) && $tanggal_mulai > $tanggal_selesai) {
        $message .= "Tanggal mulai tidak boleh lebih dari tanggal selesai.<br>";
    }
    if(empty($deskripsi)) {
        $message .= "Deskripsi harus diisi.<br>";
    }

    // Proses file
    $file_name = '';
    if(isset($_FILES['file']) && $_FILES['file']['error'] === 0)
    {
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
            }
        }
    } else {
        $message .= "File surat keterangan harus diunggah.<br>";
    }

    if(!empty($message)) {
        $_SESSION['validate'] = $message;
    } else {
        $result = mysqli_query($connection, "INSERT INTO absensi (id_karyawan, keterangan, deskripsi, tanggal_mulai, tanggal_selesai, status_pengajuan, file)
        VALUES ('$id_karyawan', '$keterangan', '$deskripsi', '$tanggal_mulai', '$tanggal_selesai', '$status_pengajuan', '$file_name')");

        $_SESSION['success'] = 'Data berhasil disimpan';
        header("Location: absensi.php");
        exit();
    }
}

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT * FROM absensi WHERE id_karyawan = '$id' ORDER BY id DESC");
?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">

  <div class="card">
            <div class="card-body">
                
                <form action="<?= base_url('karyawan/absensi/create.php') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_karyawan" value="<?= $_SESSION['id'] ?>">

                    <?php if(isset($_SESSION['validate'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['validate'] ?></div>
                        <?php unset($_SESSION['validate']); ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="">Nama Karyawan</label>
                        <input type="text" class="form-control" value="<?= $nama_karyawan ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="">Keterangan</label>
                        <select name="keterangan" class="form-control">
                            <option value="">----Pilih Keterangan-----</option>
                            
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Cuti') { 
                                echo 'selected';
                                } ?> value="Cuti">Cuti</option>

                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Izin') { 
                                echo 'selected';
                                } ?> value="Izin">Izin</option>

                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Sakit') { 
                                echo 'selected';
                                } ?> value="Sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="10"><?= isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="<?= isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?= isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '' ?>">
                    </div>


                    <div class="mb-3">
                        <label for="">Surat Keterangan</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <button class="form-control btn btn-success" name="submit">SIMPAN</button>

                </form>
            </div>
        </div>

  </div>
</div>

<?php include('../layouts/footer.php');?>
