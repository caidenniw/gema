<?php
session_start();
require_once "../config/koneksi.php";
/* =========================================
   CEK LOGIN
========================================= */

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* =========================================
   CEK ROLE ADMIN
========================================= */

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

/* =========================================
   AMBIL ID GURU
========================================= */

$guruId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* =========================================
   VALIDASI ID
========================================= */

if ($guruId <= 0) {
    header("Location: guru.php");
    exit;
}

/* =========================================
   AMBIL DATA GURU
========================================= */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT 
        id,
        nama_lengkap,
        nip,
        email,
        created_at
     FROM users
     WHERE id = ?
     AND role = 'guru'
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $guruId
);

mysqli_stmt_execute($stmt);
$resultGuru = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($resultGuru);

/* =========================================
   JIKA GURU TIDAK DITEMUKAN
========================================= */

if (!$guru) {
    echo "
    <script>
        alert('Data guru tidak ditemukan.');
        window.location='guru.php';
    </script>
    ";
    exit;
}

/* =========================================
   DATA GURU
========================================= */

$namaGuru = $guru['nama_lengkap'];
$nipGuru  = $guru['nip'] ?? '-';
$emailGuru = $guru['email'] ?? '-';

/* =========================================
   FORMAT TANGGAL BERGABUNG
========================================= */

if (!empty($guru['created_at'])) {
    $tanggalGabung = date(
        "d M Y",
        strtotime($guru['created_at'])
    );
} else {
    $tanggalGabung = "-";
}

/* =========================================
   AMBIL MODUL MILIK GURU
========================================= */

