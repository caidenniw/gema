<?php

session_start();

require_once "../config/koneksi.php";

$role = $_SESSION['role'] ?? 'guru';
$isAdmin = ($role === 'admin');

if (isset($_GET['id'])) {

    if (!isset($_SESSION['id_user'])) {
        die("Silakan login terlebih dahulu.");
    }

    $id = (int) $_GET['id'];
    
    
    $userId = $_SESSION['id_user'];
    $role = $_SESSION['role'] ?? 'guru';

    if ($role === 'admin') {

        // ADMIN boleh melihat modul semua guru
        $query = mysqli_query(
            $koneksi,

            "SELECT
                hasil_ai,
                html_modul
            FROM modul_ajar
            WHERE id='$id'
            LIMIT 1"
        );

    } else {

        // GURU hanya boleh melihat modul miliknya sendiri
        $query = mysqli_query(
            $koneksi,

            "SELECT
                hasil_ai,
                html_modul
            FROM modul_ajar
            WHERE id='$id'
            AND user_id='$userId'
            LIMIT 1"
        );

    }

    if (mysqli_num_rows($query) == 0) {
        die("Modul tidak ditemukan.");
    }

    $data = mysqli_fetch_assoc($query);

    $htmlModul = '';

    $modul = json_decode($data['hasil_ai'], true);

} else {

    if (!isset($_SESSION['hasil_modul'])) {
        die("Data modul belum tersedia.");
    }

    $modul = $_SESSION['hasil_modul'];

}

$identitas = $modul['identitas'];
$identifikasi = $modul['identifikasi'];
$desain = $modul['desain_pembelajaran'];
$pengalaman = $modul['pengalaman_belajar'];
$asesmen = $modul['asesmen'];
$lampiran = $modul['lampiran'];
$lampiranAsesmen = $lampiran['asesmen'] ?? [];
$diagnostik = $lampiranAsesmen['diagnostik'] ?? [];
$formatif = $lampiranAsesmen['formatif'] ?? [];
$sumatif = $lampiranAsesmen['sumatif'] ?? [];
$pengayaan = $lampiran['pengayaan_dan_remedial']['pengayaan'] ?? '';
$remedial = $lampiran['pengayaan_dan_remedial']['remedial'] ?? '';
$refleksi = $lampiran['refleksi'] ?? [];
?>

<?php ob_start(); ?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview Modul Ajar</title>
<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css?v=999">
<link rel="stylesheet" href="preview.css?v=1">
</head>

<body>

<div class="gema-layout">
    <!-- SIDEBAR -->

<aside class="gema-sidebar">

    <img 
        src="../assets/img/logo.png"
        class="gema-logo"
    >

    <div class="gema-menu">

        <?php if ($isAdmin): ?>

            <!-- MENU ADMIN -->

            <a href="../admin/dashboard.php">
                <i class="bi bi-grid"></i>
                Dashboard
            </a>

            <a href="../admin/guru.php">
                <i class="bi bi-people"></i>
                Kelola Guru
            </a>

            <a href="../admin/semua_modul.php">
                <i class="bi bi-journal-text"></i>
                Semua Modul
            </a>

            <a href="../admin/import_guru.php">
                <i class="bi bi-upload"></i>
                Import Guru
            </a>

            <a href="../admin/statistik.php">
                <i class="bi bi-bar-chart"></i>
                Statistik
            </a>

        <?php else: ?>

            <!-- MENU GURU -->

            <a href="../dashboard/index.php">
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

        <?php endif; ?>

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

    <p>
        <?= $isAdmin ? 'Administrator' : 'SMP Negeri 9 Pariaman' ?>
    </p>

</div>

                <div class="divider"></div>
                <a href="../auth/logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Keluar
                </a>
            </div>
        </div>

<div class="preview-toolbar">
    <div class="preview-toolbar-inner">

<div class="toolbar-left">
    <h2 class="preview-title">
        Preview Modul
    </h2>
    
<?php if ($isAdmin): ?>

    <a 
        href="../admin/detail_guru.php?id=<?= $_GET['guru_id'] ?? '' ?>"
        class="toolbar-back"
    >

        <i class="bi bi-arrow-left"></i>

        Kembali ke Detail Guru

    </a>

