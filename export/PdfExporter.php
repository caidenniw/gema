<?php

namespace Export;
use Mpdf\Mpdf;
class PdfExporter
{
    private Mpdf $pdf;
    private array $modul = [];
    private const LABEL_WIDTH = '24%';
    private const FONT = 'Times New Roman';
    private const FONT_SIZE = '12pt';
    private const TITLE_SIZE = '16pt';
    private const SUBTITLE_SIZE = '13pt';
    private const LINE_HEIGHT = '1.75';
    private const COLOR = '#0D2D62';
    public function __construct()
    {
        $this->pdf = new Mpdf([
            'format' => 'A4',
            'margin_top'    => 16,
            'margin_bottom' => 16,
            'margin_left'   => 26,
            'margin_right'  => 26,
            'default_font' => 'times'
        ]);
    }

public function export(array $modul, string $filename): void
{
    $this->modul = $modul;
    $this->renderTitle();
    $this->renderIdentitas();
    $this->renderIdentifikasi();
    $this->renderDesainPembelajaran();
    $this->renderPengalamanBelajar();
    $this->renderAsesmenPembelajaran();
    $this->renderLampiran();

    $this->pdf->Output(
        $filename,
        \Mpdf\Output\Destination::FILE
    );
}

private function renderTitle(): void
{
    $identitas = $this->modul['identitas'] ?? [];

    $mataPelajaran = strtoupper(
        htmlspecialchars(
            $identitas['mata_pelajaran'] ?? ''
        )
    );

    $this->pdf->WriteHTML("
        <div style='
            font-family: Times New Roman;
            text-align:center;
            margin-bottom:20pt;
        '>

            <div style='
                font-size:14pt;
                font-weight:bold;
                margin-bottom:8pt;
            '>
                MODUL AJAR PEMBELAJARAN MENDALAM
            </div>

            <div style='
            font-family:Times New Roman;
            font-size:14pt;
            font-weight:bold;
            text-align:center;
            margin-bottom:8pt;
            '>
                MATA PELAJARAN {$mataPelajaran}
            </div>

            <div style='
                font-size:14pt;
                font-weight:bold;
            '>
                SMP NEGERI 9 PARIAMAN
            </div>

        </div>
    ");
}

private function renderSection(string $title, array $rows): void
{
    $html = '
    <table border="1"
           cellpadding="6"
           cellspacing="0"
           width="100%"
           style="
                border-collapse:collapse;
                border:1px solid #B8B8B8;
                margin-bottom:18px;
           ">
        <tr style="background:#D8E8F8;">
            <td colspan="2"
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    font-weight:bold;
                    padding:6px;
                ">
                '.$title.'
            </td>
        </tr>';

    foreach ($rows as $row) {

        $label = htmlspecialchars($row[0]);
        $value = nl2br(htmlspecialchars($row[1] ?? ''));
        $html .= '
        <tr>
            <td
                width="'.self::LABEL_WIDTH.'"
                valign="top"
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    font-weight:bold;
                ">
                '.$label.'
            </td>

            <td
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    line-height:1.75;
                    text-align:justify;
                ">
                '.$value.'
            </td>
        </tr>';
    }

    $html .= '</table>';
    $this->pdf->WriteHTML($html);
}

private function renderIdentitas(): void
{
    $identitas = $this->modul['identitas'] ?? [];
    $this->renderSection(
        'IDENTITAS',
        [
            ['Penyusun', $identitas['penyusun'] ?? ''],
            ['NIP', $identitas['nip'] ?? ''],
            ['Tahun Pelajaran', $identitas['tahun_pelajaran'] ?? ''],
            ['Semester', $identitas['semester'] ?? ''],
            ['Mata Pelajaran', $identitas['mata_pelajaran'] ?? ''],
            ['Kelas / Fase', $identitas['kelas_fase'] ?? ''],
            ['Topik Pembelajaran', $identitas['topik_pembelajaran'] ?? ''],
            ['Alokasi Waktu', $identitas['alokasi_waktu'] ?? ''],
        ]
    );
}

private function renderParagraphSection(string $title, array $rows): void
{
    $html = '
    <table border="1"
           cellpadding="6"
           cellspacing="0"
           width="100%"
           style="
                border-collapse:collapse;
                border:1px solid #B8B8B8;
                margin-bottom:18px;
           ">

        <tr style="background:#D8E8F8;">
            <td colspan="2"
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    font-weight:bold;
                    padding:6px;
                ">
                '.$title.'
            </td>
        </tr>';

    foreach ($rows as $row) {

        $label = htmlspecialchars($row[0]);
        $value = $row[1] ?? '';

        $html .= '
        <tr>
            <td
                width="'.self::LABEL_WIDTH.'"
                valign="top"
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    font-weight:bold;
                ">
                '.$label.'
            </td>

            <td
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    line-height:1.75;
                    text-align:justify;
                ">';

        if (is_array($value)) {
            $html .= '<ul style="margin:0;padding-left:18px;">';
            foreach ($value as $item) {
                if (trim($item) == '') {
                    continue;
                }
                $html .= '<li style="margin-bottom:6px;">'
                    . htmlspecialchars($item)
                    . '</li>';
            }
            $html .= '</ul>';
        } else {
            $paragraphs = preg_split("/\R+/", trim($value));
            foreach ($paragraphs as $paragraph) {
                if (trim($paragraph) == '') {
                    continue;
                }
                $html .= '
                <p style="
                    margin:0 0 8px;
                    text-align:justify;
                    line-height:1.75;
                ">
                    '.htmlspecialchars($paragraph).'
                </p>';
            }
        }
        $html .= '
            </td>
        </tr>';
    }
    $html .= '</table>';
    $this->pdf->WriteHTML($html);
}

private function renderIdentifikasi(): void
{
    $identifikasi = $this->modul['identifikasi'] ?? [];
    $this->renderParagraphSection(
        'A. IDENTIFIKASI',
        [
            [
                'Karakteristik Murid',
                $identifikasi['murid'] ?? ''
            ],
            [
                'Materi Pembelajaran',
                $identifikasi['materi_pelajaran'] ?? ''
            ],
            [
                'Dimensi Profil Lulusan',
                $identifikasi['dimensi_profil_lulusan'] ?? []
            ]
        ]
    );
}

private function renderDesainPembelajaran(): void
{
    $desain = $this->modul['desain_pembelajaran'] ?? [];

    $this->renderParagraphSection(
        'B. DESAIN PEMBELAJARAN',
        [

            [
                'Capaian Pembelajaran',
                $desain['capaian_pembelajaran'] ?? ''
            ],

            [
                'Lintas Disiplin Ilmu',
                $desain['lintas_disiplin_ilmu'] ?? ''
            ],

            [
                'Tujuan Pembelajaran',
                $desain['tujuan_pembelajaran'] ?? ''
            ],

            [
                'Topik Pembelajaran',
                $desain['topik_pembelajaran'] ?? ''
            ],

            [
                'Praktik Pedagogis',
                $desain['praktik_pedagogis'] ?? ''
            ],

            [
                'Metode Pembelajaran',
                $desain['metode_pembelajaran'] ?? []
            ],

            [
                'Lingkungan Pembelajaran',
                $desain['lingkungan_pembelajaran'] ?? ''
            ],

            [
                'Pemanfaatan Digital',
                $desain['pemanfaatan_digital'] ?? []
            ],

            [
                'Kemitraan Pembelajaran',
                $desain['kemitraan_pembelajaran'] ?? ''
            ]

        ]
    );
}

private function renderKegiatan(
    string $judul,
    string $durasi,
    array $items,
    array $prinsip = []
): void
{
    $html = '
    <table border="1"
           cellpadding="6"
           cellspacing="0"
           width="100%"
           style="
                border-collapse:collapse;
                border:1px solid #B8B8B8;
                margin-bottom:18px;
           ">

        <tr>

            <td width="'.self::LABEL_WIDTH.'"
                valign="top"
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    font-weight:bold;
                ">

                '.strtoupper($judul).'<br>
                ('.$durasi.')

            </td>

            <td
                style="
                    font-family:Times New Roman;
                    font-size:12pt;
                    line-height:1.75;
                    text-align:justify;
                ">';
    foreach ($items as $label => $isi) {

        if (trim($isi) == '') {
            continue;
        }

        $html .= '

        <p style="
            margin:0 0 8px;
            line-height:1.75;
        ">

            <b>• '.$label.' :</b>

            '.nl2br(htmlspecialchars($isi)).'

        </p>';
    }
    
    if (!empty($prinsip)) {

        $html .= '

        <p style="
            margin-top:12px;
            margin-bottom:8px;
        ">
            <b>Prinsip Pembelajaran</b>
        </p>';

        foreach ($prinsip as $label => $isi) {

            $nama = match ($label) {

                'mindful' => 'Berkesadaran (Mindful)',

                'meaningful' => 'Bermakna (Meaningful)',

                'joyful' => 'Menggembirakan (Joyful)',

                default => ucfirst($label)

            };

            $html .= '

            <p style="
                margin:0 0 6px;
                line-height:1.75;
            ">
                <span style="
                    color:#0D2D62;
                    font-weight:bold;
                ">
                    • '.$nama.'
                </span>
                <br>
                '.nl2br(htmlspecialchars($isi)).'

            </p>';

        }

    }

    $html .= '

            </td>

        </tr>

    </table>';

    $this->pdf->WriteHTML($html);
}

private function renderPengalamanBelajar(): void
{
    $pengalaman = $this->modul['pengalaman_belajar'] ?? [];

    $this->renderSection(
        'C. PENGALAMAN BELAJAR',
        []
    );

    // KEGIATAN AWAL
    $this->renderKegiatan(
        'Awal',
        $pengalaman['awal']['durasi'] ?? '',
        [
            'Pembukaan' => $pengalaman['awal']['pembukaan'] ?? '',
            'Apersepsi' => $pengalaman['awal']['apersepsi'] ?? '',
            'Motivasi dan Pengkondisian' => $pengalaman['awal']['motivasi'] ?? '',
        ],
        $pengalaman['awal']['prinsip_pembelajaran'] ?? []
    );

    // KEGIATAN INTI
    $this->renderKegiatan(
        'Inti',
        $pengalaman['inti']['durasi'] ?? '',
        [
            'Memahami' => $pengalaman['inti']['memahami'] ?? '',
            'Mengorganisasi Belajar' => $pengalaman['inti']['mengorganisasi_belajar'] ?? '',
            'Mengaplikasikan' => $pengalaman['inti']['mengaplikasikan'] ?? '',
            'Merefleksi' => $pengalaman['inti']['merefleksi'] ?? '',
        ],
        $pengalaman['inti']['prinsip_pembelajaran'] ?? []
    );

    // KEGIATAN PENUTUP
    $this->renderKegiatan(
        'Penutup',
        $pengalaman['penutup']['durasi'] ?? '',
        [
            'Kesimpulan' => $pengalaman['penutup']['kesimpulan'] ?? '',
            'Umpan Balik' => $pengalaman['penutup']['umpan_balik'] ?? '',
            'Rencana Lanjutan' => $pengalaman['penutup']['rencana_lanjutan'] ?? '',
            'Penutup' => $pengalaman['penutup']['penutup'] ?? '',
        ],
        $pengalaman['penutup']['prinsip_pembelajaran'] ?? []
    );
}

private function renderAsesmenPembelajaran(): void
{
    $asesmen = $this->modul['asesmen'] ?? [];

    $this->renderSection(
        'D. ASESMEN PEMBELAJARAN',
        [

            [
                'Asesmen Diagnostik',
                ($asesmen['asesmen_diagnostik']['teknik'] ?? '') . ' (Terlampir)'
            ],

            [
                'Asesmen Formatif',
                ($asesmen['asesmen_formatif']['teknik'] ?? '') . ' (Terlampir)'
            ],

            [
                'Asesmen Sumatif',
                ($asesmen['asesmen_sumatif']['teknik'] ?? '') . ' (Terlampir)'
            ]

        ]
    );
}

private function addSeparator(): void
{
    $this->pdf->WriteHTML('
        <hr style="
            border:0;
            border-top:1px solid #D9D9D9;
            margin:12px 0;
        ">
    ');
}

private function addLampiranSubHeading(string $text): void
{
    $this->pdf->WriteHTML("
        <div style='
            font-family:".self::FONT.";
            font-size:".self::SUBTITLE_SIZE.";
            font-weight:bold;
            margin-top:14pt;
            margin-bottom:6pt;
        '>
            ".htmlspecialchars($text)."
        </div>
    ");
}

private function addLampiranLabel(string $text): void
{
    $this->pdf->WriteHTML("
        <div style='
            font-family:".self::FONT.";
            font-size:".self::FONT_SIZE.";
            font-weight:bold;
            color:".self::COLOR.";
            margin-top:8pt;
            margin-bottom:2pt;
        '>
            ".htmlspecialchars($text)."
        </div>
    ");
}

private function addParagraph(string $text): void
{
    if (trim($text) === '') {
        return;
    }

    $paragraphs = preg_split("/\R+/", trim($text));

    foreach ($paragraphs as $paragraph) {

        if (trim($paragraph) === '') {
            continue;
        }

        $this->pdf->WriteHTML("
            <div style='
                font-family:".self::FONT.";
                font-size:".self::FONT_SIZE.";
                text-align:justify;
                line-height:".self::LINE_HEIGHT.";
                margin-bottom:6pt;
            '>
                ".nl2br(htmlspecialchars($paragraph))."
            </div>
        ");
    }
}

private function renderLampiran(): void
{
    $this->pdf->AddPage();

    $this->addSeparator();

    $this->pdf->WriteHTML("
        <h2 style='
            text-align:center;
            font-family:Times New Roman;
            font-size:16pt;
            font-weight:bold;
            color:#0D2D62;
            margin:8px 0;
        '>
            LAMPIRAN
        </h2>
    ");

    $this->addSeparator();

    $this->renderSection(
        'A. ASESMEN',
        []
    );

    $this->renderLampiranDiagnostik();

    $this->renderLampiranFormatif();

    $this->renderLampiranSumatif();

    $this->renderSection(
        'B. PENGAYAAN DAN REMEDIAL',
        []
    );

    $this->renderPengayaanRemedial();

    $this->renderSection(
        'C. REFLEKSI',
        []
    );

    $this->renderRefleksi();

    $this->renderTandaTangan();
}

private function renderLampiranDiagnostik(): void
{
    $diagnostik = $this->modul['lampiran']['asesmen']['diagnostik'] ?? [];

    $this->addLampiranSubHeading(
        '1. Asesmen Diagnostik'
    );

    // Tujuan
    $this->addLampiranLabel('Tujuan');
    $this->addParagraph(
        $diagnostik['tujuan'] ?? ''
    );

    // Instrumen
    $this->addLampiranLabel('Instrumen');

    $instrumen = $diagnostik['instrumen'] ?? '';

    if (is_array($instrumen)) {

        foreach ($instrumen as $item) {

            $this->addParagraph($item);

        }

    } else {

        $this->addParagraph($instrumen);

    }

    // Petunjuk Guru
    $this->addLampiranLabel('Petunjuk Guru');
    $this->addParagraph(
        $diagnostik['petunjuk'] ?? ''
    );

    $this->addLampiranLabel('Pertanyaan');

    $pertanyaan = $diagnostik['pertanyaan'] ?? [];

    if (is_array($pertanyaan)) {

        foreach ($pertanyaan as $i => $item) {

            $this->addParagraph(
                ($i + 1) . '. ' . $item
            );

        }

    } else {

        $this->addParagraph($pertanyaan);

    }
    $this->addLampiranLabel('Kunci Jawaban');

    $kunci = $diagnostik['kunci_jawaban'] ?? [];

    if (is_array($kunci)) {

        foreach ($kunci as $i => $item) {

            $this->addParagraph(
                ($i + 1) . ') ' . $item
            );

        }

    } else {

        $this->addParagraph($kunci);

    }
}

private function renderLampiranFormatif(): void
{
    $formatif = $this->modul['lampiran']['asesmen']['formatif'] ?? [];

    $this->addLampiranSubHeading(
        '2. Asesmen Formatif'
    );

    // Tujuan
    $this->addLampiranLabel('Tujuan');
    $this->addParagraph(
        $formatif['tujuan'] ?? ''
    );

    // Instrumen
    $this->addLampiranLabel('Instrumen');

    $instrumen = $formatif['instrumen'] ?? '';

    if (is_array($instrumen)) {

        foreach ($instrumen as $item) {

            $this->addParagraph($item);

        }

    } else {

        $this->addParagraph($instrumen);

    }

    // Petunjuk Guru
    $this->addLampiranLabel('Petunjuk Guru');
    $this->addParagraph(
        $formatif['petunjuk'] ?? ''
    );

    $this->addLampiranLabel('LKPD');
    $this->addParagraph(
    $formatif['lkpd'] ?? ''
    );

    $this->addLampiranLabel('Rubrik Penilaian');

    $rubrik = $formatif['rubrik'] ?? [];
    $this->renderRubrikFormatif($rubrik);
}

private function renderLampiranSumatif(): void
{
    $sumatif = $this->modul['lampiran']['asesmen']['sumatif'] ?? [];

    $this->addLampiranSubHeading(
        '3. Asesmen Sumatif'
    );

    // Tujuan
    $this->addLampiranLabel('Tujuan');
    $this->addParagraph(
        $sumatif['tujuan'] ?? ''
    );

    // Instrumen
    $this->addLampiranLabel('Instrumen');

    $instrumen = $sumatif['instrumen'] ?? '';

    if (is_array($instrumen)) {

        foreach ($instrumen as $item) {

            $this->addParagraph($item);

        }

    } else {

        $this->addParagraph($instrumen);

    }

    // Petunjuk Guru
    $this->addLampiranLabel('Petunjuk Guru');
    $this->addParagraph(
        $sumatif['petunjuk'] ?? ''
    );

    // Soal
    $this->addLampiranLabel('Soal');

    $soal = $sumatif['soal'] ?? [];

    if (is_array($soal)) {

        foreach ($soal as $i => $item) {

            $this->addParagraph(
                ($i + 1) . '. ' . $item
            );

        }

    } else {

        $this->addParagraph($soal);

    }

    // Kunci Jawaban
    $this->addLampiranLabel('Kunci Jawaban');

    $this->addParagraph(
        $sumatif['kunci_jawaban'] ?? ''
    );

    // Rubrik Penilaian
    $this->addLampiranLabel('Rubrik Penilaian');

    $rubrik = $sumatif['rubrik'] ?? [];

    $this->renderRubrikFormatif($rubrik);
}

private function renderRubrikFormatif(array $rubrik): void
{
    $html = '
    <table border="1"
           cellpadding="4"
           cellspacing="0"
           width="100%"
           style="
                border-collapse:collapse;
                border:1px solid #999999;
                font-family:Times New Roman;
                font-size:10pt;
                margin-bottom:10pt;
           ">

        <tr style="background:#CFE8FF;font-weight:bold;text-align:center;">

            <td width="15%"><b>Aspek</b></td>
            <td width="20%"><b>Indikator</b></td>
            <td width="16%"><b>Sangat Baik</b></td>
            <td width="16%"><b>Baik</b></td>
            <td width="16%"><b>Cukup</b></td>
            <td width="17%"><b>Perlu Bimbingan</b></td>

        </tr>';

    foreach ($rubrik as $row) {

        $html .= '

        <tr>

            <td valign="top">'.htmlspecialchars($row['aspek'] ?? '').'</td>

            <td valign="top">'.htmlspecialchars($row['indikator'] ?? '').'</td>

            <td valign="top">'.htmlspecialchars($row['sangat_baik'] ?? '').'</td>

            <td valign="top">'.htmlspecialchars($row['baik'] ?? '').'</td>

            <td valign="top">'.htmlspecialchars($row['cukup'] ?? '').'</td>

            <td valign="top">'.htmlspecialchars($row['perlu_bimbingan'] ?? '').'</td>

        </tr>';

    }

    $html .= '</table>';

    $this->pdf->WriteHTML($html);
}

private function renderPengayaanRemedial(): void
{
    $data = $this->modul['lampiran']['pengayaan_dan_remedial'] ?? [];

    $this->addLampiranLabel('Pengayaan');

    $this->addParagraph(
        $data['pengayaan'] ?? ''
    );

    $this->addLampiranLabel('Remedial');

    $this->addParagraph(
        $data['remedial'] ?? ''
    );
}

private function renderRefleksi(): void
{
    $refleksi = $this->modul['lampiran']['refleksi'] ?? [];

    $this->addLampiranLabel('Refleksi Guru');

    $this->addParagraph(
        $refleksi['guru'] ?? ''
    );

    $this->addLampiranLabel('Refleksi Peserta Didik');

    $this->addParagraph(
        $refleksi['peserta_didik'] ?? ''
    );
}

private function renderTandaTangan(): void
{
    $identitas = $this->modul['identitas'] ?? [];

    $penyusun = htmlspecialchars(
        $identitas['penyusun'] ?? ''
    );

    $nip = htmlspecialchars(
        $identitas['nip'] ?? ''
    );

    $tanggal = date('d-m-Y');

    $html = '
    <table width="100%" style="
        margin-top:30pt;
        font-family:Times New Roman;
        font-size:12pt;
    ">

        <tr>

            <td width="50%" align="center">
                Mengetahui,<br><br>
                Kepala Sekolah
            </td>

            <td width="50%" align="center">
                Pariaman, '.$tanggal.'<br><br>
                Guru Mata Pelajaran
            </td>

        </tr>

        <tr>
            <td height="90"></td>
            <td></td>
        </tr>

        <tr>

            <td align="center">

                <b style="
                    border-bottom:1px solid #000;
                    padding-bottom:2pt;
                ">
                    Yanti Octavia, S.Kom
                </b>

            </td>

            <td align="center">

                <b style="
                    border-bottom:1px solid #000;
                    padding-bottom:2pt;
                ">
                    '.$penyusun.'
                </b>

            </td>

        </tr>

        <tr>

            <td align="center">
                NIP. 197807032009012000
            </td>

            <td align="center">
                NIP. '.$nip.'
            </td>

        </tr>

    </table>';

    $this->pdf->WriteHTML($html);
}

}