$stmtModul = mysqli_prepare(
    $koneksi,
    "SELECT
        id,
        judul_modul,
        mata_pelajaran,
        kelas,
        fase,
        created_at
     FROM modul_ajar
     WHERE user_id = ?
     ORDER BY created_at DESC"
);
mysqli_stmt_bind_param(
    $stmtModul,
    "i",
    $guruId
);
mysqli_stmt_execute($stmtModul);
$resultModul = mysqli_stmt_get_result($stmtModul);
/* =========================================
   HITUNG TOTAL MODUL
========================================= */
$totalModul = mysqli_num_rows($resultModul);
/* =========================================
   SESSION ADMIN
========================================= */
$namaAdmin = $_SESSION['nama_lengkap'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
    Detail Guru | GEMA AI
</title>

<!-- BOOTSTRAP ICON -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">
<link
    rel="stylesheet"
    href="../assets/css/admin.css?v=1"
>
<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m5">
</head>
<body>
<div class="gema-layout">

<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="gema-sidebar">
    <!-- LOGO -->
    <img
        src="../assets/img/logo.png"
        class="gema-logo"
        alt="GEMA AI">
    <!-- MENU -->
    <div class="gema-menu">
        <!-- DASHBOARD -->
        <a href="dashboard.php">
            <i class="bi bi-grid"></i>
            <span>
                Dashboard
            </span>
        </a>

        <!-- KELOLA GURU -->

        <a href="guru.php" class="active">
            <i class="bi bi-people"></i>
            <span>
                Kelola Guru
            </span>
        </a>
        <!-- SEMUA MODUL -->

        <a href="semua_modul.php">
            <i class="bi bi-journal-text"></i>
            <span>
                Semua Modul
            </span>
        </a>

        <!-- IMPORT GURU -->

        <a href="import_guru.php">
            <i class="bi bi-file-earmark-arrow-up"></i>
            <span>
                Import Guru
            </span>
        </a>

        <!-- STATISTIK -->

        <a href="statistik.php">
            <i class="bi bi-bar-chart"></i>
            <span>
                Statistik
            </span>
        </a>
    </div>
</aside>

<!-- =====================================================
     MAIN
===================================================== -->

<main class="gema-main">

<!-- =====================================================
     TOPBAR
===================================================== -->

<div class="gema-topbar">
    <div class="gema-user">
        <div class="user-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="user-info">
            <b>
                <?= htmlspecialchars($namaAdmin) ?>
            </b>
            <p>
                Administrator
            </p>
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

<!-- =====================================================
     CONTENT
===================================================== -->

<div class="admin-content">

    <!-- KEMBALI -->

<a href="guru.php" class="admin-back">
    <i class="bi bi-arrow-left"></i>
    Kembali ke Kelola Guru
</a>

    <!-- JUDUL -->

    <div class="page-heading">
        <h1>
            Detail Guru
        </h1>
        <p>
            Informasi akun dan modul yang dibuat oleh guru.
        </p>
    </div>

    <!-- =================================================
         INFORMASI GURU
    ================================================= -->

    <section class="detail-guru-card">

        <!-- HEADER GURU -->

        <div class="detail-guru-header">
<div class="detail-guru-avatar">
<i class="bi bi-person-fill"></i>
            </div>
<div class="detail-guru-info">
    <h2>
        <?= htmlspecialchars($namaGuru) ?>
    </h2>
    <p>
        Guru · SMP Negeri 9 Pariaman
    </p>
</div>
        </div>

        <!-- DATA GURU -->
<div class="detail-guru-data">
            <div>
                <strong>
                    NIP
                </strong>
                <p>
                    <?= htmlspecialchars($nipGuru) ?>
                </p>
            </div>
            <div>
                <strong>
                    Email
                </strong>
                <p>
                    <?= htmlspecialchars($emailGuru) ?>
                </p>
            </div>
            <div>
                <strong>
                    Bergabung
                </strong>
                <p>
                    <?= htmlspecialchars($tanggalGabung) ?>
                </p>
            </div>


        </div>


    </section>


    <!-- =================================================
         TOTAL MODUL
    ================================================= -->

    <section class="total-modul">

        <i class="bi bi-journal-text"></i>

        <div>
            <h2>
                <?= $totalModul ?>
            </h2>

            <p>
                Total Modul Dibuat
            </p>
        </div>

</section>

    <!-- =================================================
         MODUL YANG DIBUAT
    ================================================= -->

        <section class="modul-dibuat">


        <h2>

            Modul yang Dibuat

        </h2>


        <p>

            Daftar seluruh modul ajar yang pernah dibuat
            oleh guru ini.

        </p>


        <!-- =================================================
             JIKA ADA MODUL
        ================================================= -->

        <?php if ($totalModul > 0): ?>

<div class="modul-table-wrap">


<table class="modul-table">


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Judul Modul
                        </th>

                        <th>
                            Mata Pelajaran
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                $no = 1;

                while ($modul = mysqli_fetch_assoc($resultModul)):

                    $modulId = (int) $modul['id'];

                    $tanggalModul = date(
                        "d M Y",
                        strtotime($modul['created_at'])
                    );

                ?>


                    <tr>


                        <!-- NO -->

                        <td>

                            <?= $no++ ?>

                        </td>


                        <!-- JUDUL -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $modul['judul_modul']
                                ) ?>

                            </strong>

                        </td>


                        <!-- MAPEL -->

                        <td>

                            <?= htmlspecialchars(
                                $modul['mata_pelajaran']
                            ) ?>

                        </td>


                        <!-- KELAS -->

                        <td>

                            <?= htmlspecialchars(
                                $modul['kelas']
                            ) ?>

                    

                        </td>


                        <!-- TANGGAL -->

                        <td>

                            <?= htmlspecialchars(
                                $tanggalModul
                            ) ?>

                        </td>


                        <!-- AKSI -->

                        <td>


<a
    href="../output/index.php?id=<?= $modulId ?>&guru_id=<?= $guruId ?>"
    class="btn-generator"
>

                                <i
                                    class="bi bi-eye"
                                ></i>

                                Lihat

                            </a>


                        </td>


                    </tr>


                <?php endwhile; ?>


                </tbody>


            </table>


        </div>


        <?php else: ?>


        <!-- =================================================
             BELUM ADA MODUL
        ================================================= -->

<div class="empty-module">

            <i class="bi bi-journal-x"></i>


            <h3>

                Guru ini belum membuat modul.

            </h3>


            <p>

                Belum ada modul ajar yang dibuat
                oleh guru ini.

            </p>


        </div>


        <?php endif; ?>


    </section>


</div>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="gema-footer">


    <p>© 2026 GEMA AI - Generator Modul Ajar AI</p>


    <span>

        SMP Negeri 9 Pariaman

    </span>


</footer>


</main>


</div>


<script src="../assets/js/mobile.js"></script>
</body>

</html>