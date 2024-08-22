<?php
session_start();
require_once('../../config.php');

if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=rekap_presensi.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");

    // Header tabel
    fputcsv($output, ['NO', 'Nama', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Total Jam Kerja', 'Total Jam Terlambat'], "\t");

    // Retrieve the same data as in the main script
    $karyawan = isset($_GET['karyawan']) ? $_GET['karyawan'] : '';

    if(empty($_GET['dari_tanggal']) && empty($_GET['bulan']) && empty($_GET['tahun'])) {
        $result = mysqli_query($connection, 
        "SELECT presensi.*, karyawan.nama 
        FROM presensi 
        JOIN karyawan 
        ON presensi.id_karyawan = karyawan.id 
        WHERE karyawan.nama LIKE '%$karyawan%'
        ORDER BY tanggal_masuk DESC");
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
    } elseif (!empty($_GET['sampai_tanggal']) && empty($_GET['dari_tanggal'])) {
        $sampai_tanggal = $_GET['sampai_tanggal'];
        $dari_tanggal_query = mysqli_query($connection, 
        "SELECT MIN(tanggal_masuk) AS dari_tanggal 
        FROM presensi");
        $dari_tanggal_result = mysqli_fetch_assoc($dari_tanggal_query);
        $dari_tanggal = $dari_tanggal_result['dari_tanggal'];

        if (!$dari_tanggal) {
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
        }
    } elseif (!empty($_GET['dari_tanggal']) && empty($_GET['sampai_tanggal'])) {
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
    }

    // Menambahkan data ke file Excel
    if($result) {
        $no = 1;
        $batas_waktu_terlambat = "09:00:00";

        while($rekap = mysqli_fetch_array($result)) {
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

            // Menulis data ke file excel
            fputcsv($output, [
                $no++, 
                $rekap['nama'], 
                date('d F Y', strtotime($rekap['tanggal_masuk'])),
                $rekap['jam_masuk'], 
                $rekap['jam_pulang'], 
                ($rekap['tanggal_pulang'] == '0000-00-00') ? '0 Jam 0 Menit' : $jam_kerja . ' Jam ' . $selisih_menit . ' Menit',
                $jam_terlambat . ' Jam ' . $menit_terlambat . ' Menit'
            ], "\t");
        }
    }

    fclose($output);
    exit();
}
?>
