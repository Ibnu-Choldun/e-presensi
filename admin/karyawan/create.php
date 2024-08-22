<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Tambah Karyawan";
include('../layouts/header.php');
require_once('../../config.php');

if(isset($_POST['submit'])) 
{
    $nik = htmlspecialchars($_POST['nik']);
    $nama = htmlspecialchars($_POST['nama']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $peran = htmlspecialchars($_POST['peran']);
    $status = htmlspecialchars($_POST['status']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $file_name = $nik . '.png';
    $dir = '../../assets/img/foto_karyawan/';
    $file_path = $dir . $file_name;

    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0)
    {
        $file = $_FILES['foto'];
        $tmp_file = $file['tmp_name'];
        $file_size = $file['size'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $approve_extension = ["jpg", "jpeg", "png"];
        $max_file = 10 * 1024 * 1024;

        if(!in_array(strtolower($extension), $approve_extension)) {
            $message = "Hanya file JPG, JPEG, dan PNG yang diizinkan.";
        } elseif($file_size > $max_file) {
            $message = "Ukuran file melebihi batas maksimum 10 MB.";
        } else {
            if (!move_uploaded_file($tmp_file, $file_path)) {
                $message = "Gagal mengunggah file.";
            }
        }
    } else {
        $message = "Gagal mengunggah file.";
    }

    if(empty($nik)) {
        $message = "NIK harus diisi.";
    }
    if(empty($nama)) {
        $message = "Nama harus diisi.";
    }
    if(empty($no_hp)) {
        $message = "No HP harus diisi.";
    }
    if(empty($alamat)) {
        $message = "Alamat harus diisi.";
    }
    if(empty($jenis_kelamin)) {
        $message = "Jenis Kelamin harus diisi.";
    }
    if(empty($jabatan)) {
        $message = "Jabatan harus diisi.";
    }
    if(empty($peran)) {
        $message = "Peran harus diisi.";
    }
    if(empty($status)) {
        $message = "Status harus diisi.";
    }
    if(empty($username)) {
        $message = "Nama Pengguna harus diisi.";
    }
    if(empty($password)) {
        $message = "Kata Sandi harus diisi.";
    }
    if ($_POST['password'] != $_POST['ulangi_password']) {
        $message = "Kata Sandi tidak sama.";
    }

    if(!empty($message)) {
        $_SESSION['validate'] = $message;
    } else {
        $karyawan = mysqli_query($connection, "INSERT INTO karyawan(nik, nama, no_hp, alamat, jenis_kelamin, jabatan, foto)
            VALUES('$nik', '$nama', '$no_hp', '$alamat', '$jenis_kelamin', '$jabatan', '$file_name')");

        $id_karyawan = mysqli_insert_id($connection);
        $user = mysqli_query($connection, "INSERT INTO users(id_karyawan, username, status, peran, password)
            VALUES('$id_karyawan', '$username', '$status', '$peran', '$password')");

        $_SESSION['success'] = 'Data berhasil disimpan';
        header("Location: karyawan.php");
        exit();
    }
}
?>

<div class="page-body">
    <div class="container-xl">

        <div class="card">
            <div class="card-body">
                <form action="<?= base_url('admin/karyawan/create.php') ?>" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="">NIK</label>
                        <input type="text" name="nik" class="form-control" 
                        value="<?php if(isset($_POST['nik'])) echo $_POST['nik'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Nama</label>
                        <input type="text" name="nama" class="form-control"
                        value="<?php if(isset($_POST['nama'])) echo $_POST['nama'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">No HP</label>
                        <input type="text" name="no_hp" class="form-control"
                        value="<?php if(isset($_POST['no_hp'])) echo $_POST['no_hp'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Alamat</label>
                        <input type="text" name="alamat" class="form-control"
                        value="<?php if(isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">----Pilih Jenis Kelamin-----</option>
                            
                            <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-Laki') { 
                                echo 'selected';
                                } ?> value="Laki-Laki">Laki-Laki</option>

                            <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') { 
                                echo 'selected';
                                } ?> value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Jabatan</label>
                        <select name="jabatan" class="form-control">
                            <option value="">----Pilih Jabatan-----</option>
                    <?php 
                    $pilih_jabatan = mysqli_query($connection, "SELECT*FROM jabatan ORDER BY jabatan DESC");

                    while($jabatan = mysqli_fetch_assoc($pilih_jabatan))
                    {
                        $nama_jabatan = $jabatan['jabatan'];

                        if(isset($_POST['jabatan']) && $_POST['jabatan']== $nama_jabatan)
                        {
                            echo '<option value="'. $nama_jabatan.'"selected= "selected">'.$nama_jabatan.'</option>';
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
                            
                            <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Aktif') { 
                                echo 'selected';
                                } ?> value="Aktif">Aktif</option>

                            <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Tidak Aktif') { 
                                echo 'selected';
                                } ?> value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Peran</label>
                        <select name="peran" class="form-control">
                            <option value="">----Pilih Peran-----</option>
                            
                            <option <?php if (isset($_POST['peran']) && $_POST['peran'] == 'Admin') { 
                                echo 'selected';
                                } ?> value="Admin">Admin</option>

                            <option <?php if (isset($_POST['peran']) && $_POST['peran'] == 'Karyawan') { 
                                echo 'selected';
                                } ?> value="Karyawan">Karyawan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Foto</label>
                        <input type="file" name="foto" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="">Nama Pengguna</label>
                        <input type="text" name="username" class="form-control"
                        value="<?php if(isset($_POST['username'])) echo $_POST['username'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Kata Sandi</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="">Ulangi Kata Sandi</label>
                        <input type="password" name="ulangi_password" class="form-control">
                    </div>

                    <button class="form-control btn btn-success" name="submit">SIMPAN</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layouts/footer.php');?>