<?php else: ?>

    <a 
        href="../history/index.php"
        class="toolbar-back"
    >

        <i class="bi bi-arrow-left"></i>

        Kembali ke Riwayat

    </a>

<?php endif; ?>

</div>

            <div class="toolbar-right">
            
            <button
            id="btnEdit"
            class="toolbar-btn"
            type="button">

            <i class="bi bi-pencil-square"></i>

            Edit

            </button>


            <a
            href="../export/word.php?id=<?= $id ?>"
            class="toolbar-btn">

            <i class="bi bi-file-earmark-word"></i>

            Word

            </a>
            <a href="../export/pdf.php?id=<?= $id ?>" class="toolbar-btn">
                <i class="bi bi-file-earmark-pdf"></i>
                PDF
            </a>
        </div>
    </div>
</div>



<div class="page" id="editor">

    <div class="document-header">

        <h1 class="document-title">
            MODUL AJAR PEMBELAJARAN MENDALAM
        </h1>

        <div class="document-subtitle">
            MATA PELAJARAN
            <?= htmlspecialchars($identitas['mata_pelajaran'] ?? '') ?>
        </div>

        <div class="document-school">
            SMP NEGERI 9 PARIAMAN
        </div>

    </div>

    <table class="modul-table">

    <tr class="section-title">
        <th colspan="2">IDENTITAS</th>
    </tr>

    <tr>
        <td class="label">Penyusun</td>
        <td
        class="editable"
        data-key="identitas.penyusun"
        contenteditable="false">

        <?= htmlspecialchars($identitas['penyusun']) ?>

        </td>
    </tr>

    <tr>
        <td class="label">NIP</td>
        <td
        class="editable"
        data-key="identitas.nip"
        contenteditable="false">

        <?= htmlspecialchars($identitas['nip']) ?>

        </td>
    </tr>

    <tr>
        <td class="label">Tahun Pelajaran</td>
        <td><?= htmlspecialchars($identitas['tahun_pelajaran']) ?></td>
    </tr>

    <tr>
        <td class="label">Semester</td>
        <td><?= htmlspecialchars($identitas['semester']) ?></td>
    </tr>

    <tr>
        <td class="label">Mata Pelajaran</td>
        <td><?= htmlspecialchars($identitas['mata_pelajaran']) ?></td>
    </tr>

    <tr>
        <td class="label">Kelas / Fase</td>
        <td><?= htmlspecialchars($identitas['kelas_fase']) ?></td>
    </tr>

    <tr>
        <td class="label">Topik Pembelajaran</td>
        <td><?= htmlspecialchars($identitas['topik_pembelajaran']) ?></td>
    </tr>

    <tr>
        <td class="label">Alokasi Waktu</td>
        <td><?= htmlspecialchars($identitas['alokasi_waktu']) ?></td>
    </tr>

</table>




<!-- ===========================
A. IDENTIFIKASI
=========================== -->

<table class="modul-table">

<tr class="section-title">
    <th colspan="2">A. IDENTIFIKASI</th>
</tr>

<tr>

    <td class="label">Murid</td>

    <td class="section-text">

        <?= nl2br(htmlspecialchars($identifikasi['murid'])) ?>

    </td>

</tr>

<tr>

    <td class="label">Materi Pembelajaran</td>

    <td class="section-text">

        <?= nl2br(htmlspecialchars($identifikasi['materi_pelajaran'])) ?>

    </td>

</tr>

<tr>

    <td class="label">Dimensi Profil Lulusan</td>

<td>

    <div class="bullet-list">

        <?php foreach(($identifikasi['dimensi_profil_lulusan'] ?? []) as $item): ?>

            <div class="bullet-item">

                <span class="bullet-dot"></span>

                <span class="bullet-text">
                    <?= htmlspecialchars($item) ?>
                </span>

            </div>

        <?php endforeach; ?>

    </div>

</td>

</tr>

</table>


<!-- ===========================
B. DESAIN PEMBELAJARAN
=========================== -->

<table class="modul-table">

<tr class="section-title">
    <th colspan="2">B. DESAIN PEMBELAJARAN</th>
</tr>

<tr>
    <td class="label">Capaian Pembelajaran</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['capaian_pembelajaran'] ?? '')) ?></td>
</tr>

<tr>
    <td class="label">Lintas Disiplin Ilmu</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['lintas_disiplin_ilmu'] ?? '')) ?></td>
</tr>

