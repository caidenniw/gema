<?php
// GEMA AI - DeepSeek Config
// Support Railway ENV: DEEPSEEK_API_KEY, DEEPSEEK_URL, DEEPSEEK_MODEL
// Lokal: set ENV di Laragon/.env ATAU buat file config/local.php (gitignored)

$_deepseekKey = getenv('DEEPSEEK_API_KEY');
if ($_deepseekKey === false || $_deepseekKey === '') {
    $_deepseekKey = $_ENV['DEEPSEEK_API_KEY'] ?? $_SERVER['DEEPSEEK_API_KEY'] ?? '';
}
// Support local override file (tidak di-commit ke GitHub)
// Buat file config/local.php berisi: <?php $_localKey = 'sk-...';
if ($_deepseekKey === '' && file_exists(__DIR__ . '/local.php')) {
    include __DIR__ . '/local.php';
    if (isset($_localKey) && $_localKey !== '') {
        $_deepseekKey = $_localKey;
    }
}
// Placeholder jika belum di-set (Railway wajib set Variable DEEPSEEK_API_KEY)
if ($_deepseekKey === '' || $_deepseekKey === 'YOUR_DEEPSEEK_API_KEY') {
    $_deepseekKey = 'YOUR_DEEPSEEK_API_KEY';
}

define('DEEPSEEK_API_KEY', $_deepseekKey);

$_deepseekUrl = getenv('DEEPSEEK_URL');
if ($_deepseekUrl === false || $_deepseekUrl === '') {
    $_deepseekUrl = $_ENV['DEEPSEEK_URL'] ?? $_SERVER['DEEPSEEK_URL'] ?? 'https://api.deepseek.com/chat/completions';
}
define('DEEPSEEK_URL', $_deepseekUrl);

$_deepseekModel = getenv('DEEPSEEK_MODEL');
if ($_deepseekModel === false || $_deepseekModel === '') {
    $_deepseekModel = $_ENV['DEEPSEEK_MODEL'] ?? $_SERVER['DEEPSEEK_MODEL'] ?? 'deepseek-chat';
}
define('DEEPSEEK_MODEL', $_deepseekModel);
