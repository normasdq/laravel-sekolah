<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Registration;
use App\Models\User;
use App\Services\GeminiAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $spmbUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_ai@example.com',
            'role' => 'admin',
        ]);

        $this->spmbUser = User::factory()->create([
            'email' => 'spmb_ai@example.com',
            'role' => 'spmb',
        ]);

        $this->studentUser = User::factory()->create([
            'email' => 'student_ai@example.com',
            'role' => 'user',
        ]);
    }

    public function test_gemini_service_handles_fallback_simulator_cleanly()
    {
        $service = new GeminiAiService();

        // 1. Test generate questions
        $result = $service->generateExamQuestions('Pendidikan Agama Islam', 'Shalat Fardhu', 'sedang', 3, 2);

        $this->assertEquals('success', $result['status']);
        $this->assertCount(3, $result['data']);
        $this->assertArrayHasKey('question_text', $result['data'][0]);
        $this->assertArrayHasKey('correct_answer', $result['data'][0]);
        $this->assertContains($result['data'][0]['correct_answer'], ['A', 'B', 'C', 'D']);

        // 2. Test audit documents
        $registration = Registration::create([
            'user_id' => $this->studentUser->id,
            'name' => 'Ahmad Fulan',
            'nisn' => '0012345678',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2012-05-15',
            'school_origin' => 'SD Negeri 01',
            'parent_name' => 'Bapak Fulan',
            'phone' => '08123456789',
            'kartu_keluarga' => 'documents/kk.jpg',
            'ijazah' => 'documents/ijazah.jpg',
            'akte_kelahiran' => 'documents/akte.jpg',
            'status' => 'pending',
        ]);

        $audit = $service->auditRegistrationDocuments($registration);
        $this->assertEquals('success', $audit['status']);
        $this->assertNotEmpty($audit['data']['overall_status']);
        $this->assertNotEmpty($audit['data']['checks']);
    }

    public function test_ai_exam_generator_validates_input_and_returns_draft_questions()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.examp.ai-generate'), [
                'subject' => 'Matematika',
                'topic' => 'Operasi Hitung Bilangan Bulat',
                'difficulty' => 'sedang',
                'count' => 4,
                'points' => 3,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'question_text',
                        'option_a',
                        'option_b',
                        'option_c',
                        'option_d',
                        'correct_answer',
                        'points',
                    ]
                ]
            ]);
    }

    public function test_ai_exam_batch_save_inserts_records_into_database()
    {
        $questionsPayload = [
            [
                'question_text' => 'Soal AI Pertama: Berapa hasil 5 + 7?',
                'option_a' => '10',
                'option_b' => '11',
                'option_c' => '12',
                'option_d' => '13',
                'correct_answer' => 'C',
                'points' => 2,
            ],
            [
                'question_text' => 'Soal AI Kedua: Berapa hasil 10 - 4?',
                'option_a' => '6',
                'option_b' => '7',
                'option_c' => '8',
                'option_d' => '9',
                'correct_answer' => 'A',
                'points' => 2,
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.examp.ai-save-batch'), [
                'questions' => $questionsPayload,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Soal AI Pertama: Berapa hasil 5 + 7?',
            'correct_answer' => 'C',
        ]);

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Soal AI Kedua: Berapa hasil 10 - 4?',
            'correct_answer' => 'A',
        ]);
    }

    public function test_ai_document_audit_endpoint_requires_auth_and_permission()
    {
        $registration = Registration::create([
            'user_id' => $this->studentUser->id,
            'name' => 'Siti Aisyah',
            'nisn' => '0098765432',
            'gender' => 'P',
            'birth_place' => 'Bandung',
            'birth_date' => '2012-08-20',
            'school_origin' => 'SD Teladan',
            'parent_name' => 'Hasan',
            'phone' => '08987654321',
            'kartu_keluarga' => 'documents/kk_siti.jpg',
            'ijazah' => 'documents/ijazah_siti.jpg',
            'status' => 'pending',
        ]);

        // 1. Guest ditolak
        $guestRes = $this->postJson(route('admin.registrations.ai-audit', $registration));
        $this->assertTrue(in_array($guestRes->status(), [302, 401]));

        // 2. Regular user (siswa) ditolak (403)
        $userRes = $this->actingAs($this->studentUser)
            ->postJson(route('admin.registrations.ai-audit', $registration));
        $userRes->assertStatus(403);

        // 3. Panitia SPMB diperbolehkan (200)
        $spmbRes = $this->actingAs($this->spmbUser)
            ->postJson(route('admin.registrations.ai-audit', $registration));
        $spmbRes->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
