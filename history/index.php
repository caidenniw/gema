<?php

session_start();

if(!isset($_SESSION['login'])){

    header("location:../auth/login.php");
    exit;

}

require_once "../config/koneksi.php";
$nama = $_SESSION['nama_lengkap'];

$userId = $_SESSION['id_user'];

$query = mysqli_query(

    $koneksi,

    "SELECT

        id,
        judul_modul,
        mata_pelajaran,
        kelas,
        created_at

    FROM modul_ajar

    WHERE user_id='$userId'

    ORDER BY created_at DESC"

);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Modul</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/history.css">

<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m5">
</head>

<body>

<div class="gema-layout">

<!-- SIDEBAR -->

<aside class="gema-sidebar">

<img 
src="../assets/img/logo.png"
class="gema-logo">

<div class="gema-menu">

<a href="../dashboard/index.php">

    <i class="bi bi-house"></i>

    Beranda

</a>

<a href="../generator/index.php">

    <i class="bi bi-plus-circle"></i>

    Buat Modul

</a>

<a class="active">

    <i class="bi bi-journal-text"></i>

    Riwayat Modul

</a>

</div>

</aside>

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

<div class="history-header">

    <div>

        <h2>Riwayat Modul</h2>

        <p>
            Kelola seluruh modul ajar yang telah dibuat.
        </p>

    </div>

</div>

<div class="history-card">

<table class="history-table">

<thead>

<tr>

<th>No</th>

<th>Judul Modul</th>

<th>Mapel</th>

<th>Kelas</th>

<th>Tanggal</th>

<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

while($row=mysqli_fetch_assoc($query)):

?>

<tr>

<td data-label="No"><?= $no++ ?></td>

<td data-label="Judul Modul"><?= htmlspecialchars($row['judul_modul']) ?></td>

<td data-label="Mapel"><?= htmlspecialchars($row['mata_pelajaran']) ?></td>

<td data-label="Kelas"><?= htmlspecialchars($row['kelas']) ?></td>

<td data-label="Tanggal">

<?= date("d M Y",strtotime($row['created_at'])) ?>

</td>

<td data-label="Aksi">

    <div class="action-buttons">

        <a href="../output/index.php?id=<?= $row['id'] ?>" class="btn-action btn-view">
            <i class="bi bi-eye"></i>
            Lihat
        </a>

        <a href="../export/word.php?id=<?= $row['id'] ?>" class="btn-action btn-word">
            <i class="bi bi-file-earmark-word"></i>
            Word
        </a>

        <a href="../export/pdf.php?id=<?= $row['id'] ?>" class="btn-action btn-pdf">
            <i class="bi bi-file-earmark-pdf"></i>
            PDF
        </a>

        <a href="hapus.php?id=<?= $row['id'] ?>"
           class="btn-action btn-delete"
           onclick="return confirm('Yakin ingin menghapus modul ini?')">
            <i class="bi bi-trash"></i>
            Hapus
        </a>

    </div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</main>

</div>

<script src="../assets/js/mobile.js"></script>
</body>

</html>

