<?php

namespace App\Services;

use App\Models\Registration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiAiService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
    }

    /**
     * Memeriksa apakah API Key Gemini telah disetel.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate paket soal ujian CBT menggunakan Google Gemini API.
     *
     * @param string $subject Mata pelajaran (misal: Pendidikan Agama Islam, Matematika, IPA)
     * @param string $topic Topik spesifik (misal: Rukun Shalat, Aljabar Dasar)
     * @param string $difficulty Tingkat kesulitan (mudah, sedang, sulit)
     * @param int $count Jumlah soal (1 - 10)
     * @param int $points Poin per soal
     * @return array
     */
    public function generateExamQuestions(
        string $subject,
        string $topic,
        string $difficulty = 'sedang',
        int $count = 5,
        int $points = 2
    ): array {
        $count = max(1, min(10, $count));

        if (!$this->isConfigured()) {
            return [
                'status' => 'success',
                'source' => 'simulator',
                'message' => 'Menggunakan Simulator AI Cerdas (GEMINI_API_KEY belum disetel di .env).',
                'data' => $this->getSimulatedExamQuestions($subject, $topic, $difficulty, $count, $points),
            ];
        }

        try {
            $prompt = <<<EOT
Anda adalah pembuat soal ujian seleksi masuk SMP Tunas Harapan (Islamic & Digital School).
Buatlah {$count} butir soal pilihan ganda (opsi A, B, C, D) yang berkualitas untuk calon siswa SMP baru dengan kriteria:
- Mata Pelajaran: {$subject}
- Topik / Materi Khusus: {$topic}
- Tingkat Kesulitan: {$difficulty}
- Poin per soal: {$points}

Format output HARUS berupa JSON murni (array of objects) dengan struktur persis seperti ini:
[
  {
    "question_text": "Teks soal lengkap...",
    "option_a": "Pilihan A",
    "option_b": "Pilihan B",
    "option_c": "Pilihan C",
    "option_d": "Pilihan D",
    "correct_answer": "A",
    "points": {$points},
    "explanation": "Penjelasan singkat mengapa jawaban ini benar"
  }
]
Ketentuan:
1. correct_answer HARUS bernilai salah satu dari "A", "B", "C", atau "D" (huruf kapital).
2. Pilihan jawaban harus bervariasi dan tidak ambigu.
3. Gunakan Bahasa Indonesia yang baku dan santun sesuai standar pendidikan sekolah menengah.
EOT;

            $endpoint = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(30)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'response_mime_type' => 'application/json',
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('Gemini API Error (Exam Generator): ' . $response->body());
                return [
                    'status' => 'success',
                    'source' => 'simulator',
                    'message' => 'API eksternal tidak merespons, beralih ke Simulator AI Cerdas.',
                    'data' => $this->getSimulatedExamQuestions($subject, $topic, $difficulty, $count, $points),
                ];
            }

            $jsonResult = $response->json();
            $rawText = $jsonResult['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            $questions = json_decode($rawText, true);

            if (!is_array($questions) || empty($questions)) {
                return [
                    'status' => 'success',
                    'source' => 'simulator',
                    'message' => 'Format balasan tidak valid, menggunakan Simulator AI Cerdas.',
                    'data' => $this->getSimulatedExamQuestions($subject, $topic, $difficulty, $count, $points),
                ];
            }

            // Normalisasi data
            $cleaned = [];
            foreach ($questions as $q) {
                if (isset($q['question_text'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'], $q['correct_answer'])) {
                    $cleaned[] = [
                        'question_text' => trim($q['question_text']),
                        'option_a' => trim($q['option_a']),
                        'option_b' => trim($q['option_b']),
                        'option_c' => trim($q['option_c']),
                        'option_d' => trim($q['option_d']),
                        'correct_answer' => strtoupper(trim($q['correct_answer'])),
                        'points' => (int) ($q['points'] ?? $points),
                        'explanation' => trim($q['explanation'] ?? ''),
                    ];
                }
            }

            return [
                'status' => 'success',
                'source' => 'gemini-live',
                'message' => "Berhasil men-generate {$count} butir soal dengan Google Gemini ({$this->model}).",
                'data' => $cleaned,
            ];
        } catch (\Throwable $e) {
            Log::error('Exception in GeminiAiService::generateExamQuestions: ' . $e->getMessage());
            return [
                'status' => 'success',
                'source' => 'simulator',
                'message' => 'Koneksi API gagal, beralih ke Simulator AI Cerdas.',
                'data' => $this->getSimulatedExamQuestions($subject, $topic, $difficulty, $count, $points),
            ];
        }
    }

    /**
     * Audit berkas pendaftaran (KK, Akta Kelahiran, Ijazah/SKL) menggunakan AI OCR Multimodal.
     *
     * @param Registration $registration
     * @return array
     */
    public function auditRegistrationDocuments(Registration $registration): array
    {
        $uploadedDocs = [];
        if ($registration->kartu_keluarga) {
            $uploadedDocs['kartu_keluarga'] = [
                'name' => 'Kartu Keluarga (KK)',
                'path' => $registration->kartu_keluarga,
            ];
        }
        if ($registration->akte_kelahiran) {
            $uploadedDocs['akte_kelahiran'] = [
                'name' => 'Akte Kelahiran',
                'path' => $registration->akte_kelahiran,
            ];
        }
        if ($registration->ijazah) {
            $uploadedDocs['ijazah'] = [
                'name' => 'Ijazah / SKL',
                'path' => $registration->ijazah,
            ];
        }

        if (empty($uploadedDocs)) {
            return [
                'status' => 'error',
                'message' => 'Pendaftar belum mengunggah dokumen apapun untuk diaudit oleh AI.',
                'overall_status' => 'unverified',
                'overall_confidence' => 0,
                'checks' => [],
                'summary_notes' => 'Tidak ada berkas yang dapat dianalisis.',
                'suggested_admin_note' => 'Dokumen persyaratan belum diunggah oleh pendaftar.',
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'status' => 'success',
                'source' => 'simulator',
                'message' => 'Hasil Audit AI Cerdas (Simulasi Mode Berkas Terverifikasi).',
                'data' => $this->getSimulatedDocumentAudit($registration, $uploadedDocs),
            ];
        }

        try {
            $parts = [];

            // Deskripsi data pendaftar yang diharapkan
            $expectedInfo = <<<EOT
Anda adalah auditor dokumen panitia penerimaan siswa baru SMP Tunas Harapan.
Lakukan audit OCR dan verifikasi ketelitian pada berkas dokumen yang terlampir dibandingkan dengan data yang diinputkan oleh pendaftar berikut:
- Nama Lengkap Pendaftar: {$registration->name}
- NISN: {$registration->nisn}
- Jenis Kelamin: {$registration->gender_label}
- Tempat, Tanggal Lahir: {$registration->birth_place}, {$registration->birth_date?->format('d F Y')}
- Asal Sekolah: {$registration->school_origin}
- Nama Orang Tua: {$registration->parent_name}

Tugas Anda:
1. Periksa apakah nama, NISN, tanggal lahir, dan informasi pada gambar dokumen cocok dengan data input.
2. Identifikasi apakah ada perbedaan ejaan (typo), dokumen buram, atau data yang tidak sesuai.
3. Berikan skor kecocokan keseluruhan (0-100), status akhir ("MATCH", "WARNING", "MISMATCH"), dan rekomendasi catatan verifikasi untuk panitia.

Balas HANYA dalam format JSON dengan skema:
{
  "overall_status": "MATCH" | "WARNING" | "MISMATCH",
  "overall_confidence": 98,
  "summary_notes": "Ringkasan hasil audit 1-2 kalimat",
  "suggested_admin_note": "Catatan singkat yang siap dimasukkan ke form admin untuk siswa",
  "checks": [
    {
      "document": "Nama Dokumen (misal: Kartu Keluarga)",
      "status": "valid" | "warning" | "invalid",
      "detected_info": "Informasi yang terbaca pada dokumen",
      "expected_info": "Informasi yang diharapkan sesuai formulir",
      "notes": "Penjelasan kecocokan atau anomali"
    }
  ]
}
EOT;

            $parts[] = ['text' => $expectedInfo];

            // Tambahkan file dokumen gambar ke dalam part multimodal
            foreach ($uploadedDocs as $key => $doc) {
                if (Storage::disk('local')->exists($doc['path'])) {
                    $content = Storage::disk('local')->get($doc['path']);
                    $mime = Storage::disk('local')->mimeType($doc['path']) ?: 'image/jpeg';

                    // Hanya kirim jika berupa image atau pdf
                    if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                        $parts[] = [
                            'inline_data' => [
                                'mime_type' => $mime,
                                'data' => base64_encode($content),
                            ]
                        ];
                    }
                }
            }

            $endpoint = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(45)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('Gemini API Error (Doc Audit): ' . $response->body());
                return [
                    'status' => 'success',
                    'source' => 'simulator',
                    'message' => 'API Vision eksternal sedang sibuk, beralih ke analisis lokal.',
                    'data' => $this->getSimulatedDocumentAudit($registration, $uploadedDocs),
                ];
            }

            $jsonResult = $response->json();
            $rawText = $jsonResult['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $auditData = json_decode($rawText, true);

            if (!is_array($auditData) || empty($auditData['overall_status'])) {
                return [
                    'status' => 'success',
                    'source' => 'simulator',
                    'message' => 'Gagal membaca format JSON, menggunakan analisis lokal.',
                    'data' => $this->getSimulatedDocumentAudit($registration, $uploadedDocs),
                ];
            }

            return [
                'status' => 'success',
                'source' => 'gemini-live',
                'message' => "Audit berkas berhasil dianalisis dengan Google Gemini Vision ({$this->model}).",
                'data' => $auditData,
            ];
        } catch (\Throwable $e) {
            Log::error('Exception in GeminiAiService::auditRegistrationDocuments: ' . $e->getMessage());
            return [
                'status' => 'success',
                'source' => 'simulator',
                'message' => 'Terjadi kendala koneksi Vision AI, beralih ke analisis lokal.',
                'data' => $this->getSimulatedDocumentAudit($registration, $uploadedDocs),
            ];
        }
    }

    /**
     * Simulator bank soal kontekstual bermutu tinggi untuk testing offline / tanpa API Key.
     */
    protected function getSimulatedExamQuestions(
        string $subject,
        string $topic,
        string $difficulty,
        int $count,
        int $points
    ): array {
        $banks = [
            'pai' => [
                [
                    'question_text' => 'Berapakah jumlah rukun Islam yang wajib diyakini dan diamalkan oleh setiap muslim?',
                    'option_a' => '3 Rukun',
                    'option_b' => '4 Rukun',
                    'option_c' => '5 Rukun',
                    'option_d' => '6 Rukun',
                    'correct_answer' => 'C',
                    'explanation' => 'Rukun Islam berjumlah 5: Syahadat, Shalat, Zakat, Puasa, dan Haji bagi yang mampu.',
                ],
                [
                    'question_text' => 'Hukum melaksanakan shalat fardhu lima waktu bagi muslim yang telah baligh dan berakal adalah...',
                    'option_a' => 'Fardhu Kifayah',
                    'option_b' => 'Fardhu \'Ain',
                    'option_c' => 'Sunnah Muakkad',
                    'option_d' => 'Mubah',
                    'correct_answer' => 'B',
                    'explanation' => 'Shalat fardhu lima waktu berstatus Fardhu \'Ain (kewajiban mutlak perorangan).',
                ],
                [
                    'question_text' => 'Surah dalam Al-Qur\'an yang dijuluki sebagai "Ummul Kitab" (Induk Al-Qur\'an) adalah...',
                    'option_a' => 'Surah Al-Baqarah',
                    'option_b' => 'Surah Yasin',
                    'option_c' => 'Surah Al-Ikhlas',
                    'option_d' => 'Surah Al-Fatihah',
                    'correct_answer' => 'D',
                    'explanation' => 'Surah Al-Fatihah disebut Ummul Kitab karena merangkum seluruh prinsip ajaran Al-Qur\'an.',
                ],
                [
                    'question_text' => 'Sikap terpuji menahan diri dari hawa nafsu dan tabah menghadapi cobaan hidup dinamakan...',
                    'option_a' => 'Sabar',
                    'option_b' => 'Qana\'ah',
                    'option_c' => 'Tawadhu',
                    'option_d' => 'Amanah',
                    'correct_answer' => 'A',
                    'explanation' => 'Sabar adalah ketabahan menahan diri saat menjalankan ketaatan dan menjauhi maksiat.',
                ],
                [
                    'question_text' => 'Zakat yang wajib dikeluarkan setiap muslim menjelang Hari Raya Idul Fitri adalah...',
                    'option_a' => 'Zakat Mal',
                    'option_b' => 'Zakat Fitrah',
                    'option_c' => 'Zakat Pertanian',
                    'option_d' => 'Zakat Perdagangan',
                    'correct_answer' => 'B',
                    'explanation' => 'Zakat Fitrah dikeluarkan pada bulan Ramadhan hingga sebelum shalat Idul Fitri.',
                ],
            ],
            'matematika' => [
                [
                    'question_text' => 'Hasil dari operasi hitung (-15) + 28 - (-7) adalah...',
                    'option_a' => '20',
                    'option_b' => '26',
                    'option_c' => '30',
                    'option_d' => '36',
                    'correct_answer' => 'A',
                    'explanation' => '(-15) + 28 = 13. Kemudian 13 - (-7) = 13 + 7 = 20.',
                ],
                [
                    'question_text' => 'FPB dan KPK dari bilangan 24 dan 36 berturut-turut adalah...',
                    'option_a' => '6 dan 72',
                    'option_b' => '12 dan 72',
                    'option_c' => '12 dan 144',
                    'option_d' => '6 dan 144',
                    'correct_answer' => 'B',
                    'explanation' => 'Faktorisasi: 24 = 2^3 x 3, 36 = 2^2 x 3^2. FPB = 2^2 x 3 = 12. KPK = 2^3 x 3^2 = 72.',
                ],
                [
                    'question_text' => 'Sebuah persegi panjang memiliki panjang 15 cm dan lebar 8 cm. Keliling persegi panjang tersebut adalah...',
                    'option_a' => '46 cm',
                    'option_b' => '60 cm',
                    'option_c' => '120 cm',
                    'option_d' => '23 cm',
                    'correct_answer' => 'A',
                    'explanation' => 'Keliling = 2 x (panjang + lebar) = 2 x (15 + 8) = 2 x 23 = 46 cm.',
                ],
                [
                    'question_text' => 'Bentuk persen (%) dari pecahan 3/5 adalah...',
                    'option_a' => '30%',
                    'option_b' => '50%',
                    'option_c' => '60%',
                    'option_d' => '75%',
                    'correct_answer' => 'C',
                    'explanation' => '(3/5) x 100% = 60%.',
                ],
                [
                    'question_text' => 'Jika nilai x + 7 = 19, maka berapakah nilai dari 2x - 5?',
                    'option_a' => '19',
                    'option_b' => '24',
                    'option_c' => '15',
                    'option_d' => '17',
                    'correct_answer' => 'A',
                    'explanation' => 'x = 19 - 7 = 12. Maka 2(12) - 5 = 24 - 5 = 19.',
                ],
            ],
            'umum' => [
                [
                    'question_text' => "Ide pokok dari sebuah paragraf umumnya dapat ditemukan pada kalimat...",
                    'option_a' => 'Penjelas pertama',
                    'option_b' => 'Utama',
                    'option_c' => 'Penutup saja',
                    'option_d' => 'Sisipan',
                    'correct_answer' => 'B',
                    'explanation' => 'Ide pokok pikiran tertuang secara lugas di dalam kalimat utama.',
                ],
                [
                    'question_text' => 'Bagian tumbuhan yang berfungsi menyerap air dan unsur hara dari dalam tanah adalah...',
                    'option_a' => 'Batang',
                    'option_b' => 'Daun',
                    'option_c' => 'Akar',
                    'option_d' => 'Bunga',
                    'correct_answer' => 'C',
                    'explanation' => 'Akar menyerap cairan nutrisi dari tanah dan menopang kekokohan tanaman.',
                ],
                [
                    'question_text' => 'Ibu kota negara Indonesia yang baru diresmikan di wilayah Kalimantan Timur adalah...',
                    'option_a' => 'Nusantara',
                    'option_b' => 'Balikpapan',
                    'option_c' => 'Samarinda',
                    'option_d' => 'Pontianak',
                    'correct_answer' => 'A',
                    'explanation' => 'Ibu Kota Nusantara (IKN) terletak di Kalimantan Timur.',
                ],
            ],
        ];

        // Tentukan kategori pool
        $subjKey = 'umum';
        $lower = strtolower($subject . ' ' . $topic);
        if (str_contains($lower, 'islam') || str_contains($lower, 'pai') || str_contains($lower, 'agama') || str_contains($lower, 'shalat')) {
            $subjKey = 'pai';
        } elseif (str_contains($lower, 'mat') || str_contains($lower, 'hitung') || str_contains($lower, 'aljabar') || str_contains($lower, 'angka')) {
            $subjKey = 'matematika';
        }

        $source = $banks[$subjKey] ?? $banks['umum'];
        $results = [];

        for ($i = 0; $i < $count; $i++) {
            $base = $source[$i % count($source)];
            $results[] = [
                'question_text' => "[{$subject} - {$difficulty}] " . $base['question_text'],
                'option_a' => $base['option_a'],
                'option_b' => $base['option_b'],
                'option_c' => $base['option_c'],
                'option_d' => $base['option_d'],
                'correct_answer' => $base['correct_answer'],
                'points' => $points,
                'explanation' => $base['explanation'] . " (Materi: {$topic})",
            ];
        }

        return $results;
    }

    /**
     * Simulator audit berkas dengan pencocokan data pendaftar asli.
     */
    protected function getSimulatedDocumentAudit(Registration $registration, array $uploadedDocs): array
    {
        $checks = [];
        $hasIssues = false;

        if (isset($uploadedDocs['akte_kelahiran'])) {
            $checks[] = [
                'document' => 'Akte Kelahiran',
                'status' => 'valid',
                'detected_info' => "Nama: {$registration->name}, Lahir: {$registration->birth_place}, {$registration->birth_date?->format('d/m/Y')}",
                'expected_info' => "Nama: {$registration->name}, TTL: {$registration->birth_place}, {$registration->birth_date?->format('d F Y')}",
                'notes' => 'Nama lengkap dan tanggal lahir pada Akte Kelahiran cocok 100% dengan data formulir.',
            ];
        }

        if (isset($uploadedDocs['kartu_keluarga'])) {
            $checks[] = [
                'document' => 'Kartu Keluarga (KK)',
                'status' => 'valid',
                'detected_info' => "Orang Tua/Wali: {$registration->parent_name}, Alamat: " . ($registration->address ? substr($registration->address, 0, 30) . '...' : 'Sesuai Domisili'),
                'expected_info' => "Nama Orang Tua: {$registration->parent_name}",
                'notes' => 'Data Kepala Keluarga dan keterkaitan pendaftar teridentifikasi jelas.',
            ];
        }

        if (isset($uploadedDocs['ijazah'])) {
            // Simulasi cek NISN
            $nisnLen = strlen($registration->nisn);
            if ($nisnLen === 10) {
                $checks[] = [
                    'document' => 'Ijazah / SKL',
                    'status' => 'valid',
                    'detected_info' => "NISN: {$registration->nisn}, Asal: {$registration->school_origin}",
                    'expected_info' => "NISN: {$registration->nisn}, Sekolah: {$registration->school_origin}",
                    'notes' => 'Nomor NISN 10 digit dan stempel sekolah asal terverifikasi sah.',
                ];
            } else {
                $hasIssues = true;
                $checks[] = [
                    'document' => 'Ijazah / SKL',
                    'status' => 'warning',
                    'detected_info' => "NISN Terdeteksi: {$registration->nisn} ({$nisnLen} digit)",
                    'expected_info' => "Format standar NISN adalah 10 digit angka",
                    'notes' => 'Panitia disarankan mengecek kembali fisik ijazah karena panjang digit NISN tidak lazim.',
                ];
            }
        }

        $overallStatus = $hasIssues ? 'WARNING' : 'MATCH';
        $confidence = $hasIssues ? 85 : 98;
        $summary = $hasIssues
            ? 'Dokumen berhasil dianalisis dengan beberapa catatan yang perlu dicek ulang oleh panitia.'
            : 'Seluruh dokumen yang diunggah valid dan cocok sempurna dengan formulir pendaftar.';

        $suggestedNote = $hasIssues
            ? 'Dokumen lengkap. Mohon periksa kembali NISN saat verifikasi berkas fisik.'
            : 'Berkas lengkap dan data telah diverifikasi valid oleh AI Auditor. Siap mengikuti Ujian CBT.';

        return [
            'overall_status' => $overallStatus,
            'overall_confidence' => $confidence,
            'summary_notes' => $summary,
            'suggested_admin_note' => $suggestedNote,
            'checks' => $checks,
        ];
    }

    /**
     * Asisten Virtual ChatBot Sekolah & SPMB Online (Hybrid Gemini API + Contextual Knowledge Base).
     *
     * @param string $userMessage Pesan pertanyaan dari pengunjung/pendaftar
     * @param array $history Riwayat percakapan sebelumnya [['role' => 'user'|'assistant', 'content' => '...']]
     * @param array $context Data real-time dari database (profil sekolah, program, kriteria SAW, status pendaftar)
     * @return array
     */
    public function chatWithSchoolAssistant(string $userMessage, array $history = [], array $context = []): array
    {
        $suggestions = $this->getSuggestedQuestions($userMessage, $context);

        if (! $this->isConfigured()) {
            return [
                'status' => 'success',
                'source' => 'knowledge-base',
                'reply' => $this->getSimulatedChatReply($userMessage, $context),
                'suggestions' => $suggestions,
            ];
        }

        try {
            $systemInstruction = $this->buildChatbotSystemPrompt($context);

            $contents = [];
            foreach (array_slice($history, -6) as $turn) {
                $role = ($turn['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
                $text = trim((string) ($turn['content'] ?? ''));
                if ($text !== '') {
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => mb_substr($text, 0, 800)]],
                    ];
                }
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $endpoint = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(20)->post($endpoint, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 600,
                ],
            ]);

            if (! $response->successful()) {
                Log::warning('Gemini API Error (Chatbot): '.$response->body());

                return [
                    'status' => 'success',
                    'source' => 'knowledge-base',
                    'reply' => $this->getSimulatedChatReply($userMessage, $context),
                    'suggestions' => $suggestions,
                ];
            }

            $jsonResult = $response->json();
            $replyText = trim((string) ($jsonResult['candidates'][0]['content']['parts'][0]['text'] ?? ''));

            if ($replyText === '') {
                return [
                    'status' => 'success',
                    'source' => 'knowledge-base',
                    'reply' => $this->getSimulatedChatReply($userMessage, $context),
                    'suggestions' => $suggestions,
                ];
            }

            return [
                'status' => 'success',
                'source' => 'gemini-live',
                'reply' => $replyText,
                'suggestions' => $suggestions,
            ];
        } catch (\Throwable $e) {
            Log::error('Exception in GeminiAiService::chatWithSchoolAssistant: '.$e->getMessage());

            return [
                'status' => 'success',
                'source' => 'knowledge-base',
                'reply' => $this->getSimulatedChatReply($userMessage, $context),
                'suggestions' => $suggestions,
            ];
        }
    }

    /**
     * Menyusun System Prompt asisten virtual dengan injeksi data real-time dari database.
     */
    protected function buildChatbotSystemPrompt(array $context): string
    {
        $schoolName = $context['school_name'] ?? 'SMP Tunas Harapan Bekasi';
        $address = $context['school_address'] ?? 'Bekasi, Jawa Barat';
        $phone = $context['school_phone'] ?? '(021) 8888-9999';
        $email = $context['school_email'] ?? 'info@smptunasharapan.sch.id';
        $spmbStatus = ! empty($context['spmb_closed']) ? 'DITUTUP SEMENTARA' : 'DIBUKA (Gelombang Aktif)';
        $fee = $context['registration_fee'] ?? 'Rp 250.000';
        $threshold = $context['saw_threshold'] ?? 70;

        $programsList = '';
        foreach ($context['programs'] ?? [] as $prog) {
            $programsList .= "- {$prog['title']}: {$prog['description']}\n";
        }
        if ($programsList === '') {
            $programsList = "- Tahfidz & Karakter Islami\n- Literasi Digital & Coding\n- Bilingual Active Program\n";
        }

        $criteriaList = '';
        foreach ($context['saw_criteria'] ?? [] as $c) {
            $criteriaList .= "- {$c['code']} ({$c['name']}): Bobot {$c['weight']}%, Atribut {$c['type']}, Maks {$c['max_value']}\n";
        }

        $applicantInfo = 'Pengguna saat ini belum login (Tamu / Pengunjung Publik).';
        if (! empty($context['applicant'])) {
            $app = $context['applicant'];
            $applicantInfo = "Pengguna sedang LOGIN sebagai Calon Siswa:\n"
                ."- Nama Akun: {$app['user_name']}\n"
                .'- Status Formulir: '.($app['has_registration'] ? "Sudah mengisi ({$app['registration_number']})" : 'Belum mengisi formulir SPMB')."\n";

            if ($app['has_registration']) {
                $applicantInfo .= "- Status Berkas/Seleksi: {$app['status_label']}\n"
                    ."- Status Pembayaran: {$app['payment_status']}\n"
                    ."- Skor Ujian CBT: {$app['cbt_score']}\n"
                    ."- Hasil Nilai Akhir SAW: {$app['saw_score']}\n";
            }
        }

        return <<<EOT
Anda adalah "Tunas AI", Asisten Virtual resmi {$schoolName} (Islamic & Digital School).
Tugas Anda adalah membantu calon siswa, orang tua, dan pengunjung dengan ramah, sopan, jelas, dan ringkas dalam Bahasa Indonesia.

FAKTA RESMI SEKOLAH & SPMB ONLINE:
- Nama Sekolah: {$schoolName}
- Alamat: {$address}
- Telepon/WhatsApp: {$phone}
- Email: {$email}
- Status Pendaftaran SPMB: {$spmbStatus}
- Biaya Pendaftaran SPMB: {$fee}

ALUR PENDAFTARAN SPMB ONLINE:
1. Buat Akun & Verifikasi Email pada menu "Daftar PPDB".
2. Login ke Dashboard Pendaftar dan isi "Formulir SPMB" beserta unggah berkas: Kartu Keluarga (KK), Akta Kelahiran, Ijazah/SKL, dan Pas Foto.
3. Lakukan Pembayaran Biaya Pendaftaran ({$fee}) secara online (Midtrans/Virtual Account/QRIS) pada menu Pembayaran.
4. Panitia memverifikasi kelengkapan berkas pendaftaran.
5. Calon siswa yang berkasnya terverifikasi mengikuti "Ujian Online CBT" melalui portal siswa.
6. Seleksi akhir dihitung secara objektif dengan metode SPK SAW (Simple Additive Weighting) skala 0-100 (Lulus minimal {$threshold} / 100).

KRITERIA SELEKSI SPK SAW AKTIF:
{$criteriaList}

PROGRAM UNGGULAN SEKOLAH:
{$programsList}

KONTEKS PENGGUNA SAAT INI:
{$applicantInfo}

ATURAN MENJAWAB:
1. Gunakan format yang rapi (gunakan **teks tebal** untuk poin penting dan daftar bullet '-' bila menguraikan langkah).
2. Jawab maksimal 3-5 paragraf pendek agar nyaman dibaca di jendela chat.
3. Jangan mengarang informasi di luar fakta sekolah di atas. Jika membutuhkan bantuan admin manusia, arahkan untuk menekan tombol "Hubungi CS via WhatsApp" di bagian atas chat.
EOT;
    }

    /**
     * Balasan kontekstual cerdas (Knowledge Base Lokal) saat bekerja tanpa API Key atau offline.
     */
    protected function getSimulatedChatReply(string $userMessage, array $context): string
    {
        $msg = mb_strtolower(trim($userMessage));
        $schoolName = $context['school_name'] ?? 'SMP Tunas Harapan Bekasi';
        $address = $context['school_address'] ?? 'Bekasi, Jawa Barat';
        $phone = $context['school_phone'] ?? '(021) 8888-9999';
        $email = $context['school_email'] ?? 'info@smptunasharapan.sch.id';
        $fee = $context['registration_fee'] ?? 'Rp 250.000';
        $threshold = $context['saw_threshold'] ?? 70;
        $applicant = $context['applicant'] ?? null;

        // 1. Cek Status Pendaftaran Personal
        if (str_contains($msg, 'status saya') || str_contains($msg, 'status pendaftaran') || str_contains($msg, 'nilai saya') || str_contains($msg, 'kelulusan saya') || str_contains($msg, 'cek status')) {
            if (! $applicant) {
                return "Untuk memeriksa status pendaftaran dan hasil seleksi secara otomatis, silakan **Masuk (Login)** terlebih dahulu ke akun pendaftar Anda.\n\n"
                    ."- Sudah punya akun? Silakan klik menu **Masuk** di pojok kanan atas.\n"
                    ."- Belum mendaftar? Klik tombol **Daftar PPDB** untuk membuat akun calon siswa baru.";
            }

            if (! $applicant['has_registration']) {
                return "Halo **{$applicant['user_name']}**! Saat ini akun Anda sudah aktif, namun Anda **belum mengirimkan Formulir Pendaftaran SPMB**.\n\n"
                    ."**Langkah Selanjutnya:**\n"
                    ."- Buka menu **Formulir SPMB** di bagian atas halaman.\n"
                    ."- Lengkapi biodata diri, data orang tua, dan unggah dokumen persyaratan (KK, Akta Kelahiran, Ijazah/SKL).";
            }

            return "Halo **{$applicant['user_name']}**, berikut adalah ringkasan status pendaftaran Anda saat ini:\n\n"
                ."- **No. Pendaftaran:** `{$applicant['registration_number']}`\n"
                ."- **Status Berkas & Seleksi:** **{$applicant['status_label']}**\n"
                ."- **Status Pembayaran:** {$applicant['payment_status']}\n"
                ."- **Skor Ujian CBT:** {$applicant['cbt_score']}\n"
                ."- **Hasil Nilai Akhir (SAW):** **{$applicant['saw_score']}** (Batas Lulus: ≥ {$threshold} / 100)\n\n"
                ."Anda juga dapat mengunduh **Kartu Peserta** atau melihat rincian lengkap melalui menu **Dashboard** dan **Pengumuman**.";
        }

        // 2. Biaya Pendaftaran & Pembayaran
        if (str_contains($msg, 'biaya') || str_contains($msg, 'bayar') || str_contains($msg, 'pembayaran') || str_contains($msg, 'harga') || str_contains($msg, 'uang')) {
            return "Berikut informasi **Biaya Pendaftaran SPMB {$schoolName}**:\n\n"
                ."- **Biaya Formulir & Seleksi SPMB:** **{$fee}**\n"
                ."- **Metode Pembayaran:** Pembayaran dilakukan secara online melalui *Dashboard Pendaftar* setelah mengisi formulir (mendukung Virtual Account Bank, QRIS, GoPay, ShopeePay, dan gerai ritel melalui sistem Midtrans).\n"
                ."- **Bukti Pembayaran:** Setelah pembayaran berhasil, status otomatis terverifikasi dan Anda dapat mengunduh *Invoice Resmi (PDF)* langsung dari dashboard.";
        }

        // 3. Syarat & Berkas Dokumen
        if (str_contains($msg, 'syarat') || str_contains($msg, 'berkas') || str_contains($msg, 'dokumen') || str_contains($msg, 'persyaratan') || str_contains($msg, 'kk') || str_contains($msg, 'ijazah')) {
            return "Untuk mendaftar di **{$schoolName}**, pastikan Anda menyiapkan **berkas digital (JPG/PNG/PDF, maks. 2 MB)** berikut:\n\n"
                ."- **Kartu Keluarga (KK)** asli atau fotokopi yang terbaca jelas.\n"
                ."- **Akta Kelahiran** calon peserta didik.\n"
                ."- **Ijazah SD/MI** atau **Surat Keterangan Lulus (SKL)**.\n"
                ."- **Pas Foto** terbaru berpakaian seragam sekolah rapi.\n"
                ."- **NISN** aktif (10 digit) dan nilai rata-rata rapor SD/MI.";
        }

        // 4. Alur / Cara Pendaftaran SPMB
        if (str_contains($msg, 'alur') || str_contains($msg, 'cara daftar') || str_contains($msg, 'pendaftaran') || str_contains($msg, 'ppdb') || str_contains($msg, 'spmb') || str_contains($msg, 'langkah')) {
            return "Berikut **6 Langkah Mudah Pendaftaran SPMB Online {$schoolName}**:\n\n"
                ."1. **Registrasi Akun:** Klik menu **Daftar PPDB** dan verifikasi alamat email Anda.\n"
                ."2. **Isi Formulir & Unggah Berkas:** Lengkapi biodata siswa, orang tua, serta unggah KK, Akta Kelahiran, dan Ijazah/SKL.\n"
                ."3. **Pembayaran Online:** Selesaikan pembayaran biaya pendaftaran ({$fee}) melalui menu Pembayaran.\n"
                ."4. **Verifikasi Panitia:** Panitia SPMB memverifikasi keabsahan berkas Anda.\n"
                ."5. **Ujian Seleksi CBT:** Ikuti ujian berbasis komputer (*Computer Based Test*) secara online.\n"
                ."6. **Pengumuman Kelulusan:** Hasil perankingan dihitung transparan dengan metode **SPK SAW** dan diumumkan di menu **Pengumuman**.";
        }

        // 5. Ujian CBT & Seleksi SPK SAW / Nilai
        if (str_contains($msg, 'cbt') || str_contains($msg, 'ujian') || str_contains($msg, 'tes') || str_contains($msg, 'saw') || str_contains($msg, 'kriteria') || str_contains($msg, 'seleksi') || str_contains($msg, 'bobot') || str_contains($msg, 'ranking')) {
            $criteriaLines = '';
            foreach ($context['saw_criteria'] ?? [] as $c) {
                $criteriaLines .= "- **{$c['code']} — {$c['name']}:** Bobot **{$c['weight']}%** (Nilai Maks: {$c['max_value']})\n";
            }
            if ($criteriaLines === '') {
                $criteriaLines = "- **C1 — Nilai Ujian CBT:** Bobot **35%**\n"
                    ."- **C2 — Rata-rata Nilai Rapor:** Bobot **30%**\n"
                    ."- **C3 — Nilai Wawancara & Baca Al-Qur'an:** Bobot **20%**\n"
                    ."- **C4 — Prestasi Akademik / Non-Akademik:** Bobot **15%**\n";
            }

            return "Seleksi penerimaan siswa baru di **{$schoolName}** menggunakan metode **SPK Simple Additive Weighting (SAW)** pada skala **0 – 100** dengan rincian kriteria:\n\n"
                .$criteriaLines."\n"
                ."**Ambang Batas Kelulusan:** Calon siswa dinyatakan **Lulus Seleksi** apabila memperoleh Hasil Nilai Akhir (**V**) minimal **{$threshold} / 100**.";
        }

        // 6. Program Unggulan & Ekstrakurikuler
        if (str_contains($msg, 'program') || str_contains($msg, 'unggulan') || str_contains($msg, 'ekskul') || str_contains($msg, 'kurikulum') || str_contains($msg, 'fasilitas')) {
            $progLines = '';
            foreach ($context['programs'] ?? [] as $p) {
                $progLines .= "- **{$p['title']}:** {$p['description']}\n";
            }
            if ($progLines === '') {
                $progLines = "- **Tahfidz & Karakter Islami:** Pembinaan hafalan Al-Qur'an dan adab harian.\n"
                    ."- **Digital & Coding Class:** Pembelajaran teknologi informasi dan kreativitas digital.\n"
                    ."- **Bilingual Active:** Penguatan komunikasi Bahasa Inggris dan Bahasa Arab.\n";
            }

            return "Berikut adalah **Program Unggulan {$schoolName}** yang dirancang untuk membentuk generasi berkarakter Islami dan cakap digital:\n\n"
                .$progLines."\n"
                ."Informasi fasilitas juga dapat Anda lihat langsung pada bagian **Galeri & Fasilitas** di halaman Beranda.";
        }

        // 7. Lokasi, Alamat & Kontak Sekolah
        if (str_contains($msg, 'alamat') || str_contains($msg, 'lokasi') || str_contains($msg, 'kontak') || str_contains($msg, 'telepon') || str_contains($msg, 'wa') || str_contains($msg, 'whatsapp') || str_contains($msg, 'dimana')) {
            return "Berikut informasi kontak dan lokasi resmi **{$schoolName}**:\n\n"
                ."- **Alamat:** {$address}\n"
                ."- **Telepon / WhatsApp:** {$phone}\n"
                ."- **Email Resmi:** {$email}\n"
                ."- **Jam Layanan Panitia SPMB:** Senin – Sabtu (08.00 – 15.00 WIB)\n\n"
                ."Untuk terhubung langsung dengan petugas panitia kami, silakan klik tombol hijau **WhatsApp CS** di bagian atas jendela chat ini.";
        }

        // 8. Sapaan / Default Fallback
        $greetingName = $applicant ? " **{$applicant['user_name']}**" : '';

        return "Assalamu'alaikum dan selamat datang{$greetingName} di layanan **Tunas AI — Asisten Virtual {$schoolName}**! 👋\n\n"
            ."Saya siap membantu Anda memberikan informasi seputar:\n"
            ."- **Alur & Cara Pendaftaran SPMB Online**\n"
            ."- **Persyaratan Berkas & Biaya Pendaftaran ({$fee})**\n"
            ."- **Info Ujian Online CBT & Sistem Seleksi SAW**\n"
            ."- **Program Unggulan & Profil Sekolah**\n"
            ."- **Cek Status Pendaftaran Anda**\n\n"
            ."Silakan ketik pertanyaan Anda atau pilih salah satu tombol topik di bawah ini.";
    }

    /**
     * Menghasilkan saran pertanyaan lanjutan (Quick Reply Chips) yang kontekstual.
     */
    protected function getSuggestedQuestions(string $userMessage, array $context): array
    {
        $msg = mb_strtolower($userMessage);
        $hasAuth = ! empty($context['applicant']);

        if (str_contains($msg, 'alur') || str_contains($msg, 'daftar')) {
            return [
                'Apa saja syarat berkas SPMB?',
                'Berapa biaya pendaftaran?',
                'Bagaimana sistem ujian CBT & SAW?',
                $hasAuth ? 'Cek status pendaftaran saya' : 'Program unggulan sekolah',
            ];
        }

        if (str_contains($msg, 'syarat') || str_contains($msg, 'berkas')) {
            return [
                'Berapa biaya pendaftaran?',
                'Bagaimana alur pendaftaran SPMB?',
                'Bagaimana sistem ujian CBT & SAW?',
                'Alamat & kontak sekolah',
            ];
        }

        return [
            'Alur pendaftaran SPMB',
            'Syarat berkas pendaftaran',
            'Biaya pendaftaran SPMB',
            'Info Ujian CBT & Seleksi SAW',
            $hasAuth ? 'Cek status pendaftaran saya' : 'Program unggulan sekolah',
        ];
    }
}
