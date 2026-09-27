<?php
session_start();
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../dashboard/index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | GEMA AI</title>


<!-- BOOTSTRAP -->
<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
rel="stylesheet">


<!-- ICON -->
<link 
href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet">


<!-- CSS -->
<link rel="stylesheet" href="../assets/css/auth.css">


<link rel="stylesheet" href="../assets/css/mobile.css?v=20250927">
</head>


<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-gema">

    <div class="container">

        <a class="navbar-brand" href="../index.php">
            <img src="../assets/img/logo.png" class="logo">
        </a>

        <div class="collapse navbar-collapse">

            
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a href="../index.php" class="nav-link">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a href="login.php" class="nav-link active">
                        Login
                    </a>
                </li>

            </ul>
           

        </div>

    </div>

</nav>

<div class="auth-wrapper">


    <div class="auth-card">


        <!-- LOGO -->

        <div class="text-center">

            <img 
            src="../assets/img/logo.png"
            class="auth-logo">


            <h2>
                Masuk ke GEMA AI
            </h2>


            <p>
                Gunakan email dan password yang telah disediakan.
            </p>


        </div>


        <!-- FORM -->


        <form 
        action="proses_login.php"
        method="POST">

            <label>Email</label>

            <div class="input-box">

                <i class="bi bi-envelope"></i>


                <input 
                type="email"
                name="email"
                placeholder="Masukkan email"
                required>


            </div>

            <label>Password</label>


            <div class="input-box">

                <i class="bi bi-lock"></i>


                <input 
                type="password"
                name="password"
                placeholder="Masukkan password"
                required>


            </div>

            <button 
            type="submit"
            class="btn-auth">

                Masuk

            </button>

        </form>

        <div class="auth-footer">

            <small>
                Akun disediakan oleh Administrator GEMA AI
            </small>

            <small>
                SMP Negeri 9 Pariaman
            </small>

        </div>

  </div>


</div>

<footer class="footer-auth">
    <h6>GEMA AI — Generator Modul Ajar Deep Learning</h6>
    <p>SMP Negeri 9 Pariaman</p>
    <p>Penelitian oleh <strong>Gina Hadai Yani Fitri</strong></p>
    <p>Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi</p>
    <small>©2026 GEMA AI</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>