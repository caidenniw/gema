<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    die("Silakan login terlebih dahulu.");
}

require_once "../vendor/autoload.php";
require_once "../config/koneksi.php";
require_once "PdfExporter.php";

use Export\PdfExporter;

$id = (int) ($_GET['id'] ?? 0);
$userId = (int) $_SESSION['id_user'];
$role = $_SESSION['role'] ?? 'guru';
$isAdmin = ($role === 'admin');

if ($isAdmin) {
    $query = mysqli_query($koneksi, "SELECT hasil_ai FROM modul_ajar WHERE id='$id' LIMIT 1");
} else {
    $query = mysqli_query($koneksi, "SELECT hasil_ai FROM modul_ajar WHERE id='$id' AND user_id='$userId' LIMIT 1");
}

if (!$query || mysqli_num_rows($query) == 0) {
    die("Modul tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);
$modul = json_decode($data['hasil_ai'], true);

$pdf = new PdfExporter();
$temp = sys_get_temp_dir() . "/modul_" . time() . ".pdf";
$pdf->export($modul, $temp);

if ($isAdmin) {
    mysqli_query($koneksi, "UPDATE modul_ajar SET download_pdf = download_pdf + 1 WHERE id='$id'");
} else {
    mysqli_query($koneksi, "UPDATE modul_ajar SET download_pdf = download_pdf + 1 WHERE id='$id' AND user_id='$userId'");
}

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=Modul_Ajar.pdf");
readfile($temp);
unlink($temp);
exit;
