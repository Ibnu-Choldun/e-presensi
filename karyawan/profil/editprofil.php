<?php 
session_start();
ob_start();

if(!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["peran"] != 'Karyawan'){
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$judul = "Update Profil";
include('../layouts/header.php');
require_once('../../config.php');

// Ambil data pengguna berdasarkan NIK yang ada di session
$nik = $_SESSION['nik']; // Pastikan NIK disimpan di session saat login

// Query untuk mendapatkan data dari tabel karyawan dan users
$query = "
    SELECT 
        karyawan.nik, karyawan.nama, karyawan.no_hp, karyawan.alamat, karyawan.jenis_kelamin, karyawan.jabatan, karyawan.foto,
        users.status, users.peran, users.username, users.password
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

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = $_POST['jabatan'];
    $status = $_POST['status'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $old_password = $_POST['old_password'];
    $old_foto = $_POST['old_foto'];

    // Validasi kata sandi
    if ($password != $_POST['ulangi_password']) {
        $message = "Kata sandi tidak cocok!";
    } else {
        // Jika password kosong, gunakan old_password hash dari database
        if (empty($password)) {
            $hashed_password = $old_password;
        } else {
            // Jika password diisi, hash password baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Cek apakah ada file foto baru yang diunggah
        if (!empty($_FILES['new_foto']['name'])) {
            $foto = $_FILES['new_foto']['name'];
            $tmp = $_FILES['new_foto']['tmp_name'];
            $dir = '../../assets/img/foto_karyawan/';
            $file_name = $nik.'.png'; // Pastikan nama file unik untuk menghindari tabrakan
            $upload_path = $dir . $file_name;

            // Validasi tipe file dan ukuran file
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $file_type = mime_content_type($tmp);
            $file_size = $_FILES['new_foto']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $message = "Format file tidak valid. Hanya jpg, jpeg, dan png yang diperbolehkan.";
            } elseif ($file_size > 10 * 1024 * 1024) { // 10 MB
                $message = "Ukuran file terlalu besar. Maksimal ukuran file adalah 10 MB.";
            } else {
                // Jika file valid, pindahkan ke direktori tujuan
                move_uploaded_file($tmp, $upload_path);
            }
        } else {
            // Jika tidak ada foto baru, gunakan foto lama
            $file_name = $old_foto;
        }

        // Lanjutkan jika tidak ada pesan kesalahan
        if (!isset($message)) {
            // Update tabel karyawan
            $update_karyawan_query = "UPDATE karyawan SET 
                                          nama='$nama',
                                          no_hp='$no_hp',
                                          alamat='$alamat',
                                          jenis_kelamin='$jenis_kelamin',
                                          jabatan='$jabatan',
                                          foto='$file_name'
                                      WHERE nik='$nik'";

            // Update tabel users
            $update_users_query = "UPDATE users SET 
                                    status='$status',
                                    username='$username',
                                    password='$hashed_password'
                                  WHERE id_karyawan=(SELECT id FROM karyawan WHERE nik='$nik')";
            
            if (mysqli_query($connection, $update_karyawan_query) && mysqli_query($connection, $update_users_query)) {
                $_SESSION['success'] = "Profil berhasil diperbarui!";
                header('Location: profil.php');
                exit;
            } else {
                $message = "Gagal memperbarui profil. Coba lagi.";
            }
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">

            <?php if(isset($message)) { echo "<p style='color:red;'>$message</p>"; } ?>
            <form action="editprofil.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">

                <div class="mb-3">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" value="<?php echo $data['nik']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo $data['nama']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?php echo $data['no_hp']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?php echo $data['alamat']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="Laki-laki" <?php if ($data['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php if ($data['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="<?php echo $data['jabatan']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <input type="text" name="status" class="form-control" value="<?php echo $data['status']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Peran</label>
                    <input type="text" name="peran" class="form-control" value="<?php echo $data['peran']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Foto</label>
                    <input type="file" name="new_foto" class="form-control">
                    <input type="hidden" name="old_foto" value="<?php echo $data['foto']; ?>">
                </div>

                <div class="mb-3">
                    <label>Nama Pengguna</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Ulangi Kata Sandi</label>
                    <input type="password" name="ulangi_password" class="form-control">
                    <input type="hidden" name="old_password" value="<?php echo $data['password']; ?>">
                </div>

                <div class="mb-3">
                    <button class="form-control btn btn-success" name="submit">SIMPAN</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    const nama = document.querySelector('input[name="nama"]').value;
    const no_hp = document.querySelector('input[name="no_hp"]').value;

    // Validasi kolom Nama hanya huruf
    const regexNama = /^[a-zA-Z\s]+$/;
    if (!regexNama.test(nama)) {
        alert('Nama hanya boleh berisi huruf dan spasi.');
        return false;
    }

    // Validasi kolom No HP hanya angka
    const regexNoHp = /^[0-9]+$/;
    if (!regexNoHp.test(no_hp)) {
        alert('No HP hanya boleh berisi angka.');
        return false;
    }

    return true;
}
</script>

<?php include('../layouts/footer.php') ?>
