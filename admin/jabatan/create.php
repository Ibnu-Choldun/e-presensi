<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = " Tambah Data Jabatan";
include('../layouts/header.php');
require_once('../../config.php');

if(isset($_POST['submit']))
{
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if(empty($jabatan)) 
        {
            $message = "Jabatan harus diisi!";
        } else {
            //Cek Duplikasi
            $checkQuery = mysqli_query($connection, "SELECT * FROM jabatan WHERE jabatan = '$jabatan'");
            if(mysqli_num_rows($checkQuery) > 0) {
                $message = "Jabatan sudah ada";
            }
        }

        if(!empty($message))
        {
            $_SESSION['validate'] = $message;
        } else {
            $result = mysqli_query($connection, "INSERT INTO jabatan(jabatan) VALUES ('$jabatan')");
            $_SESSION['success'] = "Data berhasil disimpan";
            header("Location: jabatan.php");
            exit();
        }
    }
}

?>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">

            <div class="card">
                <div class="card-body">

                    <?php if(isset($_SESSION['validate'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['validate']; unset($_SESSION['validate']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/jabatan/create.php') ?>" method="POST">
                        <div class="mb-3">
                            <label for="">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan">
                        </div>

                        <button type="submit" name="submit" class="form-control btn btn-success">SIMPAN</button>
                    </form>

                </div>
            </div>
          </div>
        </div>

<?php include('../layouts/footer.php');?>
