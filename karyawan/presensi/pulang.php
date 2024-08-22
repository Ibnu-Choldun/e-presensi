<!--webcam.js-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!--leaflet.js-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
crossorigin=""></script>

<style>
    #map 
    {
        height: 300px;
    }
</style>

<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
  exit();
} else if($_SESSION['peran'] != 'Karyawan') {
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
  exit();
}

$judul = "Presensi Pulang";
include('../layouts/header.php');
include_once("../../config.php");

$tanggal_pulang = date('Y-m-d');
$jam_pulang = date('H:i:s');
$lokasi_pulang = '';
?>

<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-body" style="margin: auto;">

                        <input type="hidden" id="id" value="<?= $_SESSION['id'] ?>">
                        <input type="hidden" id="tanggal_pulang" value="<?= $tanggal_pulang ?>">
                        <input type="hidden" id="jam_pulang" value="<?= $jam_pulang ?>">
                        <input type="hidden" id="lokasi_pulang" value="<?= $lokasi_pulang ?>">

                        <div id="my_camera" style="width:320px; height:280px;"></div>
                        <div id="my_result"></div>
                        <button class="form-control btn btn-danger mt-2" id="take_foto_pulang">Pulang</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="JavaScript">
    Webcam.set({
        width: 320,
        height: 240,
        dest_width: 320,
        dest_height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
    document.getElementById('take_foto_pulang').addEventListener('click', function(){
        Webcam.snap(function(data_uri) {
            let id = document.getElementById('id').value;
            let tanggal_pulang = document.getElementById('tanggal_pulang').value;
            let jam_pulang = document.getElementById('jam_pulang').value;
            let lokasi_pulang = document.getElementById('lokasi_pulang').value;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                document.getElementById('my_result').innerHTML = '<img src="'+data_uri+'"/>';
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    window.location.href = "../beranda/beranda.php";
                }
            };
            xhttp.open("POST", "pulang_aksi.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(
                "foto=" + encodeURIComponent(data_uri) +
                "&id=" + id +
                "&tanggal_pulang=" + tanggal_pulang +
                "&jam_pulang=" + jam_pulang +
                "&lokasi_pulang=" + encodeURIComponent(lokasi_pulang)
            );
        });
    });
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;
        document.getElementById('lokasi_pulang').value = latitude + "," + longitude;

        var map = L.map('map').setView([latitude, longitude], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([latitude, longitude]).addTo(map);
    }

    function errorCallback(error) {
        alert('Gagal mendapatkan lokasi: ' + error.message);
    }
</script>

<?php include('../layouts/footer.php'); ?>
