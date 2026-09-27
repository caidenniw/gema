<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMA AI - Generator Modul Ajar</title>
    <!-- Bootstrap -->
    <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">
    <!-- Icon -->
    <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" 
    rel="stylesheet">
    <!-- CSS -->
   <link rel="stylesheet" href="assets/css/style.css?v=9999">

<link rel="stylesheet" href="assets/css/mobile.css?v=20250927m7">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-gema">

    <div class="container">
 
        <a class="navbar-brand" href="#">
            <img src="assets/img/logo.png" class="logo">
        </a>

        <!-- Tombol Hamburger -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
            aria-controls="navbarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="#beranda">Beranda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#fitur">Fitur</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#tentang">Tentang</a>
                </li>

            </ul>

            <a href="auth/login.php" class="btn btn-login">

                <i class="bi bi-box-arrow-in-right"></i>

                Login

            </a>

        </div>

    </div>

</nav>

<!-- HERO -->
<section class="hero" id="beranda">
    <div class="container">
    <div class="row">
        <div class="col-md-6">
        <h1>
        Generator Modul <br>
        Ajar <span>Deep Learning</span>
        </h1>
        <p>
        Membantu guru SMP Negeri 9 Pariaman menyusun Modul Ajar
        Pembelajaran Mendalam secara otomatis menggunakan Artificial Intelligence.
        </p>
        <a href="auth/login.php" class="btn btn-start">
        Mulai Sekarang
        <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="col-md-6 text-center">
        <img src="assets/img/hero.png" class="hero-img">
    </div>
    </div>

<!-- FITUR -->
    <div class="features" id="fitur">
    <div class="feature-item">
        <i class="bi bi-cpu"></i>
        <h6>AI Cerdas</h6>
        <p>Modul dibuat otomatis sesuai data guru</p>
    </div>
    <div class="feature-item">
        <i class="bi bi-file-earmark-word"></i>
        <h6>Word</h6>
        <p>Download DOCX dan dapat diedit</p>
    </div>
    <div class="feature-item">
        <i class="bi bi-file-pdf"></i>
        <h6>PDF</h6>
        <p>Siap dicetak kapan saja</p>
    </div>
    <div class="feature-item">
        <i class="bi bi-lightning-charge"></i>
        <h6>Cepat</h6>
        <p>Pembuatan hanya hitungan detik</p>
    </div>
    </div>

<!-- CARA KERJA -->
    <div class="steps" id="cara-kerja">
        <h4>Cara Kerja</h4>
    <div class="step-box">
    <div>
        <b>1</b>
        <h6>Isi Form</h6>
        <p>Guru mengisi data pembelajaran.</p>
    </div>
    <div>
        <b>2</b>
        <h6>Generate AI</h6>
        <p>AI menyusun modul otomatis.</p>
    </div>
    <div>
        <b>3</b>
        <h6>Download</h6>
        <p>Unduh Word atau PDF.</p>
    </div>
    </div>
    </div>
    </div>
</section>

<!-- FOOTER -->

<footer id="tentang">
    <h6>GEMA AI — Generator Modul Ajar Deep Learning</h6>
    <p>SMP Negeri 9 Pariaman</p>
    <p>Penelitian oleh <strong>Gina Hadai Yani Fitri</strong></p>
    <p>Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi</p>
    <small>©2026 GEMA AI</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>