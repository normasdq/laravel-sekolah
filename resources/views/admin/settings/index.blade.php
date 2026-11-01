@extends('admin.layouts.app')

@section('page_title', 'Pengaturan Situs')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-sliders text-primary mr-2"></i>Pengaturan Situs & SPMB
            </h4>
            <p class="text-muted small mb-0">
                Kelola informasi profil sekolah, kontak resmi, tautan media sosial, serta jadwal dan biaya SPMB.
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-cog mr-2 text-primary"></i>Konfigurasi Sistem
            </h3>
        </div>
        <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general">Umum</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contact-info">Kontak</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#social">Media Sosial</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#spmb-settings">SPMB</a></li>
            </ul>

            <div class="tab-content">

                <!-- General -->
                <div class="tab-pane active" id="general">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Sekolah</label>
                                <input type="text" name="school_name"
                                    value="{{ old('school_name', $settings['school_name'] ?? '') }}"
                                    class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Singkat</label>
                                <input type="text" name="school_short_name"
                                    value="{{ old('school_short_name', $settings['school_short_name'] ?? '') }}"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Motto</label>
                                <input type="text" name="school_motto"
                                    value="{{ old('school_motto', $settings['school_motto'] ?? '') }}"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Sambutan Kepala Sekolah</label>
                                <textarea name="headmaster_welcome" class="form-control">{{ old('headmaster_welcome', $settings['headmaster_welcome'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Google Site Verification</label>
                                <input type="text" name="google_site_verification"
                                    value="{{ old('google_site_verification', $settings['google_site_verification'] ?? '') }}"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Bing Verification</label>
                                <input type="text" name="msvalidate_01"
                                    value="{{ old('msvalidate_01', $settings['msvalidate_01'] ?? $settings['msvalidate.01'] ?? '') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="tab-pane" id="contact-info">
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="school_address" class="form-control">{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="school_phone"
                            value="{{ old('school_phone', $settings['school_phone'] ?? '') }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="school_email"
                            value="{{ old('school_email', $settings['school_email'] ?? '') }}"
                            class="form-control">
                    </div>
                </div>

                <!-- Social -->
                <div class="tab-pane" id="social">
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="url" name="school_facebook"
                            value="{{ old('school_facebook', $settings['school_facebook'] ?? '') }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="url" name="school_instagram"
                            value="{{ old('school_instagram', $settings['school_instagram'] ?? '') }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>YouTube</label>
                        <input type="url" name="school_youtube"
                            value="{{ old('school_youtube', $settings['school_youtube'] ?? '') }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tiktok</label>
                        <input type="url" name="school_tiktok"
                            value="{{ old('school_tiktok', $settings['school_tiktok'] ?? '') }}"
                            class="form-control">
                    </div>
                </div>

                <!-- SPMB -->
                <div class="tab-pane" id="spmb-settings">
                    <div class="form-group">
                        <label>Informasi SPMB</label>
                        <textarea name="spmb_info" class="form-control">{{ old('spmb_info', $settings['spmb_info'] ?? '') }}</textarea>
                    </div>

                    <hr class="my-4">
                    <h5 class="font-weight-bold mb-3">Jadwal Ujian Seleksi</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Ujian</label>
                                <input type="date" name="spmb_exam_date"
                                    value="{{ old('spmb_exam_date', $settings['spmb_exam_date'] ?? '') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Waktu Ujian</label>
                                <input type="time" name="spmb_exam_time"
                                    value="{{ old('spmb_exam_time', $settings['spmb_exam_time'] ?? '') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lokasi Ujian</label>
                                <input type="text" name="spmb_exam_location"
                                    value="{{ old('spmb_exam_location', $settings['spmb_exam_location'] ?? '') }}"
                                    class="form-control" placeholder="Contoh: Lab Komputer Sekolah">
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small">Peserta hanya bisa memulai ujian setelah waktu yang ditentukan tiba.</p>

                    <hr class="my-4">
                    <h5 class="font-weight-bold mb-3">Biaya Daftar Ulang Siswa Baru</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nominal Biaya Daftar Ulang Default (Rp)</label>
                                <input type="number" name="spmb_re_registration_fee" min="0" step="5000"
                                    value="{{ old('spmb_re_registration_fee', $settings['spmb_re_registration_fee'] ?? '150000') }}"
                                    class="form-control" placeholder="Contoh: 150000">
                                <small class="text-muted">Otomatis diterapkan pada pendaftar yang dinyatakan <strong>Lulus</strong> melalui SPK SAW apabila nominal biaya daftar ulangnya belum diatur secara khusus.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-check">
                        <input type="checkbox" name="spmb_disabled" value="1"
                            {{ ($settings['spmb_disabled'] ?? '') === '1' ? 'checked' : '' }}
                            class="form-check-input" id="spmbDisabled">
                        <label class="form-check-label" for="spmbDisabled">Nonaktifkan Halaman SPMB</label>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</div>
@endsection