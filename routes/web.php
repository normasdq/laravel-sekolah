<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
// --- Controller Publik & Pendaftar ---
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExampController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\SawController;
use App\Http\Controllers\Admin\SawCriterionController;
use App\Http\Controllers\Admin\SettingController;
// --- Controller Admin ---
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckStatusController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicContactController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\UjianController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckRole;
// --- Middleware ---
use App\Http\Middleware\IsSpmbMiddleware;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LANDING PAGE & PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');
Route::post('/kontak', [PublicContactController::class, 'store'])->name('contact.store');

// Comments
Route::get('/berita/{post:slug}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::post('/berita/{post:slug}/comments', [CommentController::class, 'store'])->middleware('throttle:5,1')->name('comments.store');

// AI ChatBot Assistant (Publik & Pendaftar)
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])
    ->middleware('throttle:20,1')
    ->name('chatbot.send');

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION ROUTES
|--------------------------------------------------------------------------
*/

// UBAH DARI verification.send MENJADI verification.notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Rute untuk memproses verifikasi (saat link diklik)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Rute untuk mengirim ulang email (ini yang cocok dinamai verification.send)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Link verifikasi baru telah dikirim.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION (GUEST ONLY)
|--------------------------------------------------------------------------
| Mencegah user yang sudah login (Admin/User) untuk kembali ke halaman login.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:3,10');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD & SPMB ROUTES (PENDAFTAR / ROLE: USER)
|--------------------------------------------------------------------------
*/
Route::prefix('spmb')->group(function () {
    // Hanya user dengan role 'user' yang bisa akses bagian ini
    Route::middleware(['auth', 'verified', CheckRole::class.':user'])->group(function () {

        Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [HomeController::class, 'profile'])->name('pendaftar.profile');
        Route::post('/profil/update-photo', [PublicRegistrationController::class, 'updatePhoto'])->name('pendaftar.profile.update-photo');
        Route::get('/download-bukti', [PublicRegistrationController::class, 'downloadBukti'])->name('pendaftar.download');
        Route::get('/download-bukti-pdf', [PublicRegistrationController::class, 'downloadBukti'])->name('pendaftar.pdf.bukti-pendaftaran');
        Route::get('/download-kartu', [PublicRegistrationController::class, 'downloadKartu'])->name('pendaftar.kartu');
        Route::get('/dokumen/{type}', [PublicRegistrationController::class, 'showDocument'])->name('pendaftar.document');

        // Cek Status Pendaftaran
        Route::get('/cek-status', [CheckStatusController::class, 'show'])->name('pendaftar.cek-status');
        Route::post('/cek-status', [CheckStatusController::class, 'check'])->name('pendaftar.cek-status.check');
        Route::get('/cek-status/pdf', [CheckStatusController::class, 'downloadPdf'])->name('pendaftar.cek-status.pdf');

        // Fitur CBT (Ujian Online)
        Route::get('/ujian', [UjianController::class, 'index'])->name('pendaftar.ujian.index');
        Route::get('/ujian/start', [UjianController::class, 'start'])->name('pendaftar.ujian.start');
        Route::post('/ujian/submit', [UjianController::class, 'submit'])->name('pendaftar.ujian.submit');

        // Payment
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('/payment/demo-pay', [PaymentController::class, 'demoPay'])->name('payment.demo.pay');
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
        Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
        Route::get('/payment/invoice', [PaymentController::class, 'downloadInvoice'])->name('payment.invoice');

        // Form Pendaftaran
        Route::get('/', [HomeController::class, 'spmb'])->name('spmb');

        Route::post('/store', [PublicRegistrationController::class, 'store'])->name('pendaftar.spmb.store');
    });
});

