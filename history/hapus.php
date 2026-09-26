<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/koneksi.php";

$id = (int)$_GET['id'];
$userId = $_SESSION['id_user'];

mysqli_query(
    $koneksi,
    "DELETE FROM modul_ajar
     WHERE id='$id'
     AND user_id='$userId'"
);

header("Location: index.php");
exit;