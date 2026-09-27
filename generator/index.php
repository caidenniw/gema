<?php

session_start();

if(!isset($_SESSION['login'])){
    header("location:../auth/login.php");
    exit;
}

/* =========================================
   MULAI MODUL BARU
   Hapus data modul sebelumnya
========================================= */

if(isset($_GET['new']) && $_GET['new'] === '1'){
    unset($_SESSION['step1']);
    unset($_SESSION['step2']);
    unset($_SESSION['hasil_modul']);
    unset($_SESSION['tanggal_modul']);
}

$nama = $_SESSION['nama_lengkap'];
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

/*
|--------------------------------------------------------------------------
| Ambil data session yang sudah pernah diisi
|--------------------------------------------------------------------------
*/

$step1 = $_SESSION['step1'] ?? [];
$step2 = $_SESSION['step2'] ?? [];

/*
|--------------------------------------------------------------------------
| Jika user mencoba langsung membuka Step 2 tanpa mengisi Step 1
|--------------------------------------------------------------------------
*/

if($step == 2 && empty($step1)){
    header("Location: index.php?step=1");
    exit;
}

/*
|--------------------------------------------------------------------------
| Jika user mencoba langsung membuka Step 3 tanpa menyelesaikan Step 1 & 2
|--------------------------------------------------------------------------
*/

if($step == 3 && (empty($step1) || empty($step2))){
    header("Location: index.php?step=1");
    exit;
}

/*
|--------------------------------------------------------------------------
| Helper untuk mengamankan output
|--------------------------------------------------------------------------
*/

