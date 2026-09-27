<?php

session_start();
if(!isset($_SESSION['login'])){
    header("location:../auth/login.php");
    exit;
}

if($_SESSION['role'] !== 'guru'){
    header("location:../admin/dashboard.php");
    exit;
}

$nama = $_SESSION['nama_lengkap'];
require_once "../config/koneksi.php";
$userId = $_SESSION['id_user'];

/* ===========================
   TOTAL MODUL
=========================== */

$sql = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM modul_ajar
     WHERE user_id = '$userId'"
);

$totalModul = mysqli_fetch_assoc($sql);
$totalModul = $totalModul['total'];

/* ===========================
   TOTAL DOWNLOAD
=========================== */

$sql = mysqli_query(
    $koneksi,
    "SELECT
    IFNULL(SUM(download_word + download_pdf),0)
    AS total_download
    FROM modul_ajar
    WHERE user_id='$userId'"
);

$totalDownload = mysqli_fetch_assoc($sql);
$totalDownload = $totalDownload['total_download'];

/* ===========================
   GENERATE TERAKHIR
=========================== */

$sql = mysqli_query(
    $koneksi,
    "SELECT created_at
    FROM modul_ajar
    WHERE user_id='$userId'
    ORDER BY created_at DESC
    LIMIT 1"
);

$lastGenerate = mysqli_fetch_assoc($sql);
if($lastGenerate){
    $lastGenerate = date(
        "d M Y",
        strtotime($lastGenerate['created_at'])
    );
}else{
    $lastGenerate = "-";
}

/* ===========================
   MODUL TERAKHIR
=========================== */

$sql = mysqli_query(
    $koneksi,
    "SELECT
        judul_modul,
        mata_pelajaran,
        kelas,
        fase,
        created_at
    FROM modul_ajar
    WHERE user_id='$userId'
    ORDER BY created_at DESC
    LIMIT 3"
);

$modulTerakhir = [];
while($row = mysqli_fetch_assoc($sql)){
    $modulTerakhir[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
Dashboard GEMA AI
</title>
<link 
href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css?v=999">
<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m6">
</head>
<body>

<div class="gema-layout">

<!-- SIDEBAR -->

<aside class="gema-sidebar">

<img 
src="../assets/img/logo.png"
class="gema-logo">

<div class="gema-menu">

<a class="active">

<i class="bi bi-house"></i>

Beranda

</a>

<a href="../generator/index.php">

<i class="bi bi-plus-circle"></i>

Buat Modul

</a>

<a href="../history/index.php">

<i class="bi bi-journal-text"></i>

Riwayat Modul

</a>

</div>

</aside>

<!-- CONTENT -->

<main class="gema-main">

<!-- TOPBAR -->

<div class="gema-topbar">

    <div class="gema-user">

        <div class="user-avatar">

            <i class="bi bi-person-fill"></i>

        </div>

        <div class="user-info">

            <b><?= $nama ?></b>

            <p>SMP Negeri 9 Pariaman</p>

        </div>

        <div class="divider"></div>

        <a href="../auth/logout.php" class="logout-btn">

            <i class="bi bi-box-arrow-right"></i>

            Keluar

        </a>

    </div>

</div>

<!-- BANNER -->

<section class="gema-banner">

<div>

<span>

AI Modul Generator

</span>

<h1>Halo, <?= $nama ?> 👋</h1>

<p>Buat modul ajar Deep Learning berbasis Artificial Intelligence
sesuai Kurikulum Merdeka.</p>

<a href="../generator/index.php" class="btn-generator">

<i class="bi bi-plus"></i>Buat Modul Baru

</a>

</div>

<img src="../assets/img/hero2.png">

</section>

<!-- STATISTIK -->

<section class="gema-stat">

<div class="stat-card">

<i class="bi bi-file-earmark-text"></i>

<h2><?= $totalModul ?></h2>

<p>Modul Dibuat</p>

</div>


<div class="stat-card">

<i class="bi bi-download"></i>

<h2><?= $totalDownload ?></h2>

<p>Total Download</p>

</div>

<div class="stat-card">

<i class="bi bi-clock"></i>

<h2><?= $lastGenerate ?></h2>

<p>Generate Terakhir</p>

</div>

</section>

<!-- MODUL TERAKHIR -->

<section class="gema-module">

<div class="module-title">

<h3>Modul Terakhir</h3>

<a href="../history/index.php">
    Lihat Semua →
</a>

</div>

<div class="module-list">

<?php if(count($modulTerakhir) > 0): ?>

    <?php foreach($modulTerakhir as $modul): ?>

        <div class="module-card">

            <i class="bi bi-journal-bookmark"></i>

            <h4>

                <?= htmlspecialchars($modul['judul_modul']) ?>

            </h4>

            <p>

                <?= htmlspecialchars($modul['kelas']) ?>
 
            </p>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="module-card">

        <i class="bi bi-journal-bookmark"></i>

        <h4>

            Belum Ada Modul

        </h4>

        <p>Silakan buat modul pertama.</p>
    </div>
<?php endif; ?>
</div>

<!-- FOOTER -->
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
