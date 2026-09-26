<?php

session_start();

require_once "../prompt/PromptBuilder.php";
require_once "../api/AiService.php";
require_once "../config/koneksi.php";

$data = array_merge(
    $_SESSION['step1'],
    $_SESSION['step2']
);

$builder = new PromptBuilder();
$prompt  = $builder->buildPrompt($data);

$ai = new AiService();
$hasil = $ai->generate($prompt);

$hasil_json = json_encode(
    $hasil,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

$userId = $_SESSION['id_user'];

$judul = $data['mapel'] . " - " . $data['topik'];

$sekolah = "SMP Negeri 9 Pariaman";

$fase = "Fase D";

$elemen = "-";

$stmt = mysqli_prepare(

    $koneksi,

    "INSERT INTO modul_ajar
    (
        user_id,
        penyusun,
        sekolah,
        tahun_pelajaran,
        semester,
        mata_pelajaran,
        kelas,
        fase,
        topik,
        elemen,
        alokasi_waktu,
        hasil_ai,
        judul_modul
    )

    VALUES
    (
        ?,?,?,?,?,?,?,?,?,?,?,?,?
    )"

);

mysqli_stmt_bind_param(

    $stmt,
    "issssssssssss",
    $userId,
    $data['penyusun'],
    $sekolah,
    $data['tahun'],
    $data['semester'],
    $data['mapel'],
    $data['kelas'],
    $fase,
    $data['topik'],
    $elemen,
    $data['alokasi'],
    $hasil_json,
    $judul
);

mysqli_stmt_execute($stmt);

$idModul = mysqli_insert_id($koneksi);

mysqli_stmt_close($stmt);

// Simpan hasil AI
$_SESSION['hasil_modul'] = $hasil;

// Simpan tanggal dari form
$_SESSION['tanggal_modul'] = $data['tanggal'];

header("Location: ../output/index.php?id=".$idModul);
exit;