// Midtrans Callback (no auth)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL ROUTES (ROLE: ADMIN, SPMB, PENULIS)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Login khusus admin (jika ingin dipisah dari login user)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Grup Middleware Keamanan Admin
    Route::middleware([AdminMiddleware::class])->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // 1. Grup Konten (Admin & Penulis)
        Route::middleware(['check.role:admin,penulis'])->group(function () {
            Route::resource('posts', PostController::class);
            Route::post('posts/bulk-delete', [PostController::class, 'bulkDelete'])->name('posts.bulk-delete');
            Route::resource('galleries', GalleryController::class);
            Route::post('galleries/bulk-delete', [GalleryController::class, 'bulkDelete'])->name('galleries.bulk-delete');
            Route::resource('programs', ProgramController::class);
            Route::post('programs/bulk-delete', [ProgramController::class, 'bulkDelete'])->name('programs.bulk-delete');
            Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
            Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');

            // Komentar
            Route::prefix('comments')->name('comments.')->group(function () {
                Route::get('/', [AdminCommentController::class, 'index'])->name('index');
                Route::patch('{comment}/approve', [AdminCommentController::class, 'approve'])->name('approve');
                Route::delete('{comment}', [AdminCommentController::class, 'destroy'])->name('destroy');
            });

            // 2. Grup Khusus Root Admin (Settings & Guru)
            Route::middleware(['check.role:admin'])->group(function () {
                Route::resource('teachers', TeacherController::class);
                Route::post('teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])->name('teachers.bulk-delete');
                Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
                Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
            });
        });

        // Pesan Masuk (Admin & SPMB)
        Route::middleware(['check.role:admin,spmb'])->group(function () {
            Route::resource('contacts', ContactController::class)->only(['index', 'show', 'destroy']);
            Route::post('contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulk-delete');
            Route::post('contacts/mark-all-read', [ContactController::class, 'markAllRead'])->name('contacts.mark-all-read');
        });

        // 3. Grup SPMB & EXAMP (Admin & Panitia/Staff SPMB)
        Route::middleware([IsSpmbMiddleware::class])->group(function () {

            Route::resource('registrations', RegistrationController::class);
            Route::get('registrations/{registration}/document/{type}', [RegistrationController::class, 'showDocument'])->name('registrations.document');
            Route::post('registrations/{registration}/ai-audit', [RegistrationController::class, 'aiAudit'])->name('registrations.ai-audit');
            Route::post('registrations/verify/{id}', [RegistrationController::class, 'verifyDocuments'])->name('registrations.verify-documents');
            Route::post('registrations/{registration}/update-payment', [RegistrationController::class, 'updatePayment'])->name('registrations.update-payment');
            Route::post('registrations/export/', [RegistrationController::class, 'export'])->name('registrations.export');
            Route::post('registrations/export-pdf/', [RegistrationController::class, 'exportPdf'])->name('registrations.export-pdf');
            Route::post('registrations/bulk-delete', [RegistrationController::class, 'bulkDelete'])->name('registrations.bulk-delete');

            // Data Pembayaran
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
                Route::post('export', [App\Http\Controllers\Admin\PaymentController::class, 'exportCsv'])->name('export');
                Route::post('export-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportPdf'])->name('export-pdf');
                Route::get('/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');

                // Hanya root admin yang berhak menghapus data transaksi keuangan
                Route::middleware(['check.role:admin'])->group(function () {
                    Route::delete('{id}', [App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->name('destroy');
                    Route::post('bulk-delete', [App\Http\Controllers\Admin\PaymentController::class, 'bulkDelete'])->name('bulk-delete');
                });
            });

            // Fitur CBT (Examp)
            Route::prefix('examp')->name('examp.')->group(function () {
                Route::get('/', [ExampController::class, 'index'])->name('index');
                Route::get('/create', [ExampController::class, 'create'])->name('create');
                Route::get('/{id}/edit', [ExampController::class, 'edit'])->name('edit');
                Route::put('/{id}', [ExampController::class, 'update'])->name('update');
                Route::post('/store', [ExampController::class, 'store'])->name('store');
                Route::get('/results', [ExampController::class, 'results'])->name('results');
                Route::delete('/{id}', [ExampController::class, 'destroy'])->name('destroy');
                Route::post('/bulk-delete', [ExampController::class, 'bulkDelete'])->name('bulk-delete');
                // Fitur AI Bank Soal CBT
                Route::post('/ai-generate', [ExampController::class, 'aiGenerate'])->name('ai-generate');
                Route::post('/ai-save-batch', [ExampController::class, 'aiSaveBatch'])->name('ai-save-batch');
            });

            // Fitur SPK Seleksi Siswa Baru (Metode SAW)
            Route::prefix('spk')->name('spk.')->group(function () {
                Route::get('/', [SawController::class, 'index'])->name('index');
                Route::get('/nilai', [SawController::class, 'scores'])->name('scores');
                Route::post('/nilai', [SawController::class, 'saveScores'])->name('scores.save');
                Route::get('/export-pdf', [SawController::class, 'exportPdf'])->name('export-pdf');

                Route::resource('kriteria', SawCriterionController::class)
                    ->only(['index', 'store', 'update', 'destroy'])
                    ->parameters(['kriteria' => 'criterion'])
                    ->names('criteria');

                // Hanya root admin yang dapat mengubah ambang batas resmi & menerapkan kelulusan massal
                Route::middleware(['check.role:admin'])->group(function () {
                    Route::put('/threshold', [SawController::class, 'updateThreshold'])->name('threshold');
                    Route::post('/terapkan', [SawController::class, 'apply'])->name('apply');
                });
            });
        });
    });
});

// Fallback untuk melayani file publik jika symlink dinonaktifkan oleh shared hosting (InfinityFree)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/'.$path);
    if (! file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');

