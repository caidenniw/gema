<?php

session_start();

require_once "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    die("Silakan login.");
}

$id = (int)($_GET['id'] ?? 0);

$userId = $_SESSION['id_user'];

$query = mysqli_query(

    $koneksi,

    "SELECT
        judul_modul,
        hasil_ai
     FROM modul_ajar
     WHERE id='$id'
     AND user_id='$userId'
     LIMIT 1"

);

if(mysqli_num_rows($query)==0){

    die("Modul tidak ditemukan.");

}

$data = mysqli_fetch_assoc($query);

$modul = json_decode($data['hasil_ai'], true);

if (!$modul) {
    die("Data modul tidak valid.");
}

require_once "../vendor/autoload.php";
require_once "WordExporter.php";

use Export\WordExporter;

$exporter = new WordExporter();

$filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $data['judul_modul'] . ".docx";

$exporter->export($modul, $filename);

mysqli_query(
    $koneksi,
    "UPDATE modul_ajar
     SET download_word = download_word + 1
     WHERE id='$id'
     AND user_id='$userId'"
);

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $data['judul_modul'] . '.docx"');
header('Content-Length: ' . filesize($filename));

readfile($filename);

unlink($filename);
exit;