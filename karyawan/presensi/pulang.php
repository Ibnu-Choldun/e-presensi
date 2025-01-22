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

    const officeLat = -2.9902725; // Latitude kantor
    const officeLng = 104.7408598; // Longitude kantor

    let userLat, userLng;

    document.getElementById('take_foto_pulang').addEventListener('click', function () {
        if (!userLat || !userLng) {
            Swal.fire({
                title: 'Gagal',
                text: 'Lokasi Anda tidak terdeteksi.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        const distance = calculateDistance(userLat, userLng, officeLat, officeLng);

        if (distance > 10) {
            Swal.fire({
                title: 'Presensi Gagal',
                text: 'Anda berada lebih dari 10 meter dari lokasi kantor.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        Webcam.snap(function (data_uri) {
            const lokasi_pulang = userLat + ',' + userLng;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    document.getElementById('my_result').innerHTML = '<img src="' + data_uri + '"/>';
                    window.location.href = "../beranda/beranda.php";
                }
            };
            xhttp.open("POST", "pulang_aksi.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(
                "foto=" + encodeURIComponent(data_uri) +
                "&id=" + document.getElementById('id').value +
                "&tanggal_pulang=" + document.getElementById('tanggal_pulang').value +
                "&jam_pulang=" + document.getElementById('jam_pulang').value +
                "&lokasi_pulang=" + encodeURIComponent(lokasi_pulang)
            );
        });
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        userLat = position.coords.latitude;
        userLng = position.coords.longitude;
        document.getElementById('lokasi_pulang').value = userLat + ',' + userLng;

        var map = L.map('map').setView([userLat, userLng], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([userLat, userLng]).addTo(map)
            .bindPopup('<b>Lokasi Anda</b>')
            .openPopup();
    }

    function errorCallback(error) {
        Swal.fire({
            title: 'Gagal',
            text: 'Gagal mendapatkan lokasi: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radius Bumi dalam meter
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c; // Jarak dalam meter
    }
</script>

<?php include('../layouts/footer.php'); ?>
