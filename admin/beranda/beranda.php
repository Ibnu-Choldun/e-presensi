<?php 
session_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul= "Beranda";
include('../layouts/header.php');

//karyawan aktif
$karyawan = mysqli_query($connection, "SELECT karyawan.*, users.status FROM karyawan JOIN users ON karyawan.id = users.id_karyawan WHERE status = 'Aktif'");
$karyawan_aktif = mysqli_num_rows($karyawan);

// Mendapatkan tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

// Query untuk mendapatkan jumlah presensi hari ini
$query_presensi = "SELECT COUNT(*) as total_presensi FROM presensi WHERE tanggal_masuk = '$tanggal_hari_ini'";
$result_presensi = mysqli_query($connection, $query_presensi);
$data_presensi = mysqli_fetch_assoc($result_presensi);
$total_presensi = $data_presensi['total_presensi'];

// Query untuk mendapatkan jumlah absensi hari ini
$query_absensi = "SELECT COUNT(*) as total_absensi FROM absensi WHERE tanggal_mulai <= '$tanggal_hari_ini' AND tanggal_selesai >= '$tanggal_hari_ini'";
$result_absensi = mysqli_query($connection, $query_absensi);
$data_absensi = mysqli_fetch_assoc($result_absensi);
$total_absensi = $data_absensi['total_absensi'];
?>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            <div class="row row-deck row-cards">

              <div class="col-12">
                <div class="row row-cards">
                  <div class="col-sm-4 col-lg-4">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users-group"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg>
                            </span>
                          </div>

                          <div class="col">
                            <div class="font-weight-medium">
                              Karyawan Aktif
                            </div>
                            <div class="text-secondary">
                              <?= $karyawan_aktif ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-sm-4 col-lg-4">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-green text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint-scan"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 0 1 6 0c0 1.657 .612 3.082 1 4" /><path d="M12 11v1.75c-.001 1.11 .661 2.206 1 3.25" /><path d="M9 14.25c.068 .58 .358 1.186 .5 1.75" /><path d="M4 8v-2a2 2 0 0 1 2 -2h2" /><path d="M4 16v2a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v2" /><path d="M16 20h2a2 2 0 0 0 2 -2v-2" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              Jumlah Presensi
                            </div>
                            <div class="text-secondary">
                            <?= $total_presensi ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-4 col-lg-4">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-twitter text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" /><path d="M8 11c0 -.848 .264 -1.634 .713 -2.28m2.4 -1.621a4 4 0 0 1 4.887 3.901l0 1" /><path d="M12 12v1a14 14 0 0 0 2.5 8" /><path d="M8 15a18 18 0 0 0 1.8 6" /><path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 1.854 -5.143m2.176 -1.825a8 8 0 0 1 7.97 .018" /><path d="M3 3l18 18" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              Jumlah Absensi
                            </div>
                            <div class="text-secondary">
                            <?= $total_absensi ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="d-flex justify-content-center">
            <canvas id="dashboardChart" style="width: 100%; max-width: 1200px; height: 300px;"></canvas>
            <script>
              const ctx = document.getElementById('dashboardChart').getContext('2d');
              const dashboardChart = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: ['Karyawan Aktif', 'Presensi Hari Ini', 'Absensi Hari Ini'],
                  datasets: [{
                    label: 'Data Kepegawaian',
                    data: [
                      <?= $karyawan_aktif ?>, 
                      <?= $total_presensi ?>, 
                      <?= $total_absensi ?>
                    ],
                    backgroundColor: [
                      'rgba(75, 192, 192, 0.6)',
                      'rgba(54, 162, 235, 0.6)',
                      'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                      'rgba(75, 192, 192, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        stepSize: 1
                      }
                    }
                  },
                  layout: {
                    padding: 10
                  },
                  plugins: {
                    legend: {
                      display: true,
                      position: 'top'
                    }
                  }
                }
              });
            </script>
          </div>
        </div>



<?php include('../layouts/footer.php');?>
        