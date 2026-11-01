<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Registration;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = auth()->user()->role;

        // Inisialisasi array stats dengan 0 agar key selalu ada
        $stats = [
            'total_posts' => 0,
            'published_posts' => 0,
            'total_staff' => 0,
            'total_registrations' => 0,
            'pending_registrations' => 0,
            'verified_registrations' => 0,
            'total_galleries' => 0,
            'unread_messages' => 0,
            'total_teachers' => 0,
        ];

        // 1. Statistik Konten (Admin & Penulis)
        if (in_array($userRole, ['admin', 'penulis'])) {
            $stats['total_posts'] = Post::count();
            $stats['published_posts'] = Post::where('is_published', true)->count();
            $stats['total_staff'] = Teacher::where('type', 'staff')->count();
        }

        // 2. Statistik SPMB (Admin & SPMB)
        if (in_array($userRole, ['admin', 'spmb'])) {
            $stats['total_registrations'] = Registration::count();
            $stats['pending_registrations'] = Registration::where('status', 'pending')->count();
            $stats['verified_registrations'] = Registration::whereIn('status', ['verified', 'lulus', 'tidak_lulus'])->count();
            $stats['lulus_registrations'] = Registration::where('status', 'lulus')->count();
            $stats['unread_messages'] = Contact::where('is_read', false)->count();
            $stats['total_galleries'] = Gallery::count();
        }

        // 3. Statistik Guru & Pesan (Khusus Admin)
        if ($userRole === 'admin') {
            $stats['total_teachers'] = Teacher::count();
            $stats['unread_messages'] = Contact::where('is_read', false)->count();
        }

        // 4. Data Terbaru (Safety Null: Return collection kosong jika tidak punya akses)
        $recentRegistrations = (in_array($userRole, ['admin', 'spmb'])) ? Registration::latest()->take(5)->get() : collect();
        $recentContacts = ($userRole === 'admin' || $userRole === 'spmb') ? Contact::latest()->take(5)->get() : collect();
        $recentPosts = (in_array($userRole, ['admin', 'penulis'])) ? Post::latest()->take(5)->get() : collect();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentContacts', 'recentPosts'));
    }
}
