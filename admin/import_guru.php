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

$pesan = '';
$error = '';

/* =========================================================
   DOWNLOAD TEMPLATE CSV
   ========================================================= */

if (isset($_GET['template'])) {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="template_guru.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'nama_lengkap',
        'nip',
        'email',
        'password'
    ]);

    fputcsv($output, [
        'Nama Guru',
        '1987654321',
        'guru@gmail.com',
        'password123'
    ]);

    fclose($output);
    exit;
}

/* =========================================================
   PROSES IMPORT CSV
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_FILES['file_csv']) ||
        $_FILES['file_csv']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = "Silakan pilih file CSV terlebih dahulu.";

    } else {

        $file = $_FILES['file_csv'];

        if ($file['size'] > 2 * 1024 * 1024) {

            $error = "Ukuran file maksimal 2 MB.";

        } else {

            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            if ($extension !== 'csv') {

                $error = "File harus berformat CSV.";

            } else {

                $handle = fopen($file['tmp_name'], 'r');

                if (!$handle) {

                    $error = "File CSV tidak dapat dibaca.";

                } else {

                    $header = fgetcsv($handle);

                    if (!$header) {

                        $error = "File CSV kosong.";

                    } else {

                        /*
                         * Hilangkan BOM jika CSV dibuat dari Excel/Windows.
                         */
                        $header[0] = preg_replace(
                            '/^\xEF\xBB\xBF/',
                            '',
                            $header[0]
                        );

                        $header = array_map(
                            function ($item) {
                                return strtolower(trim($item));
                            },
                            $header
                        );

                        $headerWajib = [
                            'nama_lengkap',
                            'nip',
                            'email',
                            'password'
                        ];

                        if ($header !== $headerWajib) {

                            $error =
                                "Format kolom CSV tidak sesuai. " .
                                "Gunakan urutan: nama_lengkap, nip, email, password.";

                        } else {

                            $berhasil = 0;
                            $ditolak = 0;

                            while (($data = fgetcsv($handle)) !== false) {

                                if (
                                    count($data) === 1 &&
                                    trim($data[0]) === ''
                                ) {
                                    continue;
                                }

                                $nama = trim($data[0] ?? '');
                                $nip = trim($data[1] ?? '');
                                $email = trim($data[2] ?? '');
                                $password = trim($data[3] ?? '');

                                if (
                                    $nama === '' ||
                                    $nip === '' ||
                                    $email === '' ||
                                    $password === ''
                                ) {
                                    $ditolak++;
                                    continue;
                                }

                                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $ditolak++;
                                    continue;
                                }

                                if (strlen($password) < 6) {
                                    $ditolak++;
                                    continue;
                                }

                                $stmtCheck = mysqli_prepare(
                                    $koneksi,
                                    "SELECT id
                                     FROM users
                                     WHERE email = ?
                                     LIMIT 1"
                                );

                                mysqli_stmt_bind_param(
                                    $stmtCheck,
                                    "s",
                                    $email
                                );

                                mysqli_stmt_execute($stmtCheck);

                                $resultCheck = mysqli_stmt_get_result($stmtCheck);

                                if (mysqli_num_rows($resultCheck) > 0) {
                                    $ditolak++;
                                    mysqli_stmt_close($stmtCheck);
                                    continue;
                                }

                                mysqli_stmt_close($stmtCheck);

                                $passwordHash = password_hash(
                                    $password,
                                    PASSWORD_DEFAULT
                                );

                                /*
                                 * Akun guru hanya memakai:
                                 * nama_lengkap, email, password, role, created_at.
                                 * Tidak ada NIP pada proses import.
                                 */
                                $stmtInsert = mysqli_prepare(
                                    $koneksi,
                                    "INSERT INTO users
                                    (
                                        nama_lengkap,
                                        nip,
                                        email,
                                        password,
                                        role,
                                        created_at
                                    )
                                    VALUES
                                    (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        'guru',
                                        NOW()
                                    )"
                                );

                                mysqli_stmt_bind_param(
                                    $stmtInsert,
                                    "ssss",
                                    $nama,
                                    $nip,
                                    $email,
                                    $passwordHash
                                );

                                if (mysqli_stmt_execute($stmtInsert)) {
                                    $berhasil++;
                                } else {
                                    $ditolak++;
                                }

                                mysqli_stmt_close($stmtInsert);
                            }

                            $pesan =
                                "Import selesai. " .
                                $berhasil .
                                " akun guru berhasil ditambahkan.";

                            if ($ditolak > 0) {
                                $pesan .=
                                    " " .
                                    $ditolak .
                                    " data dilewati karena tidak valid atau email sudah terdaftar.";
                            }
                        }
                    }

                    fclose($handle);
                }
            }
        }
    }
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

    <title>Import Guru | GEMA AI</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=2"
    >

<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927m6">
</head>

<body>

