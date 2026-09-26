<?php

namespace Export;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Language;

class WordExporter
{
    private PhpWord $phpWord;

    private $section;

    private array $modul = [];

    private const LABEL_WIDTH = 2200;

    private const VALUE_WIDTH = 6800;

    private const FULL_WIDTH = 9000;

    public function __construct()
    {
        $this->phpWord = new PhpWord();

        $this->setupDocument();

        $this->registerStyles();
    }

    private function setupDocument(): void
    {
        $this->phpWord->setDefaultFontName('Times New Roman');
        $this->phpWord->setDefaultFontSize(12);

        $this->section = $this->phpWord->addSection([
            'paperSize' => 'A4',

            'marginTop' => 900,

            'marginBottom' => 900,

            'marginLeft'  => 1500,

            'marginRight' => 1500,
        ]);
    }

    private function registerStyles(): void
    {
        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $this->phpWord->addTitleStyle(
            1,
            [
                'name' => 'Times New Roman',
                'size' => 16,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER,
                'spaceAfter' => 340
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SECTION HEADER
        |--------------------------------------------------------------------------
        */

        $this->phpWord->addTitleStyle(
            2,
            [
                'name' => 'Times New Roman',
                'size' => 14,
                'bold' => true,
                'color' => '071B45'
            ]
        );

        $this->phpWord->addParagraphStyle(
            'paragraph',
            [
                'alignment' => Jc::BOTH,
                'lineHeight' => 1.75,
                'spaceAfter' => 140
            ]
        );

        $this->phpWord->addParagraphStyle(
            'list',
            [
                'alignment' => Jc::BOTH,
                'left' => 420,
                'lineHeight' => 1.80,
                'spaceAfter' => 120
            ]
        );

        $this->phpWord->addParagraphStyle(
            'label',
            [
                'spaceBefore' => 120,
                'spaceAfter' => 60
            ]
        );

        $this->phpWord->addParagraphStyle(
            'headingBox',
            [
                'spaceBefore' => 260,
                'spaceAfter' => 180
            ]
        );

        $this->phpWord->addParagraphStyle(
            'signature',
            [
                'alignment' => Jc::CENTER
            ]
        );

    }

    /*
    |--------------------------------------------------------------------------
    | HELPER ENGINE
    |--------------------------------------------------------------------------
    */

    private function addTitle(string $text): void
    {
        $this->section->addTitle($text, 1);
    }

    private function addParagraph(string $text): void
    {
        $this->section->addText(
            trim($text),
            [
                'name' => 'Times New Roman',
                'size' => 12
            ],
            'paragraph'
        );
    }

    private function addLabel(string $text): void
    {
        $this->section->addText(
            $text,
            [
                'bold' => true,
                'name' => 'Times New Roman',
                'size' => 12,
                'color' => '0D2D62'
            ],
            'label'
        );
    }

    private function addLampiranSubHeading(string $text): void
    {
        $this->section->addText(
            $text,
            [
                'bold' => true,
                'name' => 'Times New Roman',
                'size' => 13
            ],
            [
                'spaceBefore' => 180,
                'spaceAfter' => 180
            ]
        );
    }

    private function addLampiranLabel(string $text): void
    {
        $this->section->addText(
            $text,
            [
                'bold' => true,
                'size' => 12,
                'name' => 'Times New Roman',
                'color' => '0D2D62'
            ],
            [
                'spaceBefore' => 120,
                'spaceAfter' => 60
            ]
        );
    }

    private function addHeading(string $text): void
    {
        $this->section->addTitle(
            $text,
            2
        );
    }

    private function createTable(): \PhpOffice\PhpWord\Element\Table
    {
        return $this->section->addTable([
            'width' => 100 * 50,
            'unit' => 'pct',

            'borderSize' => 6,
            'borderColor' => 'B8B8B8',

            'cellMargin' => 160
        ]);
    }

    private function createCell(
        $table,
        int $width,
        string $text,
        bool $bold = false,
        string $paragraphStyle = null,
        array $cellStyle = []
    )
    {
        return $table
            ->addCell($width, $cellStyle)
            ->addText(
                trim($text),
                [
                    'name' => 'Times New Roman',
                    'size' => 12,
                    'bold' => $bold
                ],
                $paragraphStyle
            );
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

        $this->addPageBreak();

        $this->renderLampiran();

        $this->renderTandaTangan();

        $writer = IOFactory::createWriter(
            $this->phpWord,
            'Word2007'
        );

        $writer->save($filename);
    }

    private function addHeader($table, string $title): void
    {
        $table->addRow();

        $this->createCell(
            $table,
            self::FULL_WIDTH,
            $title,
            true,
            null,
            [
                'gridSpan' => 2,
                'bgColor'  => 'D8E8F8'
            ]
        );
    }

    private function addRow(
        $table,
        string $label,
        string $value
    ): void
    {
        $table->addRow();

        $this->createCell(
            $table,
            self::LABEL_WIDTH,
            $label,
            true
        );

        $this->createCell(
            $table,
            self::VALUE_WIDTH,
            $value,
            false,
            'paragraph'
        );
    }

    private function addParagraphRow(
        $table,
        string $label,
        string $text
    ): void
    {
        $table->addRow();

        $this->createCell(
            $table,
            self::LABEL_WIDTH,
            $label,
            true
        );

        $cell = $table->addCell(self::VALUE_WIDTH);

        $paragraphs = preg_split("/\R+/", trim($text));

        foreach ($paragraphs as $paragraph) {

            if ($paragraph === '') {
                continue;
            }

            $cell->addText(
                $paragraph,
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                'paragraph'
            );

        }
    }

    private function addListRow(
        $table,
        string $label,
        array $items
    ): void
    {
        $table->addRow();

        $this->createCell(
            $table,
            self::LABEL_WIDTH,
            $label,
            true
        );

        $cell = $table->addCell(self::VALUE_WIDTH);

        foreach ($items as $item) {

            if (trim($item) === '') {
                continue;
            }

            $cell->addListItem(
                trim($item),
                0,
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                null,
                'list'
            );

        }
    }

    private function addNumberedRow(
        $table,
        string $label,
        array $items
    ): void
    {
        $table->addRow();

        $this->createCell(
            $table,
            self::LABEL_WIDTH,
            $label,
            true
        );

        $cell = $table->addCell(self::VALUE_WIDTH);

        foreach ($items as $index => $item) {

            if (trim($item) === '') {
                continue;
            }

            $cell->addText(
                ($index + 1) . '. ' . trim($item),
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                'paragraph'
            );
        }
    }

    private function renderKegiatan(
        $table,
        string $judul,
        string $durasi,
        array $items,
        array $prinsip = []
    ): void
    {
        $table->addRow();

        // Kolom kiri
        $cellLeft = $table->addCell(self::LABEL_WIDTH);

        $cellLeft->addText(
            strtoupper($judul),
            [
                'bold' => true,
                'size' => 12
            ]
        );

        $cellLeft->addText(
            '(' . $durasi . ')',
            [
                'bold' => true,
                'size' => 11
            ]
        );

        // Kolom kanan
        $cell = $table->addCell(self::VALUE_WIDTH);

        foreach ($items as $label => $isi) {

            $textRun = $cell->addTextRun('paragraph');

            $textRun->addText(
                '• ',
                ['bold' => true]
            );

            $textRun->addText(
                $label . ' : ',
                ['bold' => true]
            );

            $textRun->addText($isi);

        }

        if (!empty($prinsip)) {

            $cell->addText(
                'Prinsip Pembelajaran',
                [
                    'bold' => true,
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                'paragraph'
            );

            foreach ($prinsip as $label => $isi) {

            $textRun = $cell->addTextRun('paragraph');

            $textRun->addText(
                '• ',
                ['bold' => true]
            );

            $textRun->addText(
                match($label) {
                    'mindful' => 'Berkesadaran (Mindful)',
                    'meaningful' => 'Bermakna (Meaningful)',
                    'joyful' => 'Menggembirakan (Joyful)',
                    default => ucfirst($label)
                },
                [
                    'bold' => true,
                    'color' => '0D2D62',
                    'name' => 'Times New Roman',
                    'size' => 12
                ]
            );

            // isi turun ke baris berikutnya
            $cell->addText(
                $isi,
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                [
                    'left' => 420,
                    'spaceAfter' => 120,
                    'lineHeight' => 1.75
                ]
            );
            }
        }

    }

    private function renderSection(
        string $title,
        array $rows
    ): void
    {
        $table = $this->createTable();

        $this->addHeader(
            $table,
            $title
        );

        foreach ($rows as $row) {

            $this->addRow(
                $table,
                $row[0],
                $row[1] ?? ''
            );

        }

        $this->addSpace();
    }

    private function renderParagraphSection(
        string $title,
        array $rows
    ): void
    {
        $table = $this->createTable();

        $this->addHeader(
            $table,
            $title
        );

    foreach ($rows as $row) {

        $value = $row[1] ?? '';

        if (is_array($value)) {

            $this->addListRow(
                $table,
                $row[0],
                $value
            );

        } else {

            $this->addParagraphRow(
                $table,
                $row[0],
                $value
            );

        }

    }
        $this->addSpace();
    }

    private function renderListSection(
        string $title,
        array $rows
    ): void
    {
        $table = $this->createTable();

        $this->addHeader($table, $title);

        foreach ($rows as $row) {

            $this->addListRow(
                $table,
                $row[0],
                $row[1] ?? []
            );

        }

        $this->addSpace();
    }

    private function addSpace(int $height = 1): void
    {
        $this->section->addTextBreak($height);
    }

    private function addSeparator(): void
    {
        $this->section->addLine([
            'weight' => 1,
            'width'  => 450,
            'height' => 0,
            'color'  => 'D9D9D9'
        ]);
    }

    private function addPageBreak(): void
    {
        $this->section->addPageBreak();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER ENGINE
    |--------------------------------------------------------------------------
    */

    private function renderTitle(): void
    {
        $identitas = $this->modul['identitas'] ?? [];

        $mataPelajaran = $identitas['mata_pelajaran'] ?? '';

        $this->section->addText(
            'MODUL AJAR PEMBELAJARAN MENDALAM',
            [
                'name' => 'Times New Roman',
                'size' => 14,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER,
                'spaceAfter' => 100
            ]
        );

        $this->section->addText(
            'MATA PELAJARAN ' . strtoupper($mataPelajaran),
            [
                'name' => 'Times New Roman',
                'size' => 14,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER,
                'spaceAfter' => 80
            ]
        );

        $this->section->addText(
            'SMP NEGERI 9 PARIAMAN',
            [
                'name' => 'Times New Roman',
                'size' => 14,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER,
                'spaceAfter' => 300
            ]
        );
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

    private function renderPengalamanBelajar(): void
    {
        $pengalaman = $this->modul['pengalaman_belajar'] ?? [];

        $table = $this->createTable();

        $this->addHeader(
            $table,
            'C. PENGALAMAN BELAJAR'
        );

        /*---- KEGIATAN AWAL ----*/

        $this->renderKegiatan(
            $table,
            'Awal',
            $pengalaman['awal']['durasi'] ?? '',
            [
                'Pembukaan' => $pengalaman['awal']['pembukaan'] ?? '',
                'Apersepsi' => $pengalaman['awal']['apersepsi'] ?? '',
                'Motivasi dan Pengkondisian' => $pengalaman['awal']['motivasi'] ?? '',
            ],
            $pengalaman['awal']['prinsip_pembelajaran'] ?? []
        );

        /*---- KEGIATAN INTI ----*/

        $this->renderKegiatan(
            $table,
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

        /*---- KEGIATAN PENUTUP ----*/

        $this->renderKegiatan(
            $table,
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

        $this->addSpace();
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

    private function renderLampiran(): void
    {
        $this->addSeparator();

        $this->section->addText(
            'LAMPIRAN',
            [
                'bold'  => true,
                'size'  => 16,
                'name'  => 'Times New Roman',
                'color' => '0D2D62'
            ],
            [
                'alignment'   => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                'spaceBefore' => 180,
                'spaceAfter'  => 180
            ]
        );

        $this->addSeparator();

        $table = $this->createTable();

        $this->addHeader(
            $table,
            'A. ASESMEN'
        );

        $this->addSpace();

        $this->renderLampiranDiagnostik();

        $this->renderLampiranFormatif();

        $this->renderLampiranSumatif();

        $table = $this->createTable();

        $this->addHeader(
            $table,
            'B. PENGAYAAN DAN REMEDIAL'
        );

        $this->addSpace();

        $this->renderPengayaanRemedial();

        $table = $this->createTable();

        $this->addHeader(
            $table,
            'C. REFLEKSI'
        );

        $this->addSpace();

        $this->renderRefleksi();

    }

    private function renderRefleksiList(string $text): void
    {
        $text = preg_replace(
            '/\s+(\d+[\.\)])/',
            "\n$1",
            trim($text)
        );

        preg_match_all(
            '/\d+[\.\)]\s.*?(?=\n\d+[\.\)]|$)/s',
            $text,
            $matches
        );

        if (!empty($matches[0])) {

            foreach ($matches[0] as $item) {

                $this->section->addText(
                    trim($item),
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'left' => 420,
                        'lineHeight' => 1.75,
                        'spaceAfter' => 80
                    ]
                );

            }

            return;
        }

        $this->addParagraph($text);
    }

    private function renderLampiranDiagnostik(): void
    {
        $diagnostik = $this->modul['lampiran']['asesmen']['diagnostik'] ?? [];

        $this->addLampiranSubHeading('1. Asesmen Diagnostik');

        // Tujuan
        $this->addLampiranLabel('Tujuan');
        $this->addParagraph($diagnostik['tujuan'] ?? '');

        // Instrumen
        $this->addLampiranLabel('Instrumen');
        $this->addParagraph($diagnostik['instrumen'] ?? '');

        // Petunjuk Guru
        $this->addLampiranLabel('Petunjuk Guru');

        $petunjuk = trim($diagnostik['petunjuk'] ?? '');

        $petunjuk = preg_replace(
            '/\s+(\d+\.)/',
            "\n$1",
            $petunjuk
        );

        $lines = preg_split("/\R+/", $petunjuk);

        foreach ($lines as $line) {

            if (trim($line) == '') {
                continue;
            }

            $this->section->addText(
                trim($line),
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                [
                    'left' => 420,
                    'lineHeight' => 1.75,
                    'spaceAfter' => 80
                ]
            );

        }
        
        // Pertanyaan
        if (!empty($diagnostik['pertanyaan'])) {

            $this->addLampiranLabel('Pertanyaan');

            foreach ($diagnostik['pertanyaan'] as $i => $item) {

                $this->section->addText(
                    ($i + 1) . '. ' . trim($item),
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'left' => 420,
                        'lineHeight' => 1.75,
                        'spaceAfter' => 80
                    ]
                );

            }

        }

        // Kunci Jawaban
        if (!empty($diagnostik['kunci_jawaban'])) {

            $this->addLampiranLabel('Kunci Jawaban');

            $jawaban = preg_replace(
                '/\s+(\d+[\.\)])/',
                "\n$1",
                trim($diagnostik['kunci_jawaban'])
            );

            $lines = preg_split("/\R+/", $jawaban);

            foreach ($lines as $line) {

                if (trim($line) == '') {
                    continue;
                }

                $this->section->addText(
                    trim($line),
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'left' => 420,
                        'lineHeight' => 1.75,
                        'spaceAfter' => 80
                    ]
                );

            }

        }

        $this->addSpace();
    }

    private function renderLampiranFormatif(): void
    {
        $data = $this->modul['lampiran']['asesmen']['formatif'] ?? [];

        $this->addLampiranSubHeading(
            '2. Asesmen Formatif'
        );

        $this->addLampiranLabel('Tujuan');
        $this->addParagraph($data['tujuan'] ?? '');

        $this->addLampiranLabel('Instrumen');
        $this->addParagraph($data['instrumen'] ?? '');

        $this->addLampiranLabel('Petunjuk Guru');

        $petunjuk = trim($data['petunjuk'] ?? '');

        $petunjuk = preg_replace(
            '/\s+(\d+\.)/',
            "\n$1",
            $petunjuk
        );

        $lines = preg_split("/\R+/", $petunjuk);

        foreach ($lines as $line) {

            if (trim($line) == '') {
                continue;
            }

            $this->section->addText(
                trim($line),
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                [
                    'left' => 420,
                    'lineHeight' => 1.75,
                    'spaceAfter' => 80
                ]
            );
        }

        if (!empty($data['lkpd'])) {

            $this->addLampiranLabel(
                'Lembar Kerja Peserta Didik (LKPD)'
            );

        $lkpd = preg_split("/\R+/", trim($data['lkpd']));

        foreach ($lkpd as $baris) {

            if (trim($baris) == '') {
                continue;
            }

            $this->section->addText(
                trim($baris),
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                [
                    'left' => 420,
                    'lineHeight' => 1.75,
                    'spaceAfter' => 80
                ]
            );
        }
        }

        if (!empty($data['rubrik'])) {

            $this->addLampiranLabel(
                'Pedoman Penilaian'
            );

            $this->renderRubrikFormatif(
                $data['rubrik']
            );
        }

        $this->addSpace();
    }

    private function renderRubrikFormatif(array $rubrik): void
    {
        $table = $this->section->addTable([
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80
        ]);

        $header = [
            'bgColor' => 'CFE8FF'
        ];

        $bold = [
            'bold' => true,
            'size' => 10
        ];

        $cell = [
            'valign' => 'center'
        ];

        $table->addRow();

        $table->addCell(1800, $header)->addText('Aspek', $bold);
        $table->addCell(2600, $header)->addText('Indikator', $bold);
        $table->addCell(2300, $header)->addText('Sangat Baik', $bold);
        $table->addCell(2300, $header)->addText('Baik', $bold);
        $table->addCell(2300, $header)->addText('Cukup', $bold);
        $table->addCell(2300, $header)->addText('Perlu Bimbingan', $bold);

        foreach ($rubrik as $row) {

            $table->addRow();

            $table->addCell(1800, $cell)
                ->addText($row['aspek'] ?? '');

            $table->addCell(2600, $cell)
                ->addText($row['indikator'] ?? '');

            $table->addCell(2300, $cell)
                ->addText($row['sangat_baik'] ?? '');

            $table->addCell(2300, $cell)
                ->addText($row['baik'] ?? '');

            $table->addCell(2300, $cell)
                ->addText($row['cukup'] ?? '');

            $table->addCell(2300, $cell)
                ->addText($row['perlu_bimbingan'] ?? '');
        }

        $this->addSpace();
    }

    private function renderLampiranSumatif(): void
    {
        $data = $this->modul['lampiran']['asesmen']['sumatif'] ?? [];

        $this->addLampiranSubHeading(
            '3. Asesmen Sumatif'
        );

        // Tujuan
        $this->addLampiranLabel('Tujuan');
        $this->addParagraph($data['tujuan'] ?? '');

        // Instrumen
        $this->addLampiranLabel('Instrumen');
        $this->addParagraph($data['instrumen'] ?? '');

        // Petunjuk Guru
        $this->addLampiranLabel('Petunjuk Guru');

        $petunjuk = trim($data['petunjuk'] ?? '');

        $petunjuk = preg_replace(
            '/\s+(\d+\.)/',
            "\n$1",
            $petunjuk
        );

        $lines = preg_split("/\R+/", $petunjuk);

        foreach ($lines as $line) {

            if (trim($line) == '') {
                continue;
            }

            $this->section->addText(
                trim($line),
                [
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                [
                    'left' => 420,
                    'lineHeight' => 1.75,
                    'spaceAfter' => 80
                ]
            );
        }

        // Soal
        if (!empty($data['soal'])) {

            $this->addLampiranLabel('Soal');

            foreach ($data['soal'] as $i => $soal) {

                $this->section->addText(
                    ($i + 1) . '. ' . trim($soal),
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'left' => 420,
                        'lineHeight' => 1.75,
                        'spaceAfter' => 80
                    ]
                );
            }
        }

        // Kunci Jawaban
        if (!empty($data['kunci_jawaban'])) {

            $this->addLampiranLabel('Kunci Jawaban');

            $jawaban = preg_replace(
                '/\s+(\d+[\.\)])/',
                "\n$1",
                trim($data['kunci_jawaban'])
            );

            $lines = preg_split("/\R+/", $jawaban);

            foreach ($lines as $line) {

                if (trim($line) == '') {
                    continue;
                }

                $this->section->addText(
                    trim($line),
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'left' => 420,
                        'lineHeight' => 1.75,
                        'spaceAfter' => 80
                    ]
                );
            }
        }

    if (!empty($data['rubrik'])) {

        $this->addLampiranLabel(
            'Pedoman Penilaian'
        );

        $this->renderRubrikFormatif(
            $data['rubrik']
        );

    }

        $this->addSpace();
    }

    private function renderPengayaanRemedial(): void
    {
        $data = $this->modul['lampiran']['pengayaan_dan_remedial'] ?? [];

        // Pengayaan
        $this->addLampiranLabel('Pengayaan');

        $this->addParagraph(
            $data['pengayaan'] ?? ''
        );

        // Remedial
        $this->addLampiranLabel('Remedial');

        $this->addParagraph(
            $data['remedial'] ?? ''
        );

        $this->addSpace();
    }

    private function renderRefleksi(): void
    {
        $data = $this->modul['lampiran']['refleksi'] ?? [];

        // Refleksi Guru
        $this->addLampiranLabel('Refleksi Guru');

        if (!empty($data['guru'])) {

            $this->renderRefleksiList(
                $data['guru']
            );

        }

        // Refleksi Peserta Didik
        $this->addLampiranLabel('Refleksi Peserta Didik');

        if (!empty($data['peserta_didik'])) {

            $this->renderRefleksiList(
                $data['peserta_didik']
            );

        }

        $this->addSpace();
    }

    private function renderTandaTangan(): void
    {
        $identitas = $this->modul['identitas'] ?? [];

        $penyusun = $identitas['penyusun'] ?? '';
        $nip = $identitas['nip'] ?? '';

        $tanggal = date('d-m-Y');

        $this->section->addTextBreak(2);

        $table = $this->section->addTable([
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin'  => 0,
            'width'       => 100 * 50,
            'unit'        => 'pct'
        ]);

        $style = [
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
            'valign'      => 'center'
        ];

        /*
        |--------------------------------------------------------------------------
        | Baris 1
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(4500, $style)->addText(
            'Mengetahui,',
            [
                'name' => 'Times New Roman',
                'size' => 12
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        $table->addCell(4500, $style)->addText(
            'Pariaman, ' . $tanggal,
            [
                'name' => 'Times New Roman',
                'size' => 12
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Jabatan
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(4500, $style)->addText(
            'Kepala Sekolah',
            [
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        $table->addCell(4500, $style)->addText(
            'Guru Mata Pelajaran',
            [
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Ruang tanda tangan
        |--------------------------------------------------------------------------
        */

        $table->addRow(1500);

        $table->addCell(4500, $style);
        $table->addCell(4500, $style);

        /*
        |--------------------------------------------------------------------------
        | Nama
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(4500, $style)->addText(
            'Yanti Octavia, S.Kom',
            [
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true,
                'underline' => 'single'
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        $table->addCell(4500, $style)->addText(
            $penyusun,
            [
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true,
                'underline' => 'single'
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | NIP
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(4500, $style)->addText(
            'NIP. 197807032009012000',
            [
                'name' => 'Times New Roman',
                'size' => 12
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );

        $table->addCell(4500, $style)->addText(
            'NIP. ' . $nip,
            [
                'name' => 'Times New Roman',
                'size' => 12
            ],
            [
                'alignment' => Jc::CENTER
            ]
        );
    }




}

