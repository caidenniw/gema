<?php

// Support ENV Railway: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
// + legacy MYSQL_* / DATABASE_URL handling
// Lokal fallback: localhost / root / "" / db_gema_ai

function _env($name, $default = '') {
    $v = getenv($name);
    if ($v !== false && $v !== '') return $v;
    if (isset($_ENV[$name]) && $_ENV[$name] !== '') return $_ENV[$name];
    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') return $_SERVER[$name];
    return $default;
}

// Railway MySQL plugin injects: MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE
// Some setups use MYSQL_* or MARIADB_* prefixes — handle both
$host = _env('MYSQLHOST', '');
if ($host === '') $host = _env('MYSQL_HOST', '');
if ($host === '') $host = _env('DB_HOST', '');
if ($host === '') $host = _env('DATABASE_HOST', '');

$port = _env('MYSQLPORT', '');
if ($port === '') $port = _env('MYSQL_PORT', '');
if ($port === '') $port = _env('DB_PORT', '');

$user = _env('MYSQLUSER', '');
if ($user === '') $user = _env('MYSQL_USER', '');
if ($user === '') $user = _env('DB_USER', '');

$password = _env('MYSQLPASSWORD', '');
if ($password === '') $password = _env('MYSQL_PASSWORD', '');
if ($password === '') $password = _env('DB_PASSWORD', '');

$database = _env('MYSQLDATABASE', '');
if ($database === '') $database = _env('MYSQL_DATABASE', '');
if ($database === '') $database = _env('DB_DATABASE', '');
if ($database === '') $database = _env('DATABASE_NAME', '');

// Fallback lokal
if ($host === '') $host = 'localhost';
if ($user === '') $user = 'root';
if ($password === false) $password = '';
if ($database === '') $database = 'db_gema_ai';

// Port handling: jika ada PORT, gabung ke host (mysqli bisa host:port)
if ($port !== '' && $port !== '3306') {
    // Hindari double port jika host sudah mengandung :
    if (strpos($host, ':') === false) {
        $host = $host . ':' . $port;
    }
}

$koneksi = mysqli_connect($host, $user, $password, $database);

// Mengecek koneksi database
if (!$koneksi) {
    // Pesan ramah untuk Railway build; jangan spill creds
    die("Koneksi database gagal : " . mysqli_connect_error() . " (host=" . htmlspecialchars($host) . ", db=" . htmlspecialchars($database) . ")");
}
