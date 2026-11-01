<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Registration;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExampController extends Controller
{
    // List Semua Soal
    public function index()
    {
        $questions = Question::latest()->paginate(10);

        // Sesuaikan path view dengan folder anda
        return view('admin.examp.index', compact('questions'));
    }

    // Form Tambah Soal
    public function create()
    {
        return view('admin.examp.create');
    }

    // Simpan Soal
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'points' => 'required|numeric',
        ]);

        Question::create($data);

        return redirect()->route('admin.examp.index')->with('success', 'Soal berhasil ditambahkan');
    }

    // Edit Soal
    public function edit($id)
    {
        $exam = Question::findOrFail($id);

        return view('admin.examp.edit', compact('exam'));
    }

    // Update Soal
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'points' => 'required|numeric',
        ]);

        $exam = Question::findOrFail($id);
        $exam->update($data);

        return redirect()->route('admin.examp.index')->with('success', 'Soal berhasil diperbarui');
    }

    // Hapus Soal
    public function destroy($id)
    {
        $exam = Question::findOrFail($id);
        $exam->delete();

        return redirect()->route('admin.examp.index')->with('success', 'Soal berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        Question::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids).' soal berhasil dihapus.');
    }

    // Lihat Hasil Ujian Pendaftar
    public function results(Request $request)
    {
        $query = ExamResult::with(['user', 'registration'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('registration', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $results = $query->paginate(20)->withQueryString();

        // Muat nomor registrasi untuk pendaftar pada halaman aktif
        $userIds = $results->pluck('user_id')->filter()->unique();
        $registration = Registration::whereIn('user_id', $userIds)
            ->pluck('registration_number', 'user_id');

        return view('admin.examp.results', compact('results', 'registration'));
    }

    /**
     * Generate draft butir soal CBT dengan AI.
     */
    public function aiGenerate(Request $request, GeminiAiService $aiService)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'topic' => 'required|string|max:200',
            'difficulty' => 'required|in:mudah,sedang,sulit',
            'count' => 'required|integer|min:1|max:10',
            'points' => 'required|integer|min:1|max:100',
        ]);

        $result = $aiService->generateExamQuestions(
            $validated['subject'],
            $validated['topic'],
            $validated['difficulty'],
            (int) $validated['count'],
            (int) $validated['points']
        );

        return response()->json($result);
    }

    /**
     * Simpan batch kumpulan butir soal hasil AI ke Bank Soal.
     */
    public function aiSaveBatch(Request $request)
    {
        $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.correct_answer' => 'required|in:A,B,C,D',
            'questions.*.points' => 'required|numeric',
        ]);

        $questionsData = $request->input('questions');
        $insertedCount = 0;

        DB::transaction(function () use ($questionsData, &$insertedCount) {
            foreach ($questionsData as $item) {
                Question::create([
                    'question_text' => $item['question_text'],
                    'option_a' => $item['option_a'],
                    'option_b' => $item['option_b'],
                    'option_c' => $item['option_c'],
                    'option_d' => $item['option_d'],
                    'correct_answer' => strtoupper($item['correct_answer']),
                    'points' => $item['points'],
                ]);
                $insertedCount++;
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => "Alhamdulillah, {$insertedCount} butir soal AI berhasil disimpan ke Bank Soal CBT.",
        ]);
    }
}

