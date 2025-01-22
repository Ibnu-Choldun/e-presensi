<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
ob_start();
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION['peran'] != 'Karyawan'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Presensi Masuk";
include('../layouts/header.php');
include_once("../../config.php");

if(isset($_POST['button_masuk']))
{
    $lokasi_masuk = $_POST['lokasi_masuk'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jam_masuk = $_POST['jam_masuk'];
}

?>

<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-body" style="margin: auto;">

                        <input type="hidden" id="id" value="<?= $_SESSION['id'] ?>">
                        <input type="hidden" id="tanggal_masuk" value="<?= $tanggal_masuk ?>">
                        <input type="hidden" id="jam_masuk" value="<?= $jam_masuk ?>">
                        <input type="hidden" id="lokasi_masuk" value="<?= $lokasi_masuk ?>">

                        <div id="my_camera" style="width:320px; height:280px;"></div>
                        <div id="my_result"></div>
                        <button class="form-control btn btn-info mt-2" id="take_foto">Masuk</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div id="map">

                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="JavaScript">
    // Initialize the webcam
    Webcam.set({
        width: 320,
        height: 240,
        dest_width: 320,
        dest_height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');

    let id = document.getElementById('id').value;
    let tanggal_masuk = document.getElementById('tanggal_masuk').value;
    let jam_masuk = document.getElementById('jam_masuk').value;
    let lokasi_masuk = document.getElementById('lokasi_masuk').value;

    // Office coordinates
    const officeLat = -2.9902725;
    const officeLng = 104.7408598;

    let userLat, userLng;

    document.getElementById('take_foto').addEventListener('click', function() {
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

        if (distance > 10000) {
            Swal.fire({
                title: 'Presensi Gagal',
                text: 'Anda berada lebih dari 10 meter dari lokasi kantor.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        Webcam.snap(function(data_uri) {
            const lokasi_masuk = userLat + ',' + userLng;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    document.getElementById('my_result').innerHTML = '<img src="' + data_uri + '"/>';
                    window.location.href = "../beranda/beranda.php";
                }
            };
            xhttp.open("POST", "masuk_aksi.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(
                "foto=" + encodeURIComponent(data_uri) +
                "&id=" + id +
                "&tanggal_masuk=" + tanggal_masuk +
                "&jam_masuk=" + jam_masuk +
                "&lokasi_masuk=" + encodeURIComponent(lokasi_masuk)
            );
        });
    });

    // Map and location handling
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        userLat = position.coords.latitude;
        userLng = position.coords.longitude;

        // Display map with user's current location
        var map = L.map('map').setView([userLat, userLng], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Add a marker for the user's current location
        L.marker([userLat, userLng]).addTo(map)
            .bindPopup("<b>Anda di sini</b>")
            .openPopup();
    }

    function errorCallback() {
        Swal.fire({
            title: 'Gagal',
            text: 'Gagal mendapatkan lokasi Anda.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth's radius in meters
        const φ1 = lat1 * Math.PI / 180; // Convert to radians
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        const distance = R * c; // Distance in meters
        return distance;
    }
</script>

<?php include('../layouts/footer.php');?>