function old($array, $key, $default = '')
{
    return htmlspecialchars(
        $array[$key] ?? $default,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>


<!DOCTYPE html>

<html lang="id">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

Buat Modul | GEMA AI

</title>



<link 
href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet">



<link rel="stylesheet" href="../assets/css/dashboard.css?v=999">
<link rel="stylesheet" href="../assets/css/wizard.css">
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


<a href="../dashboard/index.php">

<i class="bi bi-house"></i>

Beranda

</a>

<a class="active" href="index.php?new=1">

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

<section class="wizard-page">

    <div class="wizard-header">

        <h2>✨ Buat Modul Ajar Baru</h2>

        <p>
            Lengkapi informasi berikut untuk membuat
            Modul Ajar Deep Learning berbasis AI.
        </p>

    </div>

    <div class="wizard-progress">

        <div class="step <?= $step==1 ? 'active' : '' ?>">

            <div class="circle">1</div>

            <span>Identitas Modul</span>

        </div>

        <div class="line"></div>

        <div class="step <?= $step==2 ? 'active' : '' ?>">

            <div class="circle">2</div>

            <span>Desain Pembelajaran</span>

        </div>

        <div class="line"></div>

        <div class="step <?= $step==3 ? 'active' : '' ?>">

            <div class="circle">3</div>

            <span>Review & Generate</span>

        </div>

    </div>



    <?php if($step == 1){ ?>

    <form action="../process/step1.php"
      method="POST"
      class="wizard-card">

    <h3>Identitas Modul</h3>

    <div class="wizard-form">

        <div class="form-group">
            <label>Penyusun <span>*</span></label>
            <input
                type="text"
                name="penyusun"
                value="<?= old($step1, 'penyusun') ?>"
                placeholder="Contoh : Gina Hadai Yani Fitri"
                required>
        </div>

        <div class="form-group">
            <label>NIP <span>*</span></label>
            <input
                type="text"
                name="nip"
                value="<?= old($step1, 'nip') ?>"
                placeholder="Contoh : 199812312020121001" required>
        </div>

        <div class="form-group">
            <label>Tahun Pelajaran <span>*</span></label>

            <select name="tahun" required>

                <option value="" disabled <?= empty($step1['tahun']) ? 'selected' : '' ?>>
                    Pilih Tahun Pelajaran
                </option>

                <option value="2026 / 2027"
                    <?= ($step1['tahun'] ?? '') == '2026 / 2027' ? 'selected' : '' ?>>
                    2026 / 2027
                </option>

                <option value="2027 / 2028"
                    <?= ($step1['tahun'] ?? '') == '2027 / 2028' ? 'selected' : '' ?>>
                    2027 / 2028
                </option>

                <option value="2028 / 2029"
                    <?= ($step1['tahun'] ?? '') == '2028 / 2029' ? 'selected' : '' ?>>
                    2028 / 2029
                </option>

                <option value="2029 / 2030"
                    <?= ($step1['tahun'] ?? '') == '2029 / 2030' ? 'selected' : '' ?>>
                    2029 / 2030
                </option>

                <option value="2030 / 2031"
                    <?= ($step1['tahun'] ?? '') == '2030 / 2031' ? 'selected' : '' ?>>
                    2030 / 2031
                </option>

            </select>

        </div>

        <div class="form-group">
            <label>Semester <span>*</span></label>

            <select name="semester" required>

                <option value="" disabled <?= empty($step1['semester']) ? 'selected' : '' ?>>
                    Pilih Semester
                </option>

                <option value="Ganjil"
                    <?= ($step1['semester'] ?? '') == 'Ganjil' ? 'selected' : '' ?>>
                    Ganjil
                </option>

                <option value="Genap"
                    <?= ($step1['semester'] ?? '') == 'Genap' ? 'selected' : '' ?>>
                    Genap
                </option>

            </select>
        </div>

        <div class="form-group">
            <label>Mata Pelajaran <span>*</span></label>

            <input
                type="text"
                name="mapel"
                value="<?= old($step1, 'mapel') ?>"
                placeholder="Contoh : Informatika" required>
        </div>

        <div class="form-group">
            <label>Kelas / Fase <span>*</span></label>

            <select name="kelas" required>

                <option value="" disabled <?= empty($step1['kelas']) ? 'selected' : '' ?>>
                    Pilih Kelas
                </option>

                <option value="VII / Fase D"
                    <?= ($step1['kelas'] ?? '') == 'VII / Fase D' ? 'selected' : '' ?>>
                    VII / Fase D
                </option>

                <option value="VIII / Fase D"
                    <?= ($step1['kelas'] ?? '') == 'VIII / Fase D' ? 'selected' : '' ?>>
                    VIII / Fase D
                </option>

                <option value="IX / Fase D"
                    <?= ($step1['kelas'] ?? '') == 'IX / Fase D' ? 'selected' : '' ?>>
                    IX / Fase D
                </option>

            </select>

        </div>

        <div class="form-group">
            <label>Topik Pembelajaran <span>*</span></label>

            <input
                type="text"
                name="topik"
                value="<?= old($step1, 'topik') ?>"
                placeholder="Contoh : Berpikir Komputasional" required>

        </div>

        <div class="form-group">
            <label>Alokasi Waktu <span>*</span></label>

            <select name="alokasi" required>

                <option value="" disabled <?= empty($step1['alokasi']) ? 'selected' : '' ?>>
                    Pilih Alokasi Waktu
                </option>

                <option value="2 x 40 menit"
                    <?= ($step1['alokasi'] ?? '') == '2 x 40 menit' ? 'selected' : '' ?>>
                    2 x 40 menit
                </option>

                <option value="3 x 40 menit"
                    <?= ($step1['alokasi'] ?? '') == '3 x 40 menit' ? 'selected' : '' ?>>
                    3 x 40 menit
                </option>

            </select>
        </div>

        <div class="form-group">
            <label>Tanggal Modul <span>*</span></label>
            <input
                type="date"
                name="tanggal"
                value="<?= old($step1, 'tanggal', date('Y-m-d')) ?>"
                required>
        </div>

    </div>

    <div class="wizard-action">

        <button type="submit" class="btn-next">

            Lanjut

            <i class="bi bi-arrow-right"></i>

        </button>

    </div>

</form>
<?php } ?>


<!-- ================= STEP 2 ================= -->

<?php if($step == 2){ ?>

<form
action="../process/step2.php"
method="POST"
class="wizard-card">

<h3>Desain Pembelajaran</h3>

<div class="wizard-form-single">

    <!-- CAPAIAN -->

    <div class="form-group full">

        <label>Capaian Pembelajaran <span>*</span></label>

        <textarea
        name="cp"
        rows="4"
        placeholder="Contoh : Peserta didik mampu menerapkan berpikir komputasional untuk menyelesaikan masalah sederhana." required><?= old($step2, 'cp') ?></textarea>

    </div>

    <!-- TUJUAN -->

    <div class="form-group full">

        <label>Tujuan Pembelajaran <span>*</span></label>

        <textarea
        name="tp"
        rows="4"
        placeholder="Contoh : Peserta didik mampu mengidentifikasi pola, melakukan abstraksi dan membuat algoritma sederhana." required><?= old($step2, 'tp') ?></textarea>

    </div>

    <!-- IDENTIFIKASI -->

    <div class="form-group full">

        <label>Identifikasi Murid <span>*</span></label>

        <textarea
        name="identifikasi"
        rows="4"
        placeholder="Contoh : Sebagian besar peserta didik telah mampu menggunakan perangkat digital sederhana namun masih memerlukan bimbingan dalam menyusun algoritma." required><?= old($step2, 'identifikasi') ?></textarea>

    </div>

<!-- DIMENSI PROFIL LULUSAN -->

<div class="form-group full">

    <label>Dimensi Profil Lulusan <span>*</span></label>

    <?php
    $profil_terpilih = $_SESSION['step2']['profil'] ?? [];
    ?>

    <div class="checkbox-grid">

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Keimanan kepada Tuhan YME"
                <?= in_array('Keimanan kepada Tuhan YME', $profil_terpilih) ? 'checked' : '' ?>
            >
            Keimanan kepada Tuhan YME
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Kewargaan"
                <?= in_array('Kewargaan', $profil_terpilih) ? 'checked' : '' ?>
            >
            Kewargaan
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Penalaran Kritis"
                <?= in_array('Penalaran Kritis', $profil_terpilih) ? 'checked' : '' ?>
            >
            Penalaran Kritis
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Kreativitas"
                <?= in_array('Kreativitas', $profil_terpilih) ? 'checked' : '' ?>
            >
            Kreativitas
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Kolaborasi"
                <?= in_array('Kolaborasi', $profil_terpilih) ? 'checked' : '' ?>
            >
            Kolaborasi
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Kemandirian"
                <?= in_array('Kemandirian', $profil_terpilih) ? 'checked' : '' ?>
            >
            Kemandirian
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Kesehatan"
                <?= in_array('Kesehatan', $profil_terpilih) ? 'checked' : '' ?>
            >
            Kesehatan
        </label>

        <label>
            <input
                type="checkbox"
                name="profil[]"
                value="Komunikasi"
                <?= in_array('Komunikasi', $profil_terpilih) ? 'checked' : '' ?>
            >
            Komunikasi
        </label>

    </div>

</div>



<div class="form-group full">

<label>Praktik Pedagogis <span>*</span></label>

<select name="praktik" required>

    <option value="" disabled <?= empty($step2['praktik']) ? 'selected' : '' ?>>
        Pilih Praktik Pedagogis
    </option>

    <option value="Problem Based Learning (PBL)"
        <?= ($step2['praktik'] ?? '') == 'Problem Based Learning (PBL)' ? 'selected' : '' ?>>
        Problem Based Learning (PBL)
    </option>

    <option value="Project Based Learning (PjBL)"
        <?= ($step2['praktik'] ?? '') == 'Project Based Learning (PjBL)' ? 'selected' : '' ?>>
        Project Based Learning (PjBL)
    </option>

    <option value="Discovery Learning"
        <?= ($step2['praktik'] ?? '') == 'Discovery Learning' ? 'selected' : '' ?>>
        Discovery Learning
    </option>

    <option value="Inquiry Learning"
        <?= ($step2['praktik'] ?? '') == 'Inquiry Learning' ? 'selected' : '' ?>>
        Inquiry Learning
    </option>

    <option value="Cooperative Learning"
        <?= ($step2['praktik'] ?? '') == 'Cooperative Learning' ? 'selected' : '' ?>>
        Cooperative Learning
    </option>

    <option value="Contextual Teaching Learning (CTL)"
        <?= ($step2['praktik'] ?? '') == 'Contextual Teaching Learning (CTL)' ? 'selected' : '' ?>>
        Contextual Teaching Learning (CTL)
    </option>

    <option value="Blended Learning"
        <?= ($step2['praktik'] ?? '') == 'Blended Learning' ? 'selected' : '' ?>>
        Blended Learning
    </option>

</select>

</div>


<div class="form-group full">

<label>Metode Pembelajaran (pilih maksimal 4 metode)<span>*</span></label>

<div class="checkbox-grid" id="metodePembelajaran">

    <?php
    $metode_terpilih = $_SESSION['step2']['metode'] ?? [];
    ?>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Ceramah"
            <?= in_array('Ceramah', $metode_terpilih) ? 'checked' : '' ?>
        >
        Ceramah
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Diskusi"
            <?= in_array('Diskusi', $metode_terpilih) ? 'checked' : '' ?>
        >
        Diskusi
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Tanya Jawab"
            <?= in_array('Tanya Jawab', $metode_terpilih) ? 'checked' : '' ?>
        >
        Tanya Jawab
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Demonstrasi"
            <?= in_array('Demonstrasi', $metode_terpilih) ? 'checked' : '' ?>
        >
        Demonstrasi
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Presentasi"
            <?= in_array('Presentasi', $metode_terpilih) ? 'checked' : '' ?>
        >
        Presentasi
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Penugasan"
            <?= in_array('Penugasan', $metode_terpilih) ? 'checked' : '' ?>
        >
        Penugasan
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Praktik"
            <?= in_array('Praktik', $metode_terpilih) ? 'checked' : '' ?>
        >
        Praktik
    </label>

    <label>
        <input
            type="checkbox"
            name="metode[]"
            value="Studi Kasus"
            <?= in_array('Studi Kasus', $metode_terpilih) ? 'checked' : '' ?>
        >
        Studi Kasus
    </label>

</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const metodeCheckboxes = document.querySelectorAll(
        '#metodePembelajaran input[type="checkbox"]'
    );

    function updateMetode() {
        let jumlahTerpilih = 0;

        metodeCheckboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                jumlahTerpilih++;
            }
        });

        metodeCheckboxes.forEach(function (checkbox) {
            if (jumlahTerpilih >= 4 && !checkbox.checked) {
                checkbox.disabled = true;
            } else {
                checkbox.disabled = false;
            }
        });
    }

    metodeCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateMetode);
    });

    updateMetode();
});
</script>


