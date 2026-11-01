<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Gallery;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Program;
use App\Models\Question;
use App\Models\Registration;
use App\Models\Teacher;
use App\Services\SettingService;

class HomeController extends Controller
{
    public function __construct(
        private SettingService $settingService
    ) {}

    public function index()
    {
        $programs = Program::active()->ordered()->take(6)->get();
        $posts = Post::published()->latestFirst()->take(3)->get();
        $galleries = Gallery::ordered()->take(8)->get();
        $headmaster = Teacher::guru()->ordered()->first();
        $settings = $this->settingService;

        return view('pages.home', compact('programs', 'posts', 'galleries', 'headmaster', 'settings'));
    }

    public function dashboard(\App\Services\SawService $sawService)
    {
        $user = auth()->user();
        $registration = Registration::where('user_id', $user->id)->first();
        $examResult = ExamResult::where('user_id', $user->id)->first();

        $maxScore = Question::sum('points');

        $payment = null;
        $sawResult = null;
        if ($registration) {
            $payment = Payment::where('registration_id', $registration->id)
                ->where('status', 'success')
                ->latest()
                ->first();

            if (in_array($registration->status, ['verified', 'lulus', 'tidak_lulus'])) {
                $sawResult = $sawService->getCandidateResult($registration->id);
            }
        }

        return view('pages.pendaftar.dashboard', compact(
            'user',
            'registration',
            'examResult',
            'maxScore',
            'payment',
            'sawResult'));
    }

    public function profile()
    {
        $registration = Registration::where('user_id', auth()->id())->first();

        if (! $registration) {
            return redirect()->route('spmb')->with('error', 'Silakan isi formulir pendaftaran terlebih dahulu.');
        }

        return view('pages.pendaftar.profile', compact('registration'));
    }

    public function about()
    {
        $teachers = Teacher::guru()->ordered()->get();
        $staff = Teacher::staff()->ordered()->get();

        return view('pages.about', compact('teachers', 'staff'));
    }

    public function berita()
    {
        $posts = Post::published()->latestFirst()->paginate(6);

        return view('pages.berita', compact('posts'));
    }

    public function beritaDetail(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latestFirst()
            ->take(3)
            ->get();

        return view('pages.berita-detail', compact('post', 'relatedPosts'));
    }

    public function spmb()
    {
        $existing = Registration::where('user_id', auth()->id())->first();
        if ($existing) {
            return redirect()
                ->route('pendaftar.profile')
                ->with('info', 'Anda sudah mengirimkan formulir pendaftaran dengan nomor: '.$existing->registration_number.'. Berikut adalah rincian biodata dan berkas Anda.');
        }

        if ($this->settingService->get('spmb_disabled') === '1') {
            return view('pages.pendaftar.spmb-closed');
        }

        $settings = $this->settingService;

        return view('pages.pendaftar.spmb', compact('settings'));
    }

    public function contact()
    {
        $settings = $this->settingService;

        return view('pages.contact', compact('settings'));
    }
}
