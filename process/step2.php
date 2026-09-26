<?php

session_start();

$_SESSION['step2'] = [

    'cp' => $_POST['cp'],

    'tp' => $_POST['tp'],

    'identifikasi' => $_POST['identifikasi'],

    'profil' => $_POST['profil'] ?? [],

    'praktik' => $_POST['praktik'],

    'metode' => $_POST['metode'] ?? [],

    'lingkungan' => $_POST['lingkungan'],

    'digital' => $_POST['digital'] ?? [],

    'kemitraan' => $_POST['kemitraan']

];

header("Location: ../generator/index.php?step=3");

exit;