<div class="form-group full">

<label>Lingkungan Pembelajaran</label>

<textarea
name="lingkungan"
rows="4"
placeholder="Contoh : Ruang kelas, laboratorium komputer, perpustakaan, atau lingkungan sekolah."><?= old($step2, 'lingkungan') ?></textarea>

</div>


<div class="form-group full">

    <label>Pemanfaatan Digital</label>

    <?php
    $digital_terpilih = $_SESSION['step2']['digital'] ?? [];
    ?>

    <div class="checkbox-grid">

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Video Pembelajaran"
                <?= in_array('Video Pembelajaran', $digital_terpilih) ? 'checked' : '' ?>
            >
            Video Pembelajaran
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Google Classroom"
                <?= in_array('Google Classroom', $digital_terpilih) ? 'checked' : '' ?>
            >
            Google Classroom
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Quizizz"
                <?= in_array('Quizizz', $digital_terpilih) ? 'checked' : '' ?>
            >
            Quizizz
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Kahoot"
                <?= in_array('Kahoot', $digital_terpilih) ? 'checked' : '' ?>
            >
            Kahoot
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Canva"
                <?= in_array('Canva', $digital_terpilih) ? 'checked' : '' ?>
            >
            Canva
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="PowerPoint Interaktif"
                <?= in_array('PowerPoint Interaktif', $digital_terpilih) ? 'checked' : '' ?>
            >
            PowerPoint Interaktif
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Internet / Sumber Digital"
                <?= in_array('Internet / Sumber Digital', $digital_terpilih) ? 'checked' : '' ?>
            >
            Internet / Sumber Digital
        </label>

        <label>
            <input
                type="checkbox"
                name="digital[]"
                value="Tidak menggunakan media digital"
                <?= in_array('Tidak menggunakan media digital', $digital_terpilih) ? 'checked' : '' ?>
            >
            Tidak menggunakan media digital
        </label>

    </div>

