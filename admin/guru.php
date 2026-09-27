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

/* =========================================================
   SEARCH GURU
========================================================= */

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($keyword !== '') {

    $search = "%" . $keyword . "%";

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            id,
            nama_lengkap,
            nip,
            email,
            created_at
         FROM users
         WHERE role = 'guru'
         AND (
            nama_lengkap LIKE ?
            OR nip LIKE ?
            OR email LIKE ?
         )
         ORDER BY nama_lengkap ASC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $search,
        $search,
        $search
    );

    mysqli_stmt_execute($stmt);

    $query = mysqli_stmt_get_result($stmt);

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            id,
            nama_lengkap,
            nip,
            email,
            created_at
         FROM users
         WHERE role = 'guru'
         ORDER BY nama_lengkap ASC"
    );

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

<title>Kelola Guru | GEMA AI</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="../assets/css/admin.css"
>

<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m7">
</head>

<body>

<div class="gema-layout">


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="gema-sidebar">

    <img
        src="../assets/img/logo.png"
        class="gema-logo"
    >

    <div class="gema-menu">

        <a href="dashboard.php">

            <i class="bi bi-grid"></i>

            Dashboard

        </a>


        <a class="active">

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
                <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
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

<section class="guru-content">


    <h1>
        Kelola Guru
    </h1>


    <p>
        Kelola akun guru yang dapat menggunakan GEMA AI.
    </p>



    <!-- =================================================
         AKSI
    ================================================== -->

    <div class="guru-actions">


        <!-- SEARCH -->

        <form
            action="guru.php"
            method="GET"
            class="guru-search"
        >

            <div class="guru-search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Cari nama guru, NIP, atau email..."
                    autocomplete="off"
                >

            </div>


            <?php if ($keyword !== ''): ?>

                <a
                    href="guru.php"
                    class="guru-search-reset"
                    title="Hapus pencarian"
                >

                    <i class="bi bi-x-lg"></i>

                </a>

            <?php endif; ?>


            <button
                type="submit"
                class="guru-search-btn"
            >

                <i class="bi bi-search"></i>

                Cari

            </button>

        </form>



        <!-- IMPORT GURU -->

        <a
            href="import_guru.php"
            class="btn-generator"
        >

            <i class="bi bi-file-earmark-arrow-up"></i>

            Import Guru

        </a>

    </div>



    <!-- =================================================
         HASIL PENCARIAN
    ================================================== -->

    <?php if ($keyword !== ''): ?>

        <div class="guru-search-info">

            <i class="bi bi-info-circle"></i>

            <span>
                Hasil pencarian untuk
                <strong>
                    "<?= htmlspecialchars($keyword) ?>"
                </strong>
            </span>

        </div>

    <?php endif; ?>



    <!-- =================================================
         TABEL GURU
    ================================================== -->

    <div class="guru-table-wrap">

        <table class="guru-table">

            <thead>

                <tr>

                    <th>
                        No
                    </th>

                    <th>
                        Nama Guru
                    </th>

                    <th>
                        NIP
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Tanggal Dibuat
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php

            $no = 1;

            if (mysqli_num_rows($query) > 0):

            ?>

                <?php while ($guru = mysqli_fetch_assoc($query)): ?>

                    <tr>


                        <!-- NO -->

                        <td>

                            <?= $no++ ?>

                        </td>



                        <!-- NAMA -->

                        <td>

                            <?= htmlspecialchars(
                                $guru['nama_lengkap']
                            ) ?>

                        </td>



                        <!-- NIP -->

                        <td>

                            <?= htmlspecialchars(
                                $guru['nip'] ?? '-'
                            ) ?>

                        </td>



                        <!-- EMAIL -->

                        <td>

                            <?= htmlspecialchars(
                                $guru['email']
                            ) ?>

                        </td>



                        <!-- TANGGAL -->

                        <td>

                            <?= date(
                                'd M Y',
                                strtotime($guru['created_at'])
                            ) ?>

                        </td>



                        <!-- AKSI -->

                        <td>

                            <a
                                href="detail_guru.php?id=<?= $guru['id'] ?>"
                                class="btn-generator"
                            >

                                <i class="bi bi-eye"></i>

                                Lihat

                            </a>

                        </td>


                    </tr>

                <?php endwhile; ?>


            <?php else: ?>

                <tr>

                    <td
                        colspan="6"
                        style="padding:30px;text-align:center;"
                    >

                        <?php if ($keyword !== ''): ?>

                            <i
                                class="bi bi-search"
                                style="font-size:24px;"
                            ></i>

                            <br><br>

                            Guru dengan kata kunci
                            <strong>
                                "<?= htmlspecialchars($keyword) ?>"
                            </strong>
                            tidak ditemukan.

                        <?php else: ?>

                            Belum ada guru terdaftar.

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


</section>



<!-- =====================================================
     FOOTER
===================================================== -->

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