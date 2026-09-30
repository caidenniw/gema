<?php
// GEMA AI - Provider Config
// Prioritas nilai: ENV (Railway Variables) > config/local.php > default di bawah
//
// Provider: OpenRouter (format OpenAI Chat Completions).
// Butuh key OpenRouter milik client (diset via ENV, bukan di file).

$_deepseekKey = getenv('DEEPSEEK_API_KEY');
if ($_deepseekKey === false || $_deepseekKey === '') {
    $_deepseekKey = $_ENV['DEEPSEEK_API_KEY'] ?? $_SERVER['DEEPSEEK_API_KEY'] ?? '';
}

$_deepseekUrl = getenv('DEEPSEEK_URL');
if ($_deepseekUrl === false || $_deepseekUrl === '') {
    $_deepseekUrl = $_ENV['DEEPSEEK_URL'] ?? $_SERVER['DEEPSEEK_URL'] ?? '';
}

$_deepseekModel = getenv('DEEPSEEK_MODEL');
if ($_deepseekModel === false || $_deepseekModel === '') {
    $_deepseekModel = $_ENV['DEEPSEEK_MODEL'] ?? $_SERVER['DEEPSEEK_MODEL'] ?? '';
}

$_deepseekMaxTokens = getenv('DEEPSEEK_MAX_TOKENS');
if ($_deepseekMaxTokens === false || $_deepseekMaxTokens === '') {
    $_deepseekMaxTokens = $_ENV['DEEPSEEK_MAX_TOKENS'] ?? $_SERVER['DEEPSEEK_MAX_TOKENS'] ?? '';
}

// Override lokal (file ini TIDAK di-commit ke GitHub)
// Bisa berisi: $_localKey, $_localUrl, $_localModel, $_localMaxTokens
if (file_exists(__DIR__ . '/local.php')) {
    include __DIR__ . '/local.php';
    if ($_deepseekKey === '' && isset($_localKey) && $_localKey !== '') {
        $_deepseekKey = $_localKey;
    }
    if ($_deepseekUrl === '' && isset($_localUrl) && $_localUrl !== '') {
        $_deepseekUrl = $_localUrl;
    }
    if ($_deepseekModel === '' && isset($_localModel) && $_localModel !== '') {
        $_deepseekModel = $_localModel;
    }
    if ($_deepseekMaxTokens === '' && isset($_localMaxTokens) && $_localMaxTokens !== '') {
        $_deepseekMaxTokens = (string) $_localMaxTokens;
    }
}

// Default terakhir
if ($_deepseekUrl === '') {
    $_deepseekUrl = 'https://openrouter.ai/api/v1/chat/completions';
}
if ($_deepseekModel === '') {
    $_deepseekModel = 'deepseek/deepseek-chat';
}
// Budget token output. Dipertahankan 64000 sebagai batas atas agar JSON
// modul penuh tidak terpotong (finish_reason: length).
if ($_deepseekMaxTokens === '') {
    $_deepseekMaxTokens = '64000';
}
if ($_deepseekKey === '' || $_deepseekKey === 'YOUR_DEEPSEEK_API_KEY') {
    $_deepseekKey = 'YOUR_DEEPSEEK_API_KEY';
}

define('DEEPSEEK_API_KEY', $_deepseekKey);
define('DEEPSEEK_URL', $_deepseekUrl);
define('DEEPSEEK_MODEL', $_deepseekModel);
define('DEEPSEEK_MAX_TOKENS', (int) $_deepseekMaxTokens);
