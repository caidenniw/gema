<?php

session_start();

require_once "../config/koneksi.php";

header("Content-Type: application/json");


// ======================================
// CEK LOGIN
// ======================================

if (!isset($_SESSION['id_user'])) {

    echo json_encode([
        "success" => false,
        "message" => "Anda belum login."
    ]);

    exit;
}


// ======================================
// CEK DATA
// ======================================

$id   = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$html = $_POST['html'] ?? '';

if ($id <= 0 || $html === '') {

    echo json_encode([
        "success" => false,
        "message" => "Data modul tidak lengkap."
    ]);

    exit;
}


// ======================================
// USER LOGIN
// ======================================

$userId = (int) $_SESSION['id_user'];
$role   = $_SESSION['role'] ?? 'guru';


// ======================================
// SIMPAN
// ======================================

$html = mysqli_real_escape_string($koneksi, $html);


// ======================================
// ADMIN
// ======================================

if ($role === 'admin') {

    $query = mysqli_query(
        $koneksi,
        "UPDATE modul_ajar
         SET html_modul='$html'
         WHERE id='$id'"
    );


// ======================================
// GURU
// ======================================

} else {

    $query = mysqli_query(
        $koneksi,
        "UPDATE modul_ajar
         SET html_modul='$html'
         WHERE id='$id'
         AND user_id='$userId'"
    );
}


// ======================================
// HASIL
// ======================================

if ($query) {

    echo json_encode([
        "success" => true,
        "message" => "Modul berhasil disimpan."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($koneksi)
    ]);
}