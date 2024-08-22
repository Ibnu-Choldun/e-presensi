<?php 
session_start();
ob_start();

if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Update Karyawan";
include('../layouts/header.php');
require_once('../../config.php');

if(isset($_POST['edit'])) 
{
    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $peran = htmlspecialchars($_POST['peran']);
    $status = htmlspecialchars($_POST['status']);
    $username = htmlspecialchars($_POST['username']);
    $nik = htmlspecialchars($_POST['nik']); // Ambil NIK dari form atau database

    if(empty($_POST['password'])) {
        $password = $_POST['old_password'];
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if($_FILES['new_foto']['error'] === 4) {
        $file_name = $_POST['old_foto'];
    } else {
        if(isset($_FILES['new_foto'])) {
            $file = $_FILES['new_foto'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION); // Mendapatkan ekstensi file
            $file_name = $nik . '.' . $extension; // Penamaan file sesuai NIK
            $tmp_file = $file['tmp_name'];
            $dir = '../../assets/img/foto_karyawan/'.$file_name;
            $file_size = $file['size'];

            $approve_extension = ["jpg", "jpeg", "png"];
            $max_file = 10*1024*1024;

            if(!in_array(strtolower($extension), $approve_extension)) {
                $message = "Hanya file JPG, JPEG, dan PNG yang diizinkan.";
            } elseif($file_size > $max_file) {
                $message = "Ukuran file melebihi batas maksimum 10 MB.";
            } else {
                $old_file_path = '../../assets/img/foto_karyawan/'.$_POST['old_foto'];
                if(file_exists($old_file_path) && $_POST['old_foto'] != 'default.jpg') {
                    unlink($old_file_path);
                }
                move_uploaded_file($tmp_file, $dir);
            }
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($nama)) {
            $message = "Nama harus diisi";
        } elseif (empty($no_hp)) {
            $message = "NO HP harus diisi";
        } elseif (empty($alamat)) {
            $message = "Alamat harus diisi";
        } elseif (empty($jenis_kelamin)) {
            $message = "Jenis Kelamin harus diisi";
        } elseif (empty($jabatan)) {
            $message = "Jabatan harus diisi";
        } elseif (empty($peran)) {
            $message = "Peran harus diisi";
        } elseif (empty($status)) {
            $message = "Status harus diisi";
        } elseif (empty($username)) {
            $message = "Nama Pengguna harus diisi";
        } elseif (empty($password)) {
            $message = "Kata Sandi harus diisi";
        } elseif ($_POST['password'] != $_POST['ulangi_password']) {
            $message = "Kata Sandi tidak sama";
        }

        if(!empty($message)) {
            $_SESSION['validate'] = ($message);
        } else {
            $karyawan = mysqli_query($connection, "UPDATE karyawan SET
                nama = '$nama',
                no_hp = '$no_hp',
                alamat = '$alamat',
                jenis_kelamin = '$jenis_kelamin',
                jabatan = '$jabatan',
                foto = '$file_name'
                WHERE id = '$id'"
            );
            $user = mysqli_query($connection, "UPDATE users SET
                username = '$username',
                password = '$password',
                peran = '$peran',
                status = '$status'
                WHERE id= '$id'"
            );

            $_SESSION['success'] = 'Data berhasil diupdate';
            header("Location: karyawan.php");
            exit();
        } 
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT users.id_karyawan, users.username, users.password, users.status, users.peran, karyawan. * FROM users JOIN karyawan ON users.id_karyawan = karyawan.id
WHERE karyawan.id = $id");

while($karyawan = mysqli_fetch_array($result)) {
    $nik = $karyawan['nik'];
    $nama = $karyawan['nama'];
    $no_hp = $karyawan['no_hp'];
    $alamat = $karyawan['alamat'];
    $jenis_kelamin = $karyawan['jenis_kelamin'];
    $jabatan = $karyawan['jabatan'];
    $status = $karyawan['status'];
    $peran = $karyawan['peran'];
    $foto = $karyawan['foto'];
    $username = $karyawan['username'];
    $password = $karyawan['password'];
}

?>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('admin/karyawan/edit.php') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="">NIK</label>
                        <input type="text" name="nik" class="form-control" readonly value="<?= $nik ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= $nama ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= $no_hp ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?= $alamat ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">----Pilih Jenis Kelamin-----</option>
                            <option <?php if ($jenis_kelamin == 'Laki-Laki') { echo 'selected'; } ?> value="Laki-Laki">Laki-Laki</option>
                            <option <?php if ($jenis_kelamin == 'Perempuan') { echo 'selected'; } ?> value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Jabatan</label>
                        <select name="jabatan" class="form-control">
                            <option value="">----Pilih Jabatan-----</option>
                            <?php 
                            $pilih_jabatan = mysqli_query($connection, "SELECT * FROM jabatan ORDER BY jabatan DESC");
                            while($bagian = mysqli_fetch_assoc($pilih_jabatan)) {
                                $nama_jabatan = $bagian['jabatan'];
                                if($jabatan == $nama_jabatan) {
                                    echo '<option value="'. $nama_jabatan.'" selected="selected">'.$nama_jabatan.'</option>';
                                } else {
                                    echo '<option value="'.$nama_jabatan. '">'.$nama_jabatan.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Status</label>
                        <select name="status" class="form-control">
                            <option value="">----Pilih Status-----</option>
                            <option <?php if ($status == 'Aktif') { echo 'selected'; } ?> value="Aktif">Aktif</option>
                            <option <?php if ($status == 'Tidak Aktif') { echo 'selected'; } ?> value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Peran</label>
                        <select name="peran" class="form-control">
                            <option value="">----Pilih Peran-----</option>
                            <option <?php if ($peran == 'Admin') { echo 'selected'; } ?> value="Admin">Admin</option>
                            <option <?php if ($peran == 'Karyawan') { echo 'selected'; } ?> value="Karyawan">Karyawan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Foto</label>
                        <input type="file" name="new_foto" class="form-control mt-2">
                        <input type="hidden" name="old_foto" value="<?= $foto ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Nama Pengguna</label>
                        <input type="text" name="username" class="form-control" value="<?= $username ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Kata Sandi</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="">Ulangi Kata Sandi</label>
                        <input type="password" name="ulangi_password" class="form-control">
                        <input type="hidden" name="old_password" value="<?= $password ?>">
                    </div>

                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" name="edit" class="form-control btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php') ?>