</div>


<div class="form-group full">

<label>Kemitraan Pembelajaran (Opsional)</label>

<textarea
name="kemitraan"
rows="4"
placeholder="Contoh : Orang tua, guru mata pelajaran lain, pustakawan, praktisi, atau narasumber."><?= old($step2, 'kemitraan') ?></textarea>

</div>


</div>

<div class="wizard-action">



<a
href="index.php?step=1"
class="btn-back">

<i class="bi bi-arrow-left"></i>

Kembali

</a>


<button
type="submit"
class="btn-next">

Lanjut

<i class="bi bi-arrow-right"></i>

</button>



</div>

</form>
<?php } ?>


<!-- ================= STEP 3 ================= -->


<?php if($step == 3){ ?>

<div class="wizard-card review-card">

    <h3>Review Data Modul</h3>

    <p class="review-description">
        Silakan periksa kembali seluruh data sebelum Generate Modul.
    </p>


    <!-- =========================================
         IDENTITAS MODUL
    ========================================== -->

    <div class="review-section">

        <div class="review-section-title">
            <span>📘</span>
            <h4>Identitas Modul</h4>
        </div>


        <div class="review-grid">

            <div>
                <b>Penyusun</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['penyusun'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>NIP</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['nip'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Tahun Pelajaran</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['tahun'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Semester</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['semester'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Mata Pelajaran</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['mapel'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Kelas / Fase</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['kelas'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Topik Pembelajaran</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['topik'] ?? '-'
                    ) ?>
                </p>
            </div>


            <div>
                <b>Alokasi Waktu</b>
                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step1']['alokasi'] ?? '-'
                    ) ?>
                </p>
            </div>

        </div>

    </div>


    <!-- =========================================
         DESAIN PEMBELAJARAN
    ========================================== -->

    <div class="review-section">

        <div class="review-section-title">
            <span>🎯</span>
            <h4>Desain Pembelajaran</h4>
        </div>


        <div class="review-grid review-design">


            <!-- CP -->

            <div class="full">

                <b>Capaian Pembelajaran</b>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $_SESSION['step2']['cp'] ?? '-'
                        )
                    ) ?>
                </p>

            </div>


            <!-- TP -->

            <div class="full">

                <b>Tujuan Pembelajaran</b>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $_SESSION['step2']['tp'] ?? '-'
                        )
                    ) ?>
                </p>

            </div>


            <!-- IDENTIFIKASI -->

            <div class="full">

                <b>Identifikasi Murid</b>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $_SESSION['step2']['identifikasi'] ?? '-'
                        )
                    ) ?>
                </p>

            </div>


            <!-- DIMENSI PROFIL -->

            <div>

                <b>Dimensi Profil Lulusan</b>

                <p>

                    <?php

                    $profil = $_SESSION['step2']['profil'] ?? [];

                    if(!empty($profil)){

                        echo htmlspecialchars(
                            implode(', ', $profil)
                        );

                    }else{

                        echo '-';

                    }

                    ?>

                </p>

            </div>


            <!-- PRAKTIK -->

            <div>

                <b>Praktik Pedagogis</b>

                <p>
                    <?= htmlspecialchars(
                        $_SESSION['step2']['praktik'] ?? '-'
                    ) ?>
                </p>

            </div>


            <!-- METODE -->

            <div class="full">

                <b>Metode Pembelajaran</b>

                <p>

                    <?php

                    $metode = $_SESSION['step2']['metode'] ?? [];

                    if(!empty($metode)){

                        echo htmlspecialchars(
                            implode(', ', $metode)
                        );

                    }else{

                        echo '-';

                    }

                    ?>

                </p>

            </div>


            <!-- LINGKUNGAN -->

            <div class="full">

                <b>Lingkungan Pembelajaran</b>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $_SESSION['step2']['lingkungan'] ?? '-'
                        )
                    ) ?>
                </p>

            </div>


            <!-- DIGITAL -->

            <div class="full">

                <b>Pemanfaatan Digital</b>

                <p>

                    <?php

                    $digital = $_SESSION['step2']['digital'] ?? [];

                    if(!empty($digital)){

                        echo htmlspecialchars(
                            implode(', ', $digital)
                        );

                    }else{

                        echo '-';

                    }

                    ?>

                </p>

            </div>


            <!-- KEMITRAAN -->

            <div class="full">

                <b>Kemitraan Pembelajaran</b>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $_SESSION['step2']['kemitraan'] ?? '-'
                        )
                    ) ?>
                </p>

            </div>


        </div>

    </div>


    <!-- =========================================
         ACTION
    ========================================== -->

    <div class="wizard-action">

        <a
            href="index.php?step=2"
            class="btn-back"
        >

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>


        <form
            action="../process/generate.php"
            method="POST"
            id="generateForm"
        >

            <button
                type="submit"
                class="btn-next"
                id="generateBtn"
            >

                <i class="bi bi-stars"></i>

                Generate Modul AI

            </button>

        </form>

    </div>

</div>

<?php } ?>


</section>

</main>


</div>

<div id="loadingAI" class="loading-ai">

    <div class="loading-box">

        <div class="loading-logo">

            🤖

        </div>

        <h2>GEMA AI</h2>

        <p>

            Sedang membuat Modul Ajar...

        </p>

        <div class="loading-spinner"></div>

        <span>

            Mohon tunggu sebentar.<br>

            Jangan menutup halaman ini.

        </span>

    </div>

</div>

<script>

const form=document.getElementById("generateForm");

if(form){

form.addEventListener("submit",function(){

document.getElementById("loadingAI").classList.add("show");

document.getElementById("generateBtn").disabled=true;

});

}

</script>

<script src="../assets/js/wizard.js"></script>

<script src="../assets/js/mobile.js"></script>
</body>


</html>