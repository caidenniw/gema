<?php

require_once "config/config.php";

$prompt = "Jawab hanya: OK";

$data = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => $prompt
                ]
            ]
        ]
    ],
    "generationConfig" => [
        "maxOutputTokens" => 100
    ]
];

$ch = curl_init(GEMINI_URL);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "x-goog-api-key: " . GEMINI_API_KEY
]);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
);

curl_setopt($ch, CURLOPT_TIMEOUT, 120);

$response = curl_exec($ch);

if ($response === false) {
    die("cURL Error: " . curl_error($ch));
}

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);

echo "<h3>HTTP Code: $httpCode</h3>";

echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";