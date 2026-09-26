<?php
require_once __DIR__ . "/config/config.php";

echo "=== CONFIG ===\n";
echo "MODEL: " . DEEPSEEK_MODEL . "\n";
echo "URL: " . DEEPSEEK_URL . "\n";
echo "KEY: " . substr(DEEPSEEK_API_KEY,0,10) . "..." . substr(DEEPSEEK_API_KEY,-6) . " len=" . strlen(DEEPSEEK_API_KEY) . "\n";
echo str_repeat("-",40)."\n";

// Test 1: Simple chat (tanpa json_object)
echo "\n=== TEST 1: Simple chat ===\n";
$data1 = [
    "model" => DEEPSEEK_MODEL,
    "messages" => [
        ["role" => "user", "content" => "Jawab hanya: OK"]
    ],
    "max_tokens" => 20,
    "stream" => false
];
$ch = curl_init(DEEPSEEK_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . DEEPSEEK_API_KEY,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data1, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response1 = curl_exec($ch);
$http1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err1 = curl_error($ch);
curl_close($ch);
echo "HTTP: $http1\n";
if($err1) echo "cURL Error: $err1\n";
echo "Response:\n" . substr($response1,0,2000) . "\n";

// Test 2: JSON mode seperti AiService
echo "\n=== TEST 2: JSON mode (seperti AiService, tanpa thinking) ===\n";
$data2 = [
    "model" => DEEPSEEK_MODEL,
    "messages" => [
        ["role" => "user", "content" => 'Buat JSON: {"jawaban":"OK"} Jawab HANYA JSON tersebut.']
    ],
    "max_tokens" => 100,
    "response_format" => ["type" => "json_object"],
    "stream" => false
];
$ch = curl_init(DEEPSEEK_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . DEEPSEEK_API_KEY,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data2, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response2 = curl_exec($ch);
$http2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err2 = curl_error($ch);
curl_close($ch);
echo "HTTP: $http2\n";
if($err2) echo "cURL Error: $err2\n";
echo "Response:\n" . substr($response2,0,3000) . "\n";
if($response2){
    $j = json_decode($response2,true);
    if(isset($j['choices'][0]['message']['content'])){
        echo "Content: " . $j['choices'][0]['message']['content'] . "\n";
        $inner = json_decode($j['choices'][0]['message']['content'], true);
        echo "Inner JSON valid? " . ($inner ? "YES" : "NO - ".json_last_error_msg()) . "\n";
    }
}

// Test 3: Dengan thinking (seperti kode lama)
echo "\n=== TEST 3: Dengan thinking disabled (kode lama) ===\n";
$data3 = [
    "model" => DEEPSEEK_MODEL,
    "messages" => [
        ["role" => "user", "content" => "Jawab hanya: OK dalam JSON {\"jawaban\":\"OK\"}"]
    ],
    "max_tokens" => 100,
    "response_format" => ["type" => "json_object"],
    "thinking" => ["type" => "disabled"],
    "stream" => false
];
$ch = curl_init(DEEPSEEK_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . DEEPSEEK_API_KEY,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data3, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response3 = curl_exec($ch);
$http3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err3 = curl_error($ch);
curl_close($ch);
echo "HTTP: $http3\n";
if($err3) echo "cURL Error: $err3\n";
echo "Response:\n" . substr($response3,0,3000) . "\n";

echo "\n=== SELESAI ===\n";
