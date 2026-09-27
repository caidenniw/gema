<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

require_once "../config/koneksi.php";

$namaAdmin = $_SESSION['nama_lengkap'];


/* =========================================================
   STATISTIK UTAMA
========================================================= */

/* Total Guru */
$queryGuru = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role = 'guru'"
);

$totalGuru = mysqli_fetch_assoc($queryGuru)['total'];


/* Total Modul */
$queryModul = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM modul_ajar"
);

$totalModul = mysqli_fetch_assoc($queryModul)['total'];


/* Total Download */
$queryDownload = mysqli_query(
    $koneksi,
    "SELECT IFNULL(
        SUM(download_word + download_pdf),
        0
     ) AS total
     FROM modul_ajar"
);

$totalDownload = mysqli_fetch_assoc($queryDownload)['total'];


/* =========================================================
   MODUL PER MATA PELAJARAN
========================================================= */

$queryMapel = mysqli_query(
    $koneksi,
    "SELECT
        mata_pelajaran,
        COUNT(*) AS total
     FROM modul_ajar
     GROUP BY mata_pelajaran
     ORDER BY total DESC"
);

$dataMapel = [];

while ($row = mysqli_fetch_assoc($queryMapel)) {
    $dataMapel[] = $row;
}


/* =========================================================
   GURU PALING AKTIF
========================================================= */

$queryGuruAktif = mysqli_query(
    $koneksi,
    "SELECT
        users.nama_lengkap,
        COUNT(modul_ajar.id) AS total_modul
     FROM users
     LEFT JOIN modul_ajar
        ON modul_ajar.user_id = users.id
     WHERE users.role = 'guru'
     GROUP BY users.id, users.nama_lengkap
     ORDER BY total_modul DESC
     LIMIT 5"
);

$dataGuruAktif = [];

while ($row = mysqli_fetch_assoc($queryGuruAktif)) {
    $dataGuruAktif[] = $row;
}


/* =========================================================
   MODUL TERBARU
========================================================= */

$queryTerbaru = mysqli_query(
    $koneksi,
    "SELECT
        judul_modul,
        mata_pelajaran,
        kelas,
        created_at
     FROM modul_ajar
     ORDER BY created_at DESC
     LIMIT 5"
);

$dataTerbaru = [];

while ($row = mysqli_fetch_assoc($queryTerbaru)) {
    $dataTerbaru[] = $row;
}


/* =========================================================
   MODUL BULANAN
========================================================= */

$queryBulanan = mysqli_query(
    $koneksi,
    "SELECT
        DATE_FORMAT(created_at, '%Y-%m') AS bulan,
        COUNT(*) AS total
     FROM modul_ajar
     GROUP BY DATE_FORMAT(created_at, '%Y-%m')
     ORDER BY bulan DESC
     LIMIT 6"
);

$dataBulanan = [];