<tr>
    <td class="label">Tujuan Pembelajaran</td>
    <td>
    <ol>
        <?php
            $tujuan = $desain['tujuan_pembelajaran'] ?? [];
            if (!is_array($tujuan)) {
                $tujuan = [$tujuan];
            }
            foreach ($tujuan as $item):
            ?>
            <li><?= htmlspecialchars($item) ?></li>
        <?php endforeach; ?>
    </ol>
</td>
</tr>

<tr>
    <td class="label">Topik Pembelajaran</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['topik_pembelajaran'] ?? '')) ?></td>
</tr>

<tr>
    <td class="label">Praktik Pedagogis</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['praktik_pedagogis'] ?? '')) ?></td>
</tr>

<tr>
    <td class="label">Metode Pembelajaran</td>

    <td>

        <div class="bullet-list">

            <?php foreach(($desain['metode_pembelajaran'] ?? []) as $item): ?>

                <div class="bullet-item">

                    <span class="bullet-dot"></span>

                    <span class="bullet-text">
                        <?= htmlspecialchars($item) ?>
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    </td>
</tr>

<tr>
    <td class="label">Lingkungan Pembelajaran</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['lingkungan_pembelajaran'] ?? '')) ?></td>
</tr>

<tr>
    <td class="label">Pemanfaatan Digital</td>

    <td>

        <div class="bullet-list">

            <?php foreach(($desain['pemanfaatan_digital'] ?? []) as $item): ?>

                <div class="bullet-item">

                    <span class="bullet-dot"></span>

                    <span class="bullet-text">
                        <?= htmlspecialchars($item) ?>
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    </td>
</tr>

<tr>
    <td class="label">Kemitraan Pembelajaran</td>
    <td class="section-text"><?= nl2br(htmlspecialchars($desain['kemitraan_pembelajaran'] ?? '')) ?></td>
</tr>

</table>

<!-- ===========================
C. PENGALAMAN BELAJAR
=========================== -->

<table class="modul-table">

<tr class="section-title">
    <th colspan="2">C. PENGALAMAN BELAJAR</th>
</tr>

<!-- ================= AWAL ================= -->

<tr>

    <td class="label">

        <strong>AWAL</strong><br>

        <small>
            (<?= htmlspecialchars($pengalaman['awal']['durasi'] ?? '') ?>)
        </small>

    </td>

    <td>

        <div class="pengalaman-section">

            <ul class="kegiatan-list">

                <li>
                    <strong>Pembukaan :</strong>
                    <span><?= nl2br(htmlspecialchars($pengalaman['awal']['pembukaan'] ?? '')) ?></span>
                </li>

                <li>
                    <strong>Apersepsi :</strong>
                    <span><?= nl2br(htmlspecialchars($pengalaman['awal']['apersepsi'] ?? '')) ?></span>
                </li>

                <li>
                    <strong>Motivasi dan Pengkondisian :</strong>
                    <span><?= nl2br(htmlspecialchars($pengalaman['awal']['motivasi'] ?? '')) ?></span>
                </li>

            </ul>

            <div class="prinsip-box">

                <div class="prinsip-title">
                    Prinsip Pembelajaran
                </div>

                <ul class="prinsip-list">

                    <li>

                        <strong>Berkesadaran (Mindful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['awal']['prinsip_pembelajaran']['mindful'] ?? '')) ?>

                        </p>

                    </li>

                    <li>

                        <strong>Bermakna (Meaningful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['awal']['prinsip_pembelajaran']['meaningful'] ?? '')) ?>

                        </p>

                    </li>

                    <li>

                        <strong>Menggembirakan (Joyful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['awal']['prinsip_pembelajaran']['joyful'] ?? '')) ?>

                        </p>

                    </li>

                </ul>

            </div>

        </div>

    </td>

</tr>

<!-- ================= INTI ================= -->

