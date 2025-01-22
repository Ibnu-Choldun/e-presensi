<?php
global $judul;
require_once('../../config.php');

// Memulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memastikan 'id' ada dalam sesi
if (!isset($_SESSION['id'])) {
    // Arahkan pengguna ke halaman login jika 'id' tidak ada
    header("Location: ../auth/login.php");
    exit();
}

// Koneksi ke database menggunakan mysqli
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$connection) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Query untuk mengambil data karyawan berdasarkan id
$id = $_SESSION['id'];
$query = "SELECT nama, jabatan,foto FROM karyawan WHERE id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $namaUser = $user['nama'];
    $jabatanUser = $user['jabatan'];
} else {
    $namaUser = "Nama tidak ditemukan";
    $jabatanUser = "Jabatan tidak ditemukan";
}
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>E-Presensi | PT Tiga Serangkai</title>
    
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
  <body >
    <script src="./dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page">
      <!-- Navbar -->
      <header class="navbar navbar-expand-md d-print-none" >
        <div class="container-xl">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href=".">
              <img src="<?= base_url('assets/img/logo.png')?>" width="110" height="32" alt="Tabler" class="navbar-brand-image">
            </a>
          </h1>
          <div class="navbar-nav flex-row order-md-last">

            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <!-- Url ganti dengan foto yang ada didatabase -->
                <span class="avatar avatar-sm" style="background-image: url('../../assets/img/foto_karyawan/<?= htmlspecialchars($user['foto']) ?>')"></span>
                <div class="d-none d-xl-block ps-2">
                  <!-- Tampilkan Nama User (Pawel Kuna) -->
                  <div><?= htmlspecialchars($user['nama']) ?></div>
                  <div class="mt-1 small text-secondary"><?= htmlspecialchars($user['jabatan']) ?></div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a href="<?= base_url('karyawan/profil/profil.php') ?>" class="dropdown-item">Profil</a>
                <a href="<?= base_url('auth/logout.php')?>" class="dropdown-item">Logout</a>
              </div>
            </div>
          </div>
        </div>
      </header>
      <header class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="navbar">
            <div class="container-xl">
              <ul class="navbar-nav">

                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('karyawan/beranda/beranda.php') ?>" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Beranda
                    </span>
                  </a>
                </li>


                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('karyawan/presensi/presensi.php') ?>" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-checklist"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8" /><path d="M14 19l2 2l4 -4" /><path d="M9 8h4" /><path d="M9 12h2" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Presensi
                    </span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('karyawan/absensi/absensi.php') ?>" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/checkbox -->
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-notes-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3h10a2 2 0 0 1 2 2v10m0 4a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-14" /><path d="M11 7h4" /><path d="M9 11h2" /><path d="M9 15h4" /><path d="M3 3l18 18" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Absensi
                    </span>
                  </a>
                </li>
              </ul>

            </div>
          </div>
        </div>
      </header>
      <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <!-- Page pre-title -->

                <h2 class="page-title">
                <?= $judul ?>
                </h2>
              </div>

            </div>
          </div>
        </div>