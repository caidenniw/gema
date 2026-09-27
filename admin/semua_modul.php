<?php

session_start();

require_once "../config/koneksi.php";

/* =========================================================
   CEK LOGIN
   ========================================================= */

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* =========================================================
   CEK ROLE ADMIN
   ========================================================= */

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

/* =========================================================
   DATA ADMIN
   ========================================================= */

$namaAdmin = $_SESSION['nama_lengkap'] ?? 'Admin';

/* =========================================================
   PENCARIAN MODUL
   ========================================================= */

$kataKunci = trim($_GET['q'] ?? '');

$sql = "
    SELECT
        m.id,
        m.judul_modul,
        m.mata_pelajaran,
        m.kelas,
        m.fase,
        m.created_at,
        u.id AS guru_id,
        u.nama_lengkap AS nama_guru
    FROM modul_ajar m
    LEFT JOIN users u
        ON m.user_id = u.id
";

if ($kataKunci !== '') {
    $sql .= "
        WHERE
            m.judul_modul LIKE ?
            OR u.nama_lengkap LIKE ?
            OR m.mata_pelajaran LIKE ?
    ";
}

$sql .= " ORDER BY m.created_at DESC ";

if ($kataKunci !== '') {
    $stmtModul = mysqli_prepare($koneksi, $sql);
    $polaCari = '%' . $kataKunci . '%';
    mysqli_stmt_bind_param($stmtModul, "sss", $polaCari, $polaCari, $polaCari);
    mysqli_stmt_execute($stmtModul);
    $resultModul = mysqli_stmt_get_result($stmtModul);
} else {
    $resultModul = mysqli_query($koneksi, $sql);
}

/* =========================================================
   HITUNG TOTAL HASIL
   ========================================================= */

$totalModul = 0;

if ($resultModul) {
    $totalModul = mysqli_num_rows($resultModul);
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

    <title>
        Semua Modul | GEMA AI
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- CSS ADMIN UTAMA -->
    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=1"
    >

<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m7">
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

            <a href="dashboard.php">

                <i class="bi bi-grid"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="guru.php">

                <i class="bi bi-people"></i>

                <span>
                    Kelola Guru
                </span>

            </a>


            <a
                href="semua_modul.php"
                class="active"
            >

                <i class="bi bi-journal-text"></i>

                <span>
                    Semua Modul
                </span>

            </a>


            <a href="import_guru.php">

                <i class="bi bi-file-earmark-arrow-up"></i>

                <span>
                    Import Guru
                </span>

            </a>


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
    ====================================================== -->

    <main class="gema-main">

        <!-- TOPBAR -->

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
        ====================================================== -->

        <div class="admin-content semua-modul-content">

            <h1>
                Semua Modul
            </h1>

            <p>
                Daftar seluruh modul ajar yang dibuat melalui GEMA AI.
            </p>


            <div class="modul-tools">

                <form action="semua_modul.php" method="GET" class="modul-search">

                    <div class="modul-search-box">

                        <i class="bi bi-search"></i>

                        <input type="text" name="q" value="<?= htmlspecialchars($kataKunci) ?>" placeholder="Cari judul modul, nama guru, atau mata pelajaran..." autocomplete="off">

                        <?php if ($kataKunci !== ''): ?>
                            <a href="semua_modul.php" class="modul-search-reset" title="Reset pencarian">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>

                    </div>

                    <button type="submit" class="modul-search-btn">
                        <i class="bi bi-search"></i>
                        Cari
                    </button>

                </form>

                <div class="total-modul-badge">
                    <?= $totalModul ?> Modul
                </div>

            </div>

            <?php if ($kataKunci !== ''): ?>
                <div class="modul-search-info">
                    <i class="bi bi-info-circle"></i>
                    <span>Menampilkan hasil pencarian untuk <strong>"<?= htmlspecialchars($kataKunci) ?>"</strong></span>
                </div>
            <?php endif; ?>

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
                                    Guru
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

                            $guruId = (int) $modul['guru_id'];

                            $tanggalModul = "-";

                            if (!empty($modul['created_at'])) {

                                $tanggalModul = date(
                                    "d M Y",
                                    strtotime($modul['created_at'])
                                );

                            }

                        ?>

                            <tr>

                                <td>
                                    <?= $no++ ?>
                                </td>


                                <td class="judul-modul">

                                    <?= htmlspecialchars(
                                        $modul['judul_modul'] ?? '-'
                                    ) ?>

                                </td>


                                <td class="nama-guru">

                                    <?= htmlspecialchars(
                                        $modul['nama_guru'] ?? '-'
                                    ) ?>

                                </td>


                                <td class="mapel">

                                    <?= htmlspecialchars(
                                        $modul['mata_pelajaran'] ?? '-'
                                    ) ?>

                                </td>


                                <td class="kelas">

                                    <?= htmlspecialchars(
                                        $modul['kelas'] ?? '-'
                                    ) ?>

                                </td>


                                <td class="tanggal">

                                    <?= htmlspecialchars(
                                        $tanggalModul
                                    ) ?>

                                </td>


                                <td>

                                    <a
                                        href="../output/index.php?id=<?= $modulId ?>&guru_id=<?= $guruId ?>"
                                        class="btn-lihat"
                                    >

                                        <i class="bi bi-eye"></i>

                                        Lihat

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>

                <div class="empty-modul">

                    <i class="bi bi-journal-x"></i>

                    <?php if ($kataKunci !== ''): ?>
                        <h3>Modul Tidak Ditemukan</h3>
                        <p>Tidak ada modul yang sesuai dengan pencarian "<?= htmlspecialchars($kataKunci) ?>".</p>
                    <?php else: ?>
                        <h3>Belum Ada Modul</h3>
                        <p>Belum ada guru yang membuat modul ajar.</p>
                    <?php endif; ?>

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