<tr>

    <td class="label">

        <strong>INTI</strong><br>

        <small>
            (<?= htmlspecialchars($pengalaman['inti']['durasi'] ?? '') ?>)
        </small>

    </td>

    <td>

        <div class="pengalaman-section">

            <ul class="kegiatan-list">

                <li>

                    <strong>Memahami :</strong>

                    <span>
                        <?= nl2br(htmlspecialchars($pengalaman['inti']['memahami'] ?? '')) ?>
                    </span>

                </li>

                <li>

                    <strong>Mengorganisasi Belajar :</strong>

                    <span>
                        <?= nl2br(htmlspecialchars($pengalaman['inti']['mengorganisasi_belajar'] ?? '')) ?>
                    </span>

                </li>

                <li>

                    <strong>Mengaplikasikan :</strong>

                    <span>
                        <?= nl2br(htmlspecialchars($pengalaman['inti']['mengaplikasikan'] ?? '')) ?>
                    </span>

                </li>

                <li>

                    <strong>Merefleksi :</strong>

                    <span>
                        <?= nl2br(htmlspecialchars($pengalaman['inti']['merefleksi'] ?? '')) ?>
                    </span>

                </li>

            </ul>

            <div class="prinsip-box">

                <div class="prinsip-title">
                    Prinsip Pembelajaran
                </div>

                <ul class="prinsip-list">

                    <li>

                        <strong>Berkesadaran (Mindful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['inti']['prinsip_pembelajaran']['mindful'] ?? '')) ?>

                        </p>

                    </li>

                    <li>

                        <strong>Bermakna (Meaningful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['inti']['prinsip_pembelajaran']['meaningful'] ?? '')) ?>

                        </p>

                    </li>

                    <li>

                        <strong>Menggembirakan (Joyful)</strong>

                        <p>

                            <?= nl2br(htmlspecialchars($pengalaman['inti']['prinsip_pembelajaran']['joyful'] ?? '')) ?>

                        </p>

                    </li>

                </ul>

            </div>

        </div>

    </td>

</tr>

<!-- ================= PENUTUP ================= -->

<tr>

    <td class="label">

        <strong>PENUTUP</strong><br>

        <small>
            (<?= htmlspecialchars($pengalaman['penutup']['durasi'] ?? '') ?>)
        </small>

    </td>

    <td>

        <div class="pengalaman-section">

            <ul class="kegiatan-list">

                <li>

                    <strong>Kesimpulan :</strong>

                    <span>

                        <?= nl2br(htmlspecialchars($pengalaman['penutup']['kesimpulan'] ?? '')) ?>

                    </span>

                </li>

                <li>

                    <strong>Umpan Balik :</strong>

                    <span>

                        <?= nl2br(htmlspecialchars($pengalaman['penutup']['umpan_balik'] ?? '')) ?>

                    </span>

                </li>

                <li>

                    <strong>Rencana Lanjutan :</strong>

                    <span>

                        <?= nl2br(htmlspecialchars($pengalaman['penutup']['rencana_lanjutan'] ?? '')) ?>

                    </span>

                </li>

                <li>

                    <strong>Penutup :</strong>

                    <span>

                        <?= nl2br(htmlspecialchars($pengalaman['penutup']['penutup'] ?? '')) ?>

                    </span>
                </li>
            </ul>
            <div class="prinsip-box">
                <div class="prinsip-title">
                    Prinsip Pembelajaran
                </div>
                <ul class="prinsip-list">
                    <li>
                        <strong>Berkesadaran (Mindful)</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($pengalaman['penutup']['prinsip_pembelajaran']['mindful'] ?? '')) ?>
                        </p>
                    </li>
                    <li>
                        <strong>Bermakna (Meaningful)</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($pengalaman['penutup']['prinsip_pembelajaran']['meaningful'] ?? '')) ?>
                        </p>
                    </li>
                    <li>
                        <strong>Menggembirakan (Joyful)</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($pengalaman['penutup']['prinsip_pembelajaran']['joyful'] ?? '')) ?>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </td>
</tr>
</table>

<!-- ===========================
D. ASESMEN PEMBELAJARAN
=========================== -->

<table class="modul-table">
<tr class="section-title">
    <th colspan="2">
        D. ASESMEN PEMBELAJARAN
    </th>
</tr>
<tr>
    <td class="label">
        Asesmen Diagnostik
    </td>
    <td>
        <?= htmlspecialchars($asesmen['asesmen_diagnostik']['teknik'] ?? '') ?>
        <span class="terlampir">(Terlampir)</span>
    </td>
</tr>
<tr>
    <td class="label">
        Asesmen Formatif
    </td>
    <td>
        <?= htmlspecialchars($asesmen['asesmen_formatif']['teknik'] ?? '') ?>
        <span class="terlampir">(Terlampir)</span>
    </td>
