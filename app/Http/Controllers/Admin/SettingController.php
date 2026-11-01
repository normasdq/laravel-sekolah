<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private SettingService $settingService
    ) {}

    public function index()
    {
        $settings = $this->settingService->getAll();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_short_name' => 'required|string|max:20',
            'school_motto' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:255',
            'school_facebook' => 'nullable|url|max:255',
            'school_instagram' => 'nullable|url|max:255',
            'school_youtube' => 'nullable|url|max:255',
            'school_tiktok' => 'nullable|url|max:255',
            'spmb_info' => 'nullable|string|max:2000',
            'headmaster_welcome' => 'nullable|string|max:5000',

            'spmb_exam_date' => 'nullable|string|max:20',
            'spmb_exam_time' => 'nullable|string|max:20',
            'spmb_exam_location' => 'nullable|string|max:255',
            'spmb_re_registration_fee' => 'nullable|numeric|min:0',
            'spmb_disabled' => 'nullable',
            'google_site_verification' => 'nullable|string|max:255',
            'msvalidate_01' => 'nullable|string|max:255',
        ]);

        $fields = [
            'school_name', 'school_short_name', 'school_motto',
            'school_address', 'school_phone', 'school_email',
            'school_facebook', 'school_instagram', 'school_youtube', 'school_tiktok',
            'spmb_info', 'spmb_exam_date', 'spmb_exam_time', 'spmb_exam_location',
            'spmb_re_registration_fee',
            'headmaster_welcome', 'spmb_disabled', 'google_site_verification', 'msvalidate_01',
        ];

        foreach ($fields as $field) {
            // Cek khusus untuk checkbox
            if ($field === 'spmb_disabled') {
                $value = $request->has('spmb_disabled') ? '1' : '0';
            } else {
                // Untuk input biasa, ambil valuenya atau biarkan string kosong
                $value = $request->input($field, '');
            }

            $this->settingService->set($field, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