<div class="gema-layout">

    <!-- SIDEBAR -->
    <aside class="gema-sidebar">

        <img
            src="../assets/img/logo.png"
            class="gema-logo"
            alt="GEMA AI"
        >

        <div class="gema-menu">

            <a href="./dashboard.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>

            <a href="./guru.php">
                <i class="bi bi-people"></i>
                <span>Kelola Guru</span>
            </a>

            <a href="./semua_modul.php">
                <i class="bi bi-journal-text"></i>
                <span>Semua Modul</span>
            </a>

            <a
                href="./import_guru.php"
                class="active"
            >
                <i class="bi bi-file-earmark-arrow-up"></i>
                <span>Import Guru</span>
            </a>

            <a href="./statistik.php">
                <i class="bi bi-bar-chart"></i>
                <span>Statistik</span>
            </a>

        </div>

    </aside>

    <!-- MAIN -->
    <main class="gema-main">

        <!-- TOPBAR -->
        <div class="gema-topbar">

            <div class="gema-user">

                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="user-info">
                    <b>
                        <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
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

        <!-- CONTENT -->
        <section class="admin-content import-content">

            <a
                href="./guru.php"
                class="admin-back"
            >
                <i class="bi bi-arrow-left"></i>
                Kembali ke Kelola Guru
            </a>

            <div class="admin-page-heading">
                <div>
                    <h1>Import Guru</h1>
                    <p>
                        Tambahkan banyak akun guru sekaligus menggunakan file CSV.
                    </p>
                </div>
            </div>

            <?php if ($pesan !== ''): ?>

                <div class="admin-alert success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($pesan) ?></span>
                </div>

            <?php endif; ?>

            <?php if ($error !== ''): ?>

                <div class="admin-alert error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>

            <?php endif; ?>

            <!-- KARTU UPLOAD -->
            <div class="admin-card import-card">

                <div class="import-card-heading">

                    <div class="admin-card-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>

                    <div>
                        <h2>Upload Data Guru</h2>
                        <p>
                            Gunakan file CSV untuk membuat beberapa akun guru sekaligus.
                        </p>
                    </div>

                </div>

                <a
                    href="./import_guru.php?template=1"
                    class="template-link"
                >
                    <i class="bi bi-download"></i>
                    Download Template CSV
                </a>

                <div class="admin-info-box">

                    <i class="bi bi-info-circle-fill"></i>

                    <div>
                        <strong>Format file CSV</strong>

                        <p>
                            Gunakan 4 kolom:
                            <b>nama_lengkap</b>,
                            <b>nip</b>,
                            <b>email</b>,
                            dan
                            <b>password</b>.
                        </p>
                        <p>
                            NIP digunakan sebagai bagian dari identitas data guru
                            dan disimpan bersama akun guru.
                        </p>
                        <p>
                            Password disediakan oleh admin dan digunakan guru
                            untuk login ke GEMA AI.
                        </p>
                    </div>

                </div>

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="import-form"
                >

                    <div class="upload-box">

                        <div class="upload-icon">
                            <i class="bi bi-filetype-csv"></i>
                        </div>

                        <div class="upload-text">

                            <label for="file_csv">
                                Pilih File CSV
                            </label>

                            <p>
                                Pilih file CSV yang sudah disiapkan.
                            </p>

                            <small>
                                Format .csv • Maksimal 2 MB
                            </small>

                        </div>

                        <input
                            type="file"
                            name="file_csv"
                            id="file_csv"
                            accept=".csv,text/csv"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn-admin-primary"
                    >
                        <i class="bi bi-upload"></i>
                        Import Data Guru
                    </button>

                </form>

            </div>

            <!-- KARTU FORMAT -->
            <div class="admin-card import-card">

                <div class="import-card-heading">

                    <div class="admin-card-icon">
                        <i class="bi bi-table"></i>
                    </div>

                    <div>
                        <h2>Format Data</h2>
                        <p>
                            Pastikan urutan kolom pada file CSV sesuai contoh berikut.
                        </p>
                    </div>

                </div>

                <div class="csv-preview">

                    <div class="csv-header">
                        <span>nama_lengkap</span>
                        <span>nip</span>
                        <span>email</span>
                        <span>password</span>
                    </div>

                    <div class="csv-row">
                        <span>Gina Hadai Yani Fitri</span>
                        <span>2522110</span>
                        <span>gina@gmail.com</span>
                        <span>gina1234</span>
                    </div>

                </div>

                <ul class="admin-list">

                    <li>
                        Satu baris digunakan untuk satu akun guru.
                    </li>
                    <li>
                        Nama, NIP, email, dan password wajib diisi.
                    </li>
                    <li>
                        Password minimal 6 karakter.
                    </li>
                    <li>
                        Email yang sudah terdaftar tidak akan diimpor ulang.
                    </li>
                    <li>
                        Password disimpan dalam bentuk hash demi keamanan.
                    </li>
                    <li>
                        Guru login menggunakan email dan password yang disediakan admin.
                    </li>
                </ul>
            </div>
        </section>

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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.getElementById('file_csv');
    const uploadBox = document.querySelector('.upload-box');
    const uploadText = document.querySelector('.upload-text p');

    if (!fileInput) return;

    fileInput.addEventListener('change', function () {

        if (this.files.length > 0) {

            uploadBox.classList.add('has-file');

            uploadText.textContent =
                this.files[0].name;

        } else {

            uploadBox.classList.remove('has-file');

            uploadText.textContent =
                'Pilih file CSV yang sudah disiapkan.';
        }

    });

});
</script>

<script src="../assets/js/mobile.js"></script>
</body>
</html>