</tr>
<tr>
    <td class="label">
        Asesmen Sumatif
    </td>
    <td>
        <?= htmlspecialchars($asesmen['asesmen_sumatif']['teknik'] ?? '') ?>
        <span class="terlampir">(Terlampir)</span>
    </td>
</tr>
</table>

<!-- ===========================
II. LAMPIRAN
=========================== -->

<div class="lampiran-title">
    LAMPIRAN
</div>

<!-- ===========================
A. ASESMEN
=========================== -->

<div class="lampiran-heading">A. ASESMEN</div>

<!-- ===========================
1. DIAGNOSTIK
=========================== -->

<div class="lampiran-subheading">

1. Asesmen Diagnostik

</div>

<div class="lampiran-section">

    <div class="lampiran-label">Tujuan</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($diagnostik['tujuan'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Instrumen</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($diagnostik['instrumen'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Petunjuk Guru</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($diagnostik['petunjuk'] ?? '')) ?>
    </p>

    <?php if (!empty($diagnostik['pertanyaan'])): ?>

        
        <div class="lampiran-label">Pertanyaan</div>

        <ol class="lampiran-list">

            <?php foreach (($diagnostik['pertanyaan'] ?? []) as $item): ?>

                <li>
                    <?= htmlspecialchars($item) ?>
                </li>

            <?php endforeach; ?>

        </ol>

    <?php endif; ?>

    <?php if (!empty($diagnostik['kunci_jawaban'])): ?>

        <div class="lampiran-label">Kunci Jawaban</div>
        <div class="lampiran-answer">

            <?php

$text = trim($diagnostik['kunci_jawaban']);

$text = preg_replace('/\s+(\d+[\.\)])/', "\n$1", $text);

$items = preg_split('/\n(?=\d+[\.\)])/', $text);

if (count($items) > 1) {

    echo '<ol class="lampiran-list">';

    foreach ($items as $item) {

        $item = trim($item);

        if ($item == '') continue;

        $item = preg_replace('/^\d+[\.\)]\s*/', '', $item);
        echo '<li>' . htmlspecialchars($item) . '</li>';
    }

    echo '</ol>';

} else {

    echo nl2br(htmlspecialchars($text));

}

?>

        </div>

    <?php endif; ?>

</div>

<!-- ===========================
2. FORMATIF
=========================== -->

<div class="lampiran-subheading">

2. Asesmen Formatif

</div>
<div class="lampiran-section">

    <div class="lampiran-label">Tujuan</div>
    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($formatif['tujuan'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Instrumen</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($formatif['instrumen'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Petunjuk Guru</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($formatif['petunjuk'] ?? '')) ?>
    </p>

    <?php if (!empty($formatif['lkpd'])): ?>

        <div class="lampiran-label">Lembar Kerja Peserta Didik (LKPD)</div>

        <div class="lampiran-text">
            <?= nl2br(htmlspecialchars($formatif['lkpd'])) ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($formatif['rubrik'])): ?>

        <div class="lampiran-label">Pedoman Penilaian</div>

        <table class="rubrik-table">

            <colgroup>

    <col style="width:14%">

    <col style="width:22%">

    <col style="width:16%">

    <col style="width:16%">

    <col style="width:16%">

    <col style="width:16%">

</colgroup>

<thead>

<tr>

    <th>Aspek</th>

    <th>Indikator</th>

    <th>Sangat Baik</th>

    <th>Baik</th>

    <th>Cukup</th>

    <th>Perlu Bimbingan</th>

</tr>

</thead>

            <tbody>

            <?php foreach(($formatif['rubrik'] ?? []) as $row): ?>

                <tr>

                    <td><?= htmlspecialchars($row['aspek'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['indikator'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['sangat_baik'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['baik'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['cukup'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['perlu_bimbingan'] ?? '') ?></td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>


<!-- ===========================
3. SUMATIF
=========================== -->

<div class="lampiran-subheading">

3. Asesmen Sumatif

</div>


<div class="lampiran-section">

    <div class="lampiran-label">Tujuan</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($sumatif['tujuan'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Instrumen</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($sumatif['instrumen'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Petunjuk Guru</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($sumatif['petunjuk'] ?? '')) ?>
    </p>

    <?php if (!empty($sumatif['soal'])): ?>

        <div class="lampiran-label">Soal</div>

        <ol class="lampiran-list">

            <?php foreach(($sumatif['soal'] ?? []) as $item): ?>

                <li>
                    <?= htmlspecialchars($item) ?>
                </li>

            <?php endforeach; ?>

        </ol>

    <?php endif; ?>

<?php if (!empty($sumatif['kunci_jawaban'])): ?>

    <div class="lampiran-label">Kunci Jawaban</div>

    <div class="lampiran-answer">

        <?php

        $text = trim($sumatif['kunci_jawaban']);

        $text = preg_replace('/\s+(\d+[\.\)])/', "\n$1", $text);

        $items = preg_split('/\n(?=\d+[\.\)])/', $text);

        if (count($items) > 1) {

            echo '<ol class="lampiran-list">';

            foreach ($items as $item) {

                $item = trim($item);

                if ($item == '') continue;

                $item = preg_replace('/^\d+[\.\)]\s*/', '', $item);

                echo '<li>' . htmlspecialchars($item) . '</li>';

            }

            echo '</ol>';

        } else {

            echo nl2br(htmlspecialchars($text));

        }

        ?>

    </div>

<?php endif; ?>

    <?php if (!empty($sumatif['rubrik'])): ?>

        <div class="lampiran-label">Pedoman Penilaian</div>

        <table class="rubrik-table">

            <colgroup>

    <col style="width:14%">

    <col style="width:22%">

    <col style="width:16%">

    <col style="width:16%">

    <col style="width:16%">

    <col style="width:16%">

</colgroup>

<thead>

<tr>

    <th>Aspek</th>

    <th>Indikator</th>

    <th>Sangat Baik</th>

    <th>Baik</th>

    <th>Cukup</th>

    <th>Perlu Bimbingan</th>

</tr>

</thead>

            <tbody>

            <?php foreach(($sumatif['rubrik'] ?? []) as $row): ?>

                <tr>

                    <td><?= htmlspecialchars($row['aspek'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['indikator'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['sangat_baik'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['baik'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['cukup'] ?? '') ?></td>

                    <td><?= htmlspecialchars($row['perlu_bimbingan'] ?? '') ?></td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>

<!-- ===========================
B. PENGAYAAN DAN REMEDIAL
=========================== -->

<div class="lampiran-heading">
    B. Pengayaan dan Remedial
</div>

<div class="lampiran-section">

    <div class="lampiran-label">Pengayaan</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($pengayaan)) ?>
    </p>

    <div class="lampiran-label">Remedial</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($remedial)) ?>
    </p>

</div>

<!-- ===========================
C. REFLEKSI
=========================== -->


<div class="lampiran-heading">
    C. Refleksi
</div>

<div class="lampiran-section">

    <div class="lampiran-label">Refleksi Guru</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($refleksi['guru'] ?? '')) ?>
    </p>

    <div class="lampiran-label">Refleksi Peserta Didik</div>

    <p class="lampiran-text">
        <?= nl2br(htmlspecialchars($refleksi['peserta_didik'] ?? '')) ?>
    </p>

</div>

<!-- ===========================
TANDA TANGAN
=========================== -->

<div class="ttd-container">

    <div class="ttd-header">

        <div>Mengetahui,</div>

        <div>
            Pariaman,
            <?= date('d-m-Y') ?>
        </div>

    </div>

    <div class="ttd-row">

        <div class="ttd-box">

            <div class="ttd-jabatan">
                Kepala Sekolah
            </div>

            <div class="ttd-space"></div>

            <div class="ttd-kepala">
                Yanti Octavia, S.Kom
            </div>

            <div class="ttd-nip">
                NIP. 197807032009012000
            </div>

        </div>

        <div class="ttd-box">

            <div class="ttd-jabatan">
                Guru Mata Pelajaran
            </div>

            <div class="ttd-space"></div>

            <div class="ttd-guru">

                <?= htmlspecialchars($identitas['penyusun'] ?? '') ?>

            </div>

            <div class="ttd-nip">

                NIP.
                <?= htmlspecialchars($identitas['nip'] ?? '') ?>

            </div>

        </div>

    </div>

</div>
</div>

<script src="editor.js"></script>

<script src="../assets/js/mobile.js"></script>
</body>

</html>

<?php

$htmlOutput = ob_get_clean();

echo $htmlOutput;

?>