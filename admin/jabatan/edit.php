<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = " Edit Data Jabatan";
include('../layouts/header.php');
require_once('../../config.php');

if(isset($_POST['update']))
{
    $id = $_POST['id'];
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if($_SERVER["REQUEST_METHOD"]== "POST")
    {
        if(empty($jabatan)) 
        {
            $message= "Jabatan harus diisi!";
        }

        if(!empty($message))
        {
            $_SESSION['validate'] = $message;
        } else {
            $result = mysqli_query($connection, "UPDATE jabatan SET jabatan='$jabatan' WHERE id=$id");
            $_SESSION['success'] = "Data berhasil diupdate";
            header("Location: jabatan.php");
            exit();
        }
    }
}

//$id = $_GET['id'];
$id =  isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT*FROM jabatan WHERE id=$id");

while($jabatan = mysqli_fetch_array($result)) 
{
    $nama_jabatan = $jabatan['jabatan'];
}

?>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">

            <div class="card">
                <div class="card-body">

                    <form action="<?= base_url('admin/jabatan/edit.php') ?>" method="POST">
                        <div class="mb-3">
                            <label for="">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan" value="<?= $nama_jabatan ?>">
                        </div>
                        <input type="hidden" value="<?= $id ?>" name="id">
                        <button type="submit" name="update" class="form-control btn btn-success">UPDATE</button>
                    </form>

                </div>
            </div>
          </div>
        </div>

<?php include('../layouts/footer.php');?>
        