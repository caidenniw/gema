<?php

session_start();

require_once "../config/koneksi.php";


// =====================================
// CEK REQUEST
// =====================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: login.php");
    exit;

}


// =====================================
// AMBIL INPUT
// =====================================

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


// =====================================
// VALIDASI
// =====================================

if ($email === '' || $password === '') {

    echo "
    <script>
        alert('Email dan password wajib diisi.');
        window.location='login.php';
    </script>
    ";

    exit;
}


// =====================================
// CARI USER
// =====================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama_lengkap, nip, email, password, role
     FROM users
     WHERE email = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $email
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// =====================================
// CEK USER
// =====================================

if (mysqli_num_rows($result) === 0) {

    echo "
    <script>
        alert('Email atau password salah.');
        window.location='login.php';
    </script>
    ";

    exit;
}


$user = mysqli_fetch_assoc($result);


// =====================================
// CEK PASSWORD
// =====================================

if (!password_verify($password, $user['password'])) {

    echo "
    <script>
        alert('Email atau password salah.');
        window.location='login.php';
    </script>
    ";

    exit;
}


// =====================================
// SESSION
// =====================================

session_regenerate_id(true);

$_SESSION['login'] = true;

$_SESSION['id_user'] = $user['id'];

$_SESSION['nama_lengkap'] = $user['nama_lengkap'];

$_SESSION['nip'] = $user['nip'];

$_SESSION['email'] = $user['email'];

$_SESSION['role'] = $user['role'];


// =====================================
// REDIRECT BERDASARKAN ROLE
// =====================================

if ($user['role'] === 'admin') {

    header("Location: ../admin/dashboard.php");

} elseif ($user['role'] === 'guru') {

    header("Location: ../dashboard/index.php");

} else {

    session_unset();
    session_destroy();

    echo "
    <script>
        alert('Role akun tidak dikenali.');
        window.location='login.php';
    </script>
    ";

    exit;
}

exit;