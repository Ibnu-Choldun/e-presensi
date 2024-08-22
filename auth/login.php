<?php
session_start();
require_once('../config.php');

if (isset($_POST["login"])) 
{
  $username = $_POST["username"];
  $password = $_POST["password"];

  $result = mysqli_query($connection, "SELECT*FROM users JOIN karyawan ON users.id_karyawan = karyawan.id WHERE username = '$username'");

  if(mysqli_num_rows($result) === 1)
  {
    $row = mysqli_fetch_assoc($result);

    if(password_verify($password, $row["password"]))
    {
      if($row['status']== 'Aktif'){

        $_SESSION["login"]           = true;
        $_SESSION['id']              = $row['id'];
        $_SESSION['peran']           = $row['peran'];
        $_SESSION['nama']            = $row['nama'];
        $_SESSION['nik']             = $row['nik'];
        $_SESSION['jabatan']         = $row['jabatan'];
        $_SESSION['presensi']        = $row['presensi'];

        if($row['peran']=== 'Admin') {
          header("Location: ../admin/beranda/beranda.php");
          exit();
        } else {
          header("Location: ../karyawan/beranda/beranda.php");
          exit();
        }

      }else {
        $_SESSION["gagal"] = "Akun tidak aktif";
      }

    }else {
      $_SESSION["gagal"] = "Password salah, silakan coba lagi";
    }

  } else {
   $_SESSION["gagal"] = "Nama Pengguna salah, silakan coba lagi";
  }
}

?>
<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta20
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Login | PT Tiga Serangkai</title>

    <!-- Logo -->
    <link rel="icon" href="<?= base_url('assets/img/logo.png')?>" type="image/x-icon"/>

    <!-- CSS files -->
    <link href="<?= base_url('assets/css/tabler.min.css?1692870487') ?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1692870487') ?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/demo.min.css?1692870487') ?>" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>

  <body  class=" d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark">
            <img src="<?= base_url('assets/img/logo.png')?>" width="110" height="32" alt="Tabler" class="navbar-brand-image"> PT Tiga Serangkai
          </a>
        </div>

        <?php 
        if(isset($_GET['pesan'])) {
          if($_GET['pesan']== "belum_login") {
            $_SESSION['gagal'] = 'Anda Belum Login!';
          }else if($_GET['pesan']== "akses_ditolak") {
            $_SESSION['gagal'] = 'Akses Ditolak!';
          }
        }
        ?>

        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Masuk Ke Akun Anda</h2>
            <form action="" method="POST" autocomplete="off" novalidate>
              <div class="mb-3">
                <label class="form-label">Nama Pengguna</label>
                <input type="text" class="form-control" autofocus name="username" placeholder="Nama Pengguna" autocomplete="off">
              </div>

              <div class="mb-2">
                <label class="form-label">
                  Kata Sandi
                </label>
                <div class="input-group input-group-flat">
                  <input type="password" class="form-control" autofocus name="password"  placeholder="Kata Sandi"  autocomplete="off">
                </div>
              </div>

              <div class="form-footer">
                <button type="submit" name="login" class="btn btn-primary w-100">LOGIN</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js?1692870487') ?>" defer></script>
    <!-- Tabler Core -->
    <script src="<?= base_url('assets/js/tabler.min.js?1692870487') ?>" defer></script>
    <script src="<?= base_url('assets/js/demo.min.js?1692870487') ?>" defer></script>

    <!--sweetalert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($_SESSION['gagal'])): ?>
        <script>
          Swal.fire({
            icon: "warning",
            title: "Maaf",
            text: "<?= $_SESSION['gagal']; ?>"
          });
        </script>

        <?php unset($_SESSION['gagal']); ?>
    <?php endif; ?>
    
  </body>
</html>