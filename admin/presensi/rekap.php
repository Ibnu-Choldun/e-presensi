<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["peran"] != 'Admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Rekap Presensi";
include('../layouts/header.php');
require_once('../../config.php');

$error_message = "";

$karyawan = isset($_GET['karyawan']) ? $_GET['karyawan'] : '';

if(empty($_GET['dari_tanggal']) && empty($_GET['bulan']) && empty($_GET['tahun'])) {
    $dari_tanggal = date('Y-m-d');
    $result = mysqli_query($connection, 
    "SELECT presensi.*, karyawan.nama 
    FROM presensi 
    JOIN karyawan 
    ON presensi.id_karyawan = karyawan.id 
    WHERE karyawan.nama LIKE '%$karyawan%'
    ORDER BY tanggal_masuk DESC");
    $display_date = "Rekap Tanggal: " . date('d F Y');
} elseif (!empty($_GET['bulan']) && empty($_GET['tahun'])) {
    $error_message = "Tahun harus dipilih jika Anda memilih bulan.";
    $result = null;
} elseif (!empty($_GET['bulan']) && !empty($_GET['tahun'])) {
    $bulan = $_GET['bulan'];
    $tahun = $_GET['tahun'];
    $result = mysqli_query($connection, 
    "SELECT presensi.*, karyawan.nama 
    FROM presensi 
    JOIN karyawan 
    ON presensi.id_karyawan = karyawan.id 
    WHERE MONTH(tanggal_masuk) = '$bulan' 
    AND YEAR(tanggal_masuk) = '$tahun'
    AND karyawan.nama LIKE '%$karyawan%'
    ORDER BY tanggal_masuk DESC");
    $display_date = "Rekap Bulan: " . date('F', mktime(0, 0, 0, $bulan, 10)) . " " . $tahun;
} elseif (!empty($_GET['tahun']) && empty($_GET['bulan'])) {
    $tahun = $_GET['tahun'];
    $result = mysqli_query($connection, 
    "SELECT presensi.*, karyawan.nama 
    FROM presensi 
    JOIN karyawan 
    ON presensi.id_karyawan = karyawan.id 
    WHERE YEAR(tanggal_masuk) = '$tahun'
    AND karyawan.nama LIKE '%$karyawan%'
    ORDER BY tanggal_masuk DESC");
    $display_date = "Rekap Tahun: " . $tahun;
} elseif (!empty($_GET['sampai_tanggal']) && empty($_GET['dari_tanggal'])) {
    $sampai_tanggal = $_GET['sampai_tanggal'];
    $dari_tanggal_query = mysqli_query($connection, 
    "SELECT MIN(tanggal_masuk) AS dari_tanggal 
    FROM presensi");
    $dari_tanggal_result = mysqli_fetch_assoc($dari_tanggal_query);
    $dari_tanggal = $dari_tanggal_result['dari_tanggal'];

    if (!$dari_tanggal) {
        $error_message = "Data presensi tidak ditemukan.";
        $result = null;
    } else {
        $result = mysqli_query($connection, 
        "SELECT presensi.*, karyawan.nama
        FROM presensi
        JOIN karyawan 
        ON presensi.id_karyawan = karyawan.id 
        WHERE tanggal_masuk 
        BETWEEN '$dari_tanggal' AND '$sampai_tanggal' 
        AND karyawan.nama LIKE '%$karyawan%'
        ORDER BY tanggal_masuk DESC");
        $display_date = "Rekap Tanggal: " . date('d F Y', strtotime($dari_tanggal)) . " sampai " . date('d F Y', strtotime($sampai_tanggal));
    }
} elseif (!empty($_GET['dari_tanggal']) && empty($_GET['sampai_tanggal'])) {
    $error_message = "Sampai tanggal presensi harus dipilih.";
    $result = null;
} else {
    $dari_tanggal = $_GET['dari_tanggal'];
    $sampai_tanggal = $_GET['sampai_tanggal'];
    $result = mysqli_query($connection, 
    "SELECT presensi.*, karyawan.nama
    FROM presensi
    JOIN karyawan 
    ON presensi.id_karyawan = karyawan.id 
    WHERE tanggal_masuk 
    BETWEEN '$dari_tanggal' AND '$sampai_tanggal' 
    AND karyawan.nama LIKE '%$karyawan%'
    ORDER BY tanggal_masuk DESC");
    $display_date = "Rekap Tanggal: " . date('d F Y', strtotime($_GET['dari_tanggal'])) . " sampai " . date('d F Y', strtotime($_GET['sampai_tanggal']));
}
?>

<div class="page-body">
    <div class="container-xl">

    <form method="GET" action="export_excel.php">
        <input type="hidden" name="karyawan" value="<?= $karyawan ?>">
        <input type="hidden" name="dari_tanggal" value="<?= isset($_GET['dari_tanggal']) ? $_GET['dari_tanggal'] : '' ?>">
        <input type="hidden" name="sampai_tanggal" value="<?= isset($_GET['sampai_tanggal']) ? $_GET['sampai_tanggal'] : '' ?>">
        <input type="hidden" name="bulan" value="<?= isset($_GET['bulan']) ? $_GET['bulan'] : '' ?>">
        <input type="hidden" name="tahun" value="<?= isset($_GET['tahun']) ? $_GET['tahun'] : '' ?>">
        <button type="submit" name="export" value="excel" class="btn btn-success mt-2">Export Excel</button>
    </form>

        <!-- Form untuk Pilihan Karyawan -->
        <div class="col-12 mt-2">
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="karyawan" class="form-control" placeholder="Nama Karyawan" value="<?= $karyawan ?>">
                    <button type="submit" class="btn btn-info">Tampilkan</button>
                </div>
            </form>
        </div>

        <!-- Form untuk Tanggal Harian -->
        <div class="col-12 mt-2">
            <form method="GET">
                <div class="input-group">
                    <input type="date" name="dari_tanggal" class="form-control">
                    <input type="date" name="sampai_tanggal" class="form-control">
                    <input type="hidden" name="karyawan" value="<?= $karyawan ?>">
                    <button type="submit" class="btn btn-info">Tampilkan Harian</button>
                </div>
            </form>
        </div>

        <!-- Form untuk Pilihan Bulan dan Tahun -->
        <div class="col-12 mt-2">
            <form method="GET">
                <div class="input-group">
                    <select name="bulan" class="form-control">
                        <option value="">-- Pilih Bulan --</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 10)) ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="tahun" class="form-control">
                        <option value="">-- Pilih Tahun --</option>
                        <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <input type="hidden" name="karyawan" value="<?= $karyawan ?>">
                    <button type="submit" class="btn btn-info">Tampilkan Bulanan</button>
                </div>
            </form>
        </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger mt-2"><?= $error_message ?></div>
    <?php else: ?>
        <span><?= $display_date ?></span>
    <?php endif; ?>

    <table class="table table-bordered mt-2 text-center">
        <tr>
            <th>NO</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Total Jam Kerja</th>
            <th>Total Jam Terlambat</th>
        </tr>

        <?php if($result && mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="7">Tidak ada data</td>
            </tr>
        <?php } elseif ($result) { ?>
            <?php 
            $no = 1;
            $batas_waktu_terlambat = "08:00:00";

            while($rekap = mysqli_fetch_array($result)) :

                $jam_tgl_msk = date('Y-m-d H:i:s', strtotime($rekap['tanggal_masuk']. ' ' . $rekap['jam_masuk']));
                $jam_tgl_plg = date('Y-m-d H:i:s', strtotime($rekap['tanggal_pulang']. ' ' . $rekap['jam_pulang']));

                $timestamp_msk = strtotime($jam_tgl_msk);
                $timestamp_plg = strtotime($jam_tgl_plg);

                $selisih = $timestamp_plg - $timestamp_msk;

                $jam_kerja = floor($selisih / 3600);
                $selisih -= $jam_kerja * 3600;
                $selisih_menit = floor($selisih / 60);

                $timestamp_batas_terlambat = strtotime($rekap['tanggal_masuk'] . ' ' . $batas_waktu_terlambat);
                $jam_terlambat = 0;
                $menit_terlambat = 0;

                if ($timestamp_msk > $timestamp_batas_terlambat) {
                    $selisih_terlambat = $timestamp_msk - $timestamp_batas_terlambat;

                    $jam_terlambat = floor($selisih_terlambat / 3600);
                    $selisih_terlambat -= $jam_terlambat * 3600;
                    $menit_terlambat = floor($selisih_terlambat / 60);
                }
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $rekap['nama'] ?></td>
                <td><?= date('d F Y', strtotime($rekap['tanggal_masuk'])) ?></td>
                <td class="text-center"><?= $rekap['jam_masuk'] ?></td>
                <td class="text-center"><?= $rekap['jam_pulang'] ?></td>
                <td class="text-center">
                    <?php if ($rekap['tanggal_pulang'] == '0000-00-00') : ?>
                        <span>0 Jam 0 Menit</span>
                    <?php else : ?>
                        <?= $jam_kerja . ' Jam ' . $selisih_menit . ' Menit' ?>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?= $jam_terlambat . ' Jam ' . $menit_terlambat . ' Menit' ?>
                </td>
            </tr>

            <?php endwhile; ?>
        <?php } ?>

    </table>
</div>
</div>

<?php include('../layouts/footer.php')?>
