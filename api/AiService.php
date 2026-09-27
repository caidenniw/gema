<?php

require_once "../config/config.php";

class AiService
{
    public function generate($prompt)
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {

            $data = [
                "model" => DEEPSEEK_MODEL,

                "messages" => [
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],

                // Tetap seperti sistem lama
                "max_tokens" => 12000,

                // Output harus JSON
                "response_format" => [
                    "type" => "json_object"
                ],

                "stream" => false
            ];

            $json = json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
            );

            if ($json === false) {
                die(
                    "Gagal membuat JSON request: "
                    . json_last_error_msg()
                );
            }

            $ch = curl_init(DEEPSEEK_URL);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . DEEPSEEK_API_KEY,
                "Content-Type: application/json"
            ]);

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                $json
            );

            curl_setopt(
                $ch,
                CURLOPT_TIMEOUT,
                300
            );

            $response = curl_exec($ch);

            if ($response === false) {

                $error = curl_error($ch);

                curl_close($ch);

                if ($attempt < $maxAttempts) {
                    sleep($attempt);
                    continue;
                }

                die("cURL Error: " . $error);
            }

            $httpCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

            curl_close($ch);

            /*
             * ========================================
             * BERHASIL
             * ========================================
             */
            if ($httpCode >= 200 && $httpCode < 300) {

                $result = json_decode(
                    $response,
                    true
                );

                if ($result === null) {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    die(
                        "Response DeepSeek bukan JSON yang valid."
                    );
                }

                if (
                    !isset(
                        $result['choices'][0]['message']['content']
                    )
                ) {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    echo "<h3>DeepSeek tidak mengembalikan jawaban.</h3>";

                    echo "<pre>";
                    echo htmlspecialchars($response);
                    echo "</pre>";

                    exit;
                }

                $content =
                    $result['choices'][0]['message']['content'];

                $content = trim($content);

                /*
                 * DeepSeek mendokumentasikan bahwa JSON Output
                 * pada kondisi tertentu dapat menghasilkan
                 * content kosong, jadi jangan langsung simpan.
                 */
                if ($content === '') {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    echo "<h3>AI tidak mengembalikan isi modul.</h3>";

                    echo "<pre>";
                    echo htmlspecialchars($response);
                    echo "</pre>";

                    exit;
                }

                /*
                 * Bersihkan code block bila ada.
                 */
                $content = preg_replace(
                    '/^```json\s*/i',
                    '',
                    $content
                );

                $content = preg_replace(
                    '/\s*```$/',
                    '',
                    $content
                );

                $content = trim($content);

                /*
                 * JSON → Array PHP
                 */
                $module = json_decode(
                    $content,
                    true
                );

                if ($module === null) {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    echo "<h3>JSON AI Tidak Valid</h3>";

                    echo "<b>Error:</b> "
                        . htmlspecialchars(
                            json_last_error_msg()
                        );

                    echo "<hr>";

                    echo "<pre>";
                    echo htmlspecialchars($content);
                    echo "</pre>";

                    exit;
                }

                /*
                 * ========================================
                 * VALIDASI STRUKTUR GEMA AI
                 * ========================================
                 */
                $requiredFields = [
                    'identitas',
                    'identifikasi',
                    'desain_pembelajaran',
                    'pengalaman_belajar',
                    'asesmen',
                    'lampiran'
                ];

                $strukturLengkap = true;

                foreach (
                    $requiredFields as $field
                ) {
                    if (
                        !array_key_exists(
                            $field,
                            $module
                        )
                    ) {
                        $strukturLengkap = false;
                        break;
                    }
                }

                if (!$strukturLengkap) {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    die(
                        "Struktur modul tidak lengkap."
                    );
                }

                /*
                 * ========================================
                 * VALIDASI BAGIAN INTI
                 * ========================================
                 *
                 * Ini untuk mencegah kasus seperti:
                 * mengaplikasikan = ""
                 */
                $intiFields = [
                    'memahami',
                    'mengorganisasi_belajar',
                    'mengaplikasikan',
                    'merefleksi'
                ];

                $intiLengkap = true;

                foreach (
                    $intiFields as $field
                ) {

                    if (
                        !isset(
                            $module['pengalaman_belajar']
                                ['inti']
                                [$field]
                        )
                        ||
                        trim(
                            (string)
                            $module['pengalaman_belajar']
                                ['inti']
                                [$field]
                        ) === ''
                    ) {
                        $intiLengkap = false;
                        break;
                    }
                }

                /*
                 * Kalau ada field inti kosong,
                 * jangan diterima sebagai hasil final.
                 */
                if (!$intiLengkap) {

                    if ($attempt < $maxAttempts) {
                        sleep($attempt);
                        continue;
                    }

                    echo "<h3>Modul memiliki bagian yang kosong.</h3>";
                    echo "<p>Silakan generate kembali.</p>";

                    echo "<pre>";
                    echo htmlspecialchars(
                        json_encode(
                            $module,
                            JSON_PRETTY_PRINT |
                            JSON_UNESCAPED_UNICODE
                        )
                    );
                    echo "</pre>";

                    exit;
                }

                /*
                 * Semua valid
                 */
                return $module;
            }

            /*
             * ========================================
             * RETRY ERROR SEMENTARA
             * ========================================
             *
             * 429 = rate/concurrency
             * 500 = server error
             * 503 = server overloaded
             */
            if (
                in_array(
                    $httpCode,
                    [429, 500, 503],
                    true
                )
                &&
                $attempt < $maxAttempts
            ) {

                // 1 detik → 2 detik
                sleep(
                    pow(2, $attempt - 1)
                );

                continue;
            }

            /*
             * ========================================
             * ERROR AKHIR
             * ========================================
             */
            echo "<h3>DeepSeek API Error</h3>";

            echo "<b>HTTP Code:</b> "
                . htmlspecialchars(
                    (string) $httpCode
                );

            echo "<br><br>";

            echo "<pre>";
            echo htmlspecialchars(
                $response
            );
            echo "</pre>";

            exit;
        }

        die(
            "Gagal menghasilkan modul."
        );
    }
}