<?php

session_start();

$_SESSION['step1'] = [

    'penyusun' => $_POST['penyusun'],
    'nip' => $_POST['nip'],
    'tahun' => $_POST['tahun'],
    'semester' => $_POST['semester'],
    'mapel' => $_POST['mapel'],
    'kelas' => $_POST['kelas'],
    'topik' => $_POST['topik'],
    'alokasi' => $_POST['alokasi'],
    'tanggal' => $_POST['tanggal']

];

header("Location: ../generator/index.php?step=2");

exit;