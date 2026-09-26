<?php


$host = "localhost";
$user = "root";
$password = "";
$database = "db_gema_ai";


$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);


// Mengecek koneksi database
if (!$koneksi) {

    die(
        "Koneksi database gagal : "
        . mysqli_connect_error()
    );

}


?>