while ($row = mysqli_fetch_assoc($queryBulanan)) {
    $dataBulanan[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Statistik | GEMA AI</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=3"
    >

<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m6">
</head>


<body>

<div class="gema-layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="gema-sidebar">

        <img
            src="../assets/img/logo.png"
            class="gema-logo"
            alt="GEMA AI"
        >

        <div class="gema-menu">

            <a href="./dashboard.php">

                <i class="bi bi-grid"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="./guru.php">

                <i class="bi bi-people"></i>

                <span>
                    Kelola Guru
                </span>

            </a>


            <a href="./semua_modul.php">

                <i class="bi bi-journal-text"></i>

                <span>
                    Semua Modul
                </span>

            </a>


            <a href="./import_guru.php">

                <i class="bi bi-file-earmark-arrow-up"></i>

                <span>
                    Import Guru
                </span>

            </a>


            <a
                href="./statistik.php"
                class="active"
            >

                <i class="bi bi-bar-chart"></i>

                <span>
                    Statistik
                </span>

            </a>

        </div>

    </aside>



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="gema-main">


        <!-- =================================================
             TOPBAR
        ================================================== -->

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



        <!-- =================================================
             CONTENT
        ================================================== -->

        <section class="admin-content statistik-content">


            <!-- HEADER -->

            <div class="admin-page-heading">

                <div>

                    <h1>
                        Statistik
                    </h1>

                    <p>
                        Pantau penggunaan GEMA AI dan aktivitas guru.
                    </p>

                </div>

            </div>



            <!-- =================================================
                 STAT CARD
            ================================================== -->

            <div class="statistik-summary">


                <div class="statistik-card">

                    <div class="statistik-icon">

                        <i class="bi bi-people"></i>

                    </div>

                    <div>

                        <span>
                            Guru Terdaftar
                        </span>

                        <strong>
                            <?= $totalGuru ?>
                        </strong>

                    </div>

                </div>



                <div class="statistik-card">

                    <div class="statistik-icon">

                        <i class="bi bi-journal-text"></i>

                    </div>

                    <div>

                        <span>
                            Total Modul
                        </span>

                        <strong>
                            <?= $totalModul ?>
                        </strong>

                    </div>

                </div>



                <div class="statistik-card">

                    <div class="statistik-icon">

                        <i class="bi bi-download"></i>

                    </div>

                    <div>

                        <span>
                            Total Download
                        </span>

                        <strong>
                            <?= $totalDownload ?>
                        </strong>

                    </div>

                </div>


            </div>



            <!-- =================================================
                 GRID STATISTIK
            ================================================== -->

            <div class="statistik-grid">


                <!-- =============================================
                     MATA PELAJARAN
                ============================================== -->

                <div class="admin-card statistik-box">

                    <div class="statistik-box-title">

                        <div>

                            <h2>
                                Modul per Mata Pelajaran
                            </h2>

                            <p>
                                Jumlah modul berdasarkan mata pelajaran.
                            </p>

                        </div>

                        <i class="bi bi-book"></i>

                    </div>


                    <div class="statistik-list">

                        <?php if (count($dataMapel) > 0): ?>

                            <?php foreach ($dataMapel as $mapel): ?>

                                <div class="statistik-row">

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $mapel['mata_pelajaran']
                                            ) ?>
                                        </strong>

                                    </div>

                                    <span>
                                        <?= $mapel['total'] ?> modul
                                    </span>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="statistik-empty">

                                Belum ada data modul.

                            </div>

                        <?php endif; ?>

                    </div>

                </div>



                <!-- =============================================
                     GURU AKTIF
                ============================================== -->

                <div class="admin-card statistik-box">

                    <div class="statistik-box-title">

                        <div>

                            <h2>
                                Guru Paling Aktif
                            </h2>

                            <p>
                                Guru dengan jumlah modul terbanyak.
                            </p>

                        </div>

                        <i class="bi bi-person-check"></i>

                    </div>


                    <div class="statistik-list">

                        <?php if (count($dataGuruAktif) > 0): ?>

                            <?php foreach ($dataGuruAktif as $index => $guru): ?>

                                <div class="statistik-row">

                                    <div class="guru-ranking">

                                        <span class="ranking-number">
                                            <?= $index + 1 ?>
                                        </span>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $guru['nama_lengkap']
                                            ) ?>
                                        </strong>

                                    </div>

                                    <span>
                                        <?= $guru['total_modul'] ?> modul
                                    </span>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="statistik-empty">

                                Belum ada aktivitas guru.

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


            </div>



            <!-- =================================================
                 AKTIVITAS BULANAN
            ================================================== -->

            <div class="admin-card statistik-box full-width">

                <div class="statistik-box-title">

                    <div>

                        <h2>
                            Aktivitas Pembuatan Modul
                        </h2>

                        <p>
                            Jumlah modul yang dibuat berdasarkan bulan.
                        </p>

                    </div>

                    <i class="bi bi-graph-up"></i>

                </div>


                <div class="monthly-stat">

                    <?php if (count($dataBulanan) > 0): ?>

                        <?php foreach ($dataBulanan as $bulan): ?>

                            <?php

                            $namaBulan = date(
                                'F Y',
                                strtotime($bulan['bulan'] . '-01')
                            );

                            ?>

                            <div class="monthly-row">

                                <span>
                                    <?= htmlspecialchars($namaBulan) ?>
                                </span>

                                <div class="monthly-bar-wrap">

                                    <div
                                        class="monthly-bar"
                                        style="
                                            width: <?= min(
                                                100,
                                                ($bulan['total'] / max(
                                                    array_column(
                                                        $dataBulanan,
                                                        'total'
                                                    )
                                                )) * 100
                                            ) ?>%;
                                        "
                                    ></div>

                                </div>

                                <strong>
                                    <?= $bulan['total'] ?>
                                </strong>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="statistik-empty">

                            Belum ada data aktivitas modul.

                        </div>

                    <?php endif; ?>

                </div>

            </div>



            <!-- =================================================
                 MODUL TERBARU
            ================================================== -->

            <div class="admin-card statistik-box full-width">

                <div class="statistik-box-title">

                    <div>

                        <h2>
                            Modul Terbaru
                        </h2>

                        <p>
                            Lima modul terakhir yang dibuat guru.
                        </p>

                    </div>

                    <i class="bi bi-clock-history"></i>

                </div>


                <div class="statistik-table-wrap">

                    <table class="statistik-table">

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

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (count($dataTerbaru) > 0): ?>

                            <?php foreach (
                                $dataTerbaru
                                as $index => $modul
                            ): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $modul['judul_modul']
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $modul['mata_pelajaran']
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $modul['kelas']
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $modul['created_at']
                                            )
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="table-empty"
                                >
                                    Belum ada modul.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


        </section>



        <!-- =================================================
             FOOTER
        ================================================== -->

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