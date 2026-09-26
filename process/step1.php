<?php

session_start();

$_SESSION['step1'] = [

    'penyusun' => trim($_POST['penyusun'] ?? ''),
    'nip'      => trim($_POST['nip'] ?? ''),
    'tahun'    => $_POST['tahun'] ?? '',
    'semester' => $_POST['semester'] ?? '',
    'mapel'    => trim($_POST['mapel'] ?? ''),
    'kelas'    => $_POST['kelas'] ?? '',
    'topik'    => trim($_POST['topik'] ?? ''),
    'alokasi'  => $_POST['alokasi'] ?? '',
    'tanggal'  => $_POST['tanggal'] ?? date('Y-m-d'),

];

header("Location: ../generator/index.php?step=2");

exit;