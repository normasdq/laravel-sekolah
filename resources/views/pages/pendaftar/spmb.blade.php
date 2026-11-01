@extends('layouts.app')

@section('title', 'Pendaftaran Online - ' . $settings->get('school_name'))

@section('content')
    <!-- Header Section -->
    <section class="relative pt-32 pb-20 bg-dark-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="{{ asset('assets/images/pattern-dots.png') }}" class="w-full h-full object-cover">
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
            <span
                class="inline-block px-4 py-1.5 bg-primary-500/10 text-primary-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-full mb-6 border border-primary-500/20">
                Admission 2026/2027
            </span>
            <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
                Pendaftaran <span class="text-primary-500">Siswa Baru</span>
            </h1>
            <p class="text-dark-300 max-w-2xl mx-auto text-lg leading-relaxed">
                Langkah awal menuju masa depan gemilang dimulai di sini. Lengkapi data diri Anda untuk memulai proses
                seleksi.
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">

            <!-- Requirement Sidebar & Form Grid -->
            <div class="grid lg:grid-cols-12 gap-12">

                <!-- Sidebar Info -->
                <aside class="lg:col-span-4 space-y-8">
                    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm sticky top-32">
                        <h3 class="font-display font-bold text-dark-900 mb-6 flex items-center gap-3">
                            <i class="fa-solid fa-list-check text-primary-500"></i>
                            Persyaratan
                        </h3>
                        <ul class="space-y-4">
                            @foreach (['Scan Kartu Keluarga', 'Scan Ijazah / SKL', 'Scan Akta Kelahiran', 'Pas Foto Digital', 'Rapor Semester 1-5', 'NISN 10 Digit'] as $req)
                                <li class="flex items-start gap-3">
                                    <div
                                        class="mt-1 w-5 h-5 bg-emerald-50 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] text-emerald-600">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="text-dark-600 text-xs font-medium">{{ $req }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <p class="text-xs text-dark-500 leading-relaxed italic">
                                * Pastikan dokumen dalam format PDF, JPG, JPEG, PNG, atau WEBP dengan ukuran maksimal 10MB per file.
                            </p>
                        </div>
                    </div>
                </aside>

                <!-- Form Section -->
                <div class="lg:col-span-8">
                    <!-- Flash Error Alert -->
                    @if (session('error'))
                        <div class="mb-8 p-6 rounded-2xl bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-600 text-xl flex-shrink-0"></i>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors Banner -->
                    @if ($errors->any())
                        <div class="mb-8 p-6 rounded-2xl bg-red-50 border-2 border-red-300 text-red-900 shadow-md">
                            <div class="flex items-center gap-3 mb-3 font-bold text-base text-red-900">
                                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl flex-shrink-0"></i>
                                <span>Pendaftaran Belum Dapat Diproses (Terdapat {{ $errors->count() }} Isian Belum Lengkap/Sesuai):</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1.5 text-xs text-red-800 font-medium ml-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pendaftar.spmb.store') }}" id="spmb-form"
                        enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Section 1: Data Pribadi -->
                        <div
                            class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-xl shadow-primary-900/5">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-dark-900 text-xl">Identitas Calon Siswa</h3>
                                    <p class="text-xs text-dark-400 uppercase tracking-widest font-bold mt-1">Lengkapi data
                                        diri sesuai Akta/KK</p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Nama
                                        Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all text-sm @error('name') border-red-400 bg-red-50/30 @enderror"
                                        placeholder="Masukkan nama lengkap sesuai ijazah/akta">
                                    @error('name')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">NISN (10
                                        Digit) <span class="text-red-500">*</span></label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all text-sm @error('nisn') border-red-400 bg-red-50/30 @enderror"
                                        placeholder="Contoh: 0012345678">
                                    @error('nisn')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Jenis
                                        Kelamin <span class="text-red-500">*</span></label>
                                    <select name="gender" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('gender') border-red-400 bg-red-50/30 @enderror">
                                        <option value="" class="text-gray-400">Pilih Jenis Kelamin...</option>
                                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }} class="text-gray-900">Laki-laki</option>
                                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }} class="text-gray-900">Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Tempat
                                        Lahir <span class="text-red-500">*</span></label>
                                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('birth_place') border-red-400 bg-red-50/30 @enderror"
                                        placeholder="Kota / Kabupaten Kelahiran">
                                    @error('birth_place')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Tanggal
                                        Lahir <span class="text-red-500">*</span></label>
                                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('birth_date') border-red-400 bg-red-50/30 @enderror">
                                    @error('birth_date')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Kontak & Alamat -->
                        <div
                            class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-xl shadow-primary-900/5">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-dark-900 text-xl">Kontak & Alamat</h3>
                                    <p class="text-xs text-dark-400 uppercase tracking-widest font-bold mt-1">Informasi
                                        tempat tinggal dan kontak aktif</p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Asal
                                        Sekolah <span class="text-red-500">*</span></label>
                                    <input type="text" name="school_origin" value="{{ old('school_origin') }}"
                                        placeholder="Contoh: SD Negeri 01 Bekasi" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('school_origin') border-red-400 bg-red-50/30 @enderror">
                                    @error('school_origin')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">No. HP
                                        WhatsApp <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                                        required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('phone') border-red-400 bg-red-50/30 @enderror">
                                    @error('phone')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                {{-- email --}}
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Email Aktif</label>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                        placeholder="email@contoh.com"
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('email') border-red-400 bg-red-50/30 @enderror">
                                    @error('email')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                {{-- parent name --}}
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Nama
                                        Orang Tua / Wali <span class="text-red-500">*</span></label>
                                    <input type="text" name="parent_name" value="{{ old('parent_name') }}"
                                        placeholder="Nama lengkap ayah/ibu/wali" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('parent_name') border-red-400 bg-red-50/30 @enderror">
                                    @error('parent_name')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                {{-- phone parent --}}
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">No. HP
                                        WhatsApp Orang Tua <span class="text-red-500">*</span></label>
                                    <input type="tel" name="parent_phone" value="{{ old('parent_phone') }}"
                                        placeholder="081234567890" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm @error('parent_phone') border-red-400 bg-red-50/30 @enderror">
                                    @error('parent_phone')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                {{-- address --}}
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700 ml-1">Alamat
                                        Lengkap Tempat Tinggal <span class="text-red-500">*</span></label>
                                    <textarea name="address" rows="3" required
                                        class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400 font-semibold focus:bg-white focus:border-primary-500 outline-none transition-all text-sm resize-none @error('address') border-red-400 bg-red-50/30 @enderror"
                                        placeholder="Nama jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="text-red-500 text-xs font-bold mt-1.5 ml-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Upload Berkas -->
                        <div
                            class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-xl shadow-primary-900/5">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-dark-900 text-xl">Berkas Pendukung</h3>
                                    <p class="text-xs text-dark-400 uppercase tracking-widest font-bold mt-1">Pastikan
                                        dokumen terbaca jelas (Maks. 10MB per file)</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @foreach ([
                                    ['pas_foto', 'Pas Foto Calon Siswa (Formal 3x4 / 4x6)', '.jpg,.jpeg,.png,.webp', 'JPG, PNG, WEBP (Maks 10MB)'],
                                    ['kartu_keluarga', 'Scan Kartu Keluarga (KK)', '.pdf,.jpg,.jpeg,.png,.webp', 'PDF, JPG, PNG, WEBP (Maks 10MB)'],
                                    ['ijazah', 'Scan Ijazah / Surat Keterangan Lulus (SKL)', '.pdf,.jpg,.jpeg,.png,.webp', 'PDF, JPG, PNG, WEBP (Maks 10MB)'],
                                    ['akte_kelahiran', 'Scan Akte Kelahiran', '.pdf,.jpg,.jpeg,.png,.webp', 'PDF, JPG, PNG, WEBP (Maks 10MB)']
                                ] as $file)
                                    <div
                                        class="p-6 rounded-2xl border-2 border-dashed @error($file[0]) border-red-300 bg-red-50/20 @else border-gray-200 hover:border-primary-300 bg-gray-50/30 @enderror transition-all">
                                        <label class="block">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-xs font-bold uppercase tracking-wider text-gray-800 block">
                                                    {{ $file[1] }} <span class="text-red-500">*</span>
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-mono">{{ $file[3] }}</span>
                                            </div>
                                            <input type="file" name="{{ $file[0] }}"
                                                accept="{{ $file[2] }}" required
                                                class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all cursor-pointer">
                                        </label>
                                        @error($file[0])
                                            <p class="text-red-500 text-xs font-bold mt-2 ml-1 flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <span>{{ $message }}</span>
                                            </p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- CTA Section -->
                        <div class="bg-primary-900 rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden">
                            <div class="relative z-10">
                                <h4 class="font-display font-bold text-xl mb-4 italic">Pernyataan Kejujuran</h4>
                                <p class="text-primary-100 text-sm leading-relaxed mb-8">
                                    Dengan menekan tombol daftar di bawah, saya menyatakan bahwa seluruh data yang saya
                                    isikan adalah benar dan dapat dipertanggungjawabkan sesuai hukum yang berlaku.
                                </p>
                                <button type="submit" id="submit-btn"
                                    class="w-full py-5 bg-primary-500 text-white font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-primary-400 transition-all transform active:scale-95 shadow-xl shadow-black/20 flex items-center justify-center gap-3">
                                    <span>Kirim Pendaftaran</span>
                                    <i class="fa-solid fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary-500/10 rounded-full blur-3xl">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('spmb-form');
            const submitBtn = document.getElementById('submit-btn');

            // File size validation on client side
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const maxSize = 10 * 1024 * 1024; // 10MB
                        if (file.size > maxSize) {
                            alert('Ukuran file "' + file.name + '" terlalu besar (' + (file.size / (1024*1024)).toFixed(1) + ' MB). Maksimal ukuran file adalah 10MB.');
                            this.value = '';
                        }
                    }
                });
            });

            // Safe submit handling that doesn't block browser submission
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    setTimeout(function() {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Mengirim Berkas Pendaftaran...';
                    }, 50);
                });
            }
        });
    </script>
@endpush
