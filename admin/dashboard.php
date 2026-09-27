<?php

session_start();

if(!isset($_SESSION['login'])){

    header("location:../auth/login.php");
    exit;

}

if($_SESSION['role'] !== 'admin'){

    header("location:../dashboard/index.php");
    exit;
}
require_once "../config/koneksi.php";

$nama = $_SESSION['nama_lengkap'];

$sql = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role = 'guru'"
);

$dataGuru = mysqli_fetch_assoc($sql);
$totalGuru = $dataGuru['total'];

$sql = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM modul_ajar"
);

$dataModul = mysqli_fetch_assoc($sql);
$totalModul = $dataModul['total'];

$sql = mysqli_query(
    $koneksi,
    "SELECT
        IFNULL(SUM(download_word + download_pdf), 0)
        AS total_download
     FROM modul_ajar"
);

$dataDownload = mysqli_fetch_assoc($sql);
$totalDownload = $dataDownload['total_download'];

$sql = mysqli_query(
    $koneksi,
    "SELECT
        modul_ajar.judul_modul,
        modul_ajar.mata_pelajaran,
        modul_ajar.kelas,
        modul_ajar.fase,
        modul_ajar.created_at,
        users.nama_lengkap
     FROM modul_ajar

     LEFT JOIN users
     ON modul_ajar.user_id = users.id
     ORDER BY modul_ajar.created_at DESC
     LIMIT 5"
);

$modulTerbaru = [];
while($row = mysqli_fetch_assoc($sql)){

    $modulTerbaru[] = $row;

}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin | GEMA AI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet">


<!-- PAKAI CSS DASHBOARD YANG SAMA -->

<link
rel="stylesheet"
href="../assets/css/admin.css?v=1000">
<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m6">
</head>
<body>
<div class="gema-layout">

<!-- ==================================
     SIDEBAR ADMIN
================================== -->

<aside class="gema-sidebar">
<img
src="../assets/img/logo.png"
class="gema-logo">


<div class="gema-menu">
<a class="active">
<i class="bi bi-grid"></i>
Dashboard
</a>
<a href="guru.php">
<i class="bi bi-people"></i>
Kelola Guru
</a>
<a href="semua_modul.php">
<i class="bi bi-journal-text"></i>
Semua Modul
</a>
<a href="import_guru.php">
<i class="bi bi-file-earmark-arrow-up"></i>
Import Guru
</a>
<a href="statistik.php">
<i class="bi bi-bar-chart"></i>
Statistik
</a>
</div>
</aside>
<!-- ==================================
     MAIN CONTENT
================================== -->

<main class="gema-main">
<!-- ==================================
     TOPBAR
================================== -->

<div class="gema-topbar">

<div class="gema-user">
<div class="user-avatar">
<i class="bi bi-person-fill"></i>
</div>
<div class="user-info">
<b>
<?= htmlspecialchars($nama) ?>
</b>
<p>Administrator</p>
</div>
<div class="divider"></div>
<a
href="../auth/logout.php"
class="logout-btn"
>
<i class="bi bi-box-arrow-right"></i>
Keluar
</a>
</div>
</div>
<!-- ==================================
     BANNER ADMIN
================================== -->

<section class="gema-banner">
<div>
<span>
ADMIN GEMA AI
</span>
<h1>
Halo, <?= htmlspecialchars($nama) ?> 👋
</h1>
<p>
Kelola akun guru dan pantau penggunaan
GEMA AI di SMP Negeri 9 Pariaman.
</p>
</div>
<img
src="../assets/img/hero2.png"
>
</section>
<!-- ==================================
     STATISTIK
================================== -->

<section class="gema-stat">
<div class="stat-card">
<i class="bi bi-people"></i>
<h2>
<?= $totalGuru ?>
</h2>
<p>
Guru Terdaftar
</p>
</div>

<div class="stat-card">
<i class="bi bi-file-earmark-text"></i>
<h2>
<?= $totalModul ?>
</h2>
<p>Total Modul</p>
</div>

<div class="stat-card">
<i class="bi bi-download"></i>
<h2>

<?= $totalDownload ?>

</h2>
<p>

Total Download
</p>
</div>
</section>


<!-- ==================================
     MODUL TERBARU
================================== -->

<section class="gema-module">
<div class="module-title">
<h3>
Modul Terbaru
</h3>
<a href="semua_modul.php">

Lihat Semua →

</a>
</div>

<div class="module-list">

<?php if(count($modulTerbaru) > 0): ?>
<?php foreach($modulTerbaru as $modul): ?>
<div class="module-card">
<i class="bi bi-journal-bookmark"></i>
<h4>
<?= htmlspecialchars($modul['judul_modul']) ?>
</h4>
<p>
<?= htmlspecialchars($modul['nama_lengkap'] ?? 'Guru') ?>
<br>

<?= htmlspecialchars($modul['mata_pelajaran']) ?>

 ·

<?= htmlspecialchars($modul['kelas']) ?>

 / Fase

<?= htmlspecialchars($modul['fase']) ?>

</p>


</div>


<?php endforeach; ?>


<?php else: ?>


<div class="module-card">


<i class="bi bi-journal-bookmark"></i>


<h4>

Belum Ada Modul

</h4>


<p>

Belum ada guru yang membuat modul.

</p>


</div>


<?php endif; ?>


</div>


</section>



<!-- ==================================
     FOOTER
================================== -->

<footer class="gema-footer">


<p>

© 2026 GEMA AI - Generator Modul Ajar AI

</p>


<span>

SMP Negeri 9 Pariaman

</span>


</footer>


</main>


</div>


<script src="../assets/js/mobile.js"></script>
</body>

</html>