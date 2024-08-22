<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if($_SESSION['peran'] != 'Karyawan'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul= "Beranda";
include('../layouts/header.php');
include_once("../../config.php");

$result = mysqli_query($connection, "SELECT * FROM presensi WHERE id_karyawan='".$_SESSION['id']."' ORDER BY tanggal_masuk DESC LIMIT 1");

$lokasi_masuk = '';
$lokasi_keluar = '';
$jam_masuk = '';
$jam_pulang = '';

if ($lokasi = mysqli_fetch_assoc($result)) {
    $lokasi_masuk = $lokasi['lokasi_masuk'];
    $lokasi_keluar = $lokasi['lokasi_keluar'] ?? ''; 
    $jam_masuk = $lokasi['jam_masuk'];
    $jam_pulang = $lokasi['jam_pulang'] ?? ''; 
}
?>

<style>
  .parent_clock {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 20px;
    text-align: center;
    font-weight: bold;
    justify-content: center;
  }

  .parent_date {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 20px;
    text-align: center;
    font-weight: bold;
    justify-content: center;
  }
</style>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">

    <!-- Jam -->
    <div class="row">
      <div class="col-12">
        <div class="card text-center">
          <div class="card-body">

          <div class="parent_clock">
              <div id="jam_masuk"></div>
              <div>:</div>
              <div id="menit_masuk"></div>
              <div>:</div>
              <div id="detik_masuk"></div>
            </div>

            <div class="parent_date">
              <div id="tanggal_masuk"></div>
              <div class="ms-1"></div>
              <div id="bulan_masuk"></div>
              <div class="ms-1"></div>
              <div id="tahun_masuk"></div>
            </div>

            </div>
        </div>
      </div>
    </div>

    <!-- Presensi Masuk -->
    <div class="row mt-2">
      <div class="col-12">
        <div class="card text-center">
          <div class="card-body">

          <?php 
          $id_karyawan = $_SESSION['id'];
          $tanggal_hari_ini = date('Y-m-d');
      
          $cek_masuk = mysqli_query($connection, "SELECT * FROM presensi WHERE id_karyawan = '$id_karyawan' AND tanggal_masuk = '$tanggal_hari_ini'"); 
          ?>

          <?php if (mysqli_num_rows($cek_masuk) === 0) { ?>

            <form method="POST" action="<?= base_url('karyawan/presensi/masuk.php') ?>">
              <input type="hidden" name="lokasi_masuk" id="lokasi_masuk">
              <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_masuk">
              <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_masuk">
              <button type="submit" name="button_masuk" class="form-control btn btn-info mt-2">Masuk</button>
            </form>
            <?php }else{ ?>
              <h4>Presensi Masuk Berhasil</h4>

            <?php } ?>

          </div>
        </div>
      </div>
    </div>

    <!-- Presensi Pulang -->
    <div class="row mt-2">
      <div class="col-12">  
        <div class="card text-center"> 
          <div class="card-body">

          <?php   
          $cek_pulang = mysqli_query($connection, "SELECT * FROM presensi WHERE id_karyawan = '$id_karyawan' AND tanggal_pulang = '$tanggal_hari_ini'"); 
          ?>

          <?php if (mysqli_num_rows($cek_pulang) > 0) { ?>
            <h4>Presensi Pulang Berhasil</h4>
            <?php }elseif(mysqli_num_rows($cek_masuk) === 0){ ?>
              <h4>Presensi Masuk Terlebih Dahulu</h4>
              <?php } else {?>

            <form method="POST" action="<?= base_url('karyawan/presensi/pulang.php') ?>">
              <input type="hidden" name="lokasi_keluar" id="lokasi_keluar">
              <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_pulang">
              <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_pulang">
              <button type="submit" name="button_pulang" class="form-control btn btn-danger mt-2">Pulang</button>
            </form>
              <?php } ?>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

  function updateClockAndDate(elementPrefix) {
    const waktu = new Date();
    document.getElementById(`tanggal_${elementPrefix}`).innerHTML = waktu.getDate();
    document.getElementById(`bulan_${elementPrefix}`).innerHTML = bulan[waktu.getMonth()];
    document.getElementById(`tahun_${elementPrefix}`).innerHTML = waktu.getFullYear();
    document.getElementById(`jam_${elementPrefix}`).innerHTML = String(waktu.getHours()).padStart(2, '0');
    document.getElementById(`menit_${elementPrefix}`).innerHTML = String(waktu.getMinutes()).padStart(2, '0');
    document.getElementById(`detik_${elementPrefix}`).innerHTML = String(waktu.getSeconds()).padStart(2, '0');
  }

  function initClock() {
    setInterval(function() {
      updateClockAndDate('masuk');
      updateClockAndDate('pulang');
    }, 1000);
  }

  function getLocation() {
    if(navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    document.getElementById('lokasi_masuk').value = position.coords.latitude + "," + position.coords.longitude;
    document.getElementById('lokasi_keluar').value = position.coords.latitude + "," + position.coords.longitude;
  }

  function showError(error) {
    switch(error.code) {
      case error.PERMISSION_DENIED:
        alert("User denied the request for Geolocation.");
        break;
      case error.POSITION_UNAVAILABLE:
        alert("Location information is unavailable.");
        break;
      case error.TIMEOUT:
        alert("The request to get user location timed out.");
        break;
      case error.UNKNOWN_ERROR:
        alert("An unknown error occurred.");
        break;
    }
  }

  initClock();
  getLocation();
</script>

<?php include('../layouts/footer.php')?>