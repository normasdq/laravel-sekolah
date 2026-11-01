@extends('layouts.app')

@section('title', 'Profil Pendaftar - ' . auth()->user()->name)

@section('content')
    <section class="min-h-screen bg-white pt-32 pb-20">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">

            <!-- Header Profil -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Header Profil -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 fade-up">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        @if ($registration->pas_foto)
                            <img src="{{ route('pendaftar.document', 'pas_foto') }}" alt="Pas Foto {{ $registration->name }}"
                                class="w-24 h-24 rounded-[2rem] object-cover shadow-xl shadow-primary-500/20 border-2 border-primary-500">
                        @else
                            <div class="w-24 h-24 bg-primary-500 rounded-[2rem] flex items-center justify-center text-white shadow-xl shadow-primary-500/20">
                                <i class="fa-solid fa-user-graduate text-4xl"></i>
                            </div>
                        @endif
                        <button type="button" onclick="document.getElementById('upload-photo-modal').classList.remove('hidden')"
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-primary-600 hover:bg-primary-500 border-4 border-white rounded-full flex items-center justify-center text-white shadow-lg transition-all"
                            title="Unggah / Ganti Pas Foto">
                            <i class="fa-solid fa-camera text-xs"></i>
                        </button>
                    </div>
                    <div>
                        <h1 class="font-display text-3xl font-black text-dark-900 tracking-tight">{{ $registration->name }}
                        </h1>
                        <p class="text-dark-500 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-id-card text-primary-500"></i>
                            NISN: {{ $registration->nisn }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pendaftar.kartu') }}" target="_blank"
                        class="px-5 py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-sm flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-id-badge"></i> Cetak Kartu Peserta
                    </a>
                    <a href="{{ route('pendaftar.download') }}" target="_blank"
                        class="px-5 py-3 bg-white border border-gray-200 text-dark-700 font-bold rounded-2xl hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-print"></i> Cetak Bukti
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Kolom Kiri: Detail Informasi -->
                <div class="lg:col-span-2 space-y-8 fade-up" style="transition-delay: 100ms">

                    <!-- Data Pendidikan & Pribadi -->
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-sm">
                        <h3 class="font-display font-bold text-dark-900 text-lg mb-8 flex items-center gap-3">
                            <span class="w-2 h-6 bg-primary-500 rounded-full"></span>
                            Informasi Biodata
                        </h3>

                        <div class="grid md:grid-cols-2 gap-y-8 gap-x-12">
                            <div class="group">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1 group-hover:text-primary-500 transition-colors">
                                    Asal Sekolah</p>
                                <p class="font-semibold text-dark-900">{{ $registration->school_origin }}</p>
                            </div>
                            <div class="group">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1 group-hover:text-primary-500 transition-colors">
                                    Tempat, Tanggal Lahir</p>
                                <p class="font-semibold text-dark-900">{{ $registration->birth_place }},
                                    {{ $registration->birth_date->format('d F Y') }}</p>
                            </div>
                            <div class="group">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1 group-hover:text-primary-500 transition-colors">
                                    Jenis Kelamin</p>
                                <p class="font-semibold text-dark-900">{{ $registration->gender_label }}</p>
                            </div>
                            <div class="group">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1 group-hover:text-primary-500 transition-colors">
                                    Nomor WhatsApp</p>
                                <p class="font-semibold text-dark-900">{{ $registration->phone }}</p>
                            </div>
                            <div class="md:col-span-2 group">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1 group-hover:text-primary-500 transition-colors">
                                    Alamat Lengkap</p>
                                <p class="font-semibold text-dark-900 leading-relaxed">{{ $registration->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-sm">
                        <h3 class="font-display font-bold text-dark-900 text-lg mb-8 flex items-center gap-3">
                            <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                            Data Orang Tua / Wali
                        </h3>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1">Nama
                                    Ayah/Ibu/Wali</p>
                                <p class="font-semibold text-dark-900">{{ $registration->parent_name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-dark-400 mb-1">Telepon Orang
                                    Tua</p>
                                <p class="font-semibold text-dark-900">{{ $registration->parent_phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Status & Dokumen -->
                <div class="space-y-8 fade-up" style="transition-delay: 200ms">

                    <!-- Card Status -->
                    <div class="bg-dark-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-xl">
                        <div class="relative z-10">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/50 mb-6">Status
                                Pendaftaran</p>
                            <div
                                class="inline-flex px-4 py-2 rounded-xl bg-{{ $registration->status_color }}-500/20 text-{{ $registration->status_color }}-400 text-xs font-bold border border-{{ $registration->status_color }}-500/30 mb-4">
                                {{ $registration->status_label }}
                            </div>
                            <h4 class="font-display text-xl font-bold mb-2">No. Registrasi:</h4>
                            <p class="text-3xl font-black tracking-tighter text-primary-400">
                                {{ $registration->registration_number }}</p>
                        </div>
                        <!-- Glow decoration -->
                        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary-500/20 rounded-full blur-3xl"></div>
                    </div>

                    <!-- Card Dokumen -->
                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-display font-bold text-dark-900 text-lg">Berkas Terunggah</h3>
                            <button type="button" onclick="document.getElementById('upload-photo-modal').classList.remove('hidden')"
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-camera"></i>
                                <span>{{ $registration->pas_foto ? 'Ganti Foto' : 'Unggah Foto' }}</span>
                            </button>
                        </div>
                        <div class="space-y-4">
                            @foreach ([
                                ['Pas Foto Calon Siswa', $registration->pas_foto, 'pas_foto', 'fa-image'],
                                ['Kartu Keluarga', $registration->kartu_keluarga, 'kartu_keluarga', 'fa-file-pdf'],
                                ['Ijazah / SKL', $registration->ijazah, 'ijazah', 'fa-file-pdf'],
                                ['Akte Kelahiran', $registration->akte_kelahiran, 'akte_kelahiran', 'fa-file-pdf']
                            ] as $doc)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-primary-200 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all">
                                            <i class="fa-solid {{ $doc[3] }}"></i>
                                        </div>
                                        <span class="text-xs font-bold text-dark-700">{{ $doc[0] }}</span>
                                    </div>
                                    @if ($doc[1])
                                        <a href="{{ route('pendaftar.document', $doc[2]) }}" target="_blank"
                                            class="text-xs font-black uppercase tracking-widest text-primary-600 hover:text-primary-700">Lihat</a>
                                    @else
                                        @if ($doc[2] === 'pas_foto')
                                            <button type="button" onclick="document.getElementById('upload-photo-modal').classList.remove('hidden')"
                                                class="text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200">
                                                Unggah
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">Belum Diunggah</span>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Alert Bantuan -->
                    <div class="p-6 bg-blue-50 rounded-[2rem] border border-blue-100 flex items-start gap-4">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Data ini bersifat permanen. Jika terdapat kesalahan input, silakan hubungi <strong>Sekretariat
                                PPDB</strong> melalui WhatsApp.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Unggah Pas Foto Digital -->
    <div id="upload-photo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark-950/70 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-gray-100 relative animate-fade-in">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-dark-900">Unggah Pas Foto</h3>
                        <p class="text-xs text-dark-500">Foto resmi kartu ujian (3x4 / 4x6)</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('upload-photo-modal').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg text-dark-400 hover:text-dark-700 hover:bg-gray-100 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('pendaftar.profile.update-photo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4 mb-6">
                    <div class="p-6 rounded-2xl border-2 border-dashed border-gray-200 hover:border-primary-300 bg-gray-50/50 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <label class="block cursor-pointer">
                            <span class="text-xs font-bold text-dark-800 block mb-1">Pilih File Pas Foto Digital</span>
                            <span class="text-[11px] text-dark-400 block mb-3 font-mono">Format JPG, PNG, WEBP (Maksimal 10MB)</span>
                            <input type="file" name="pas_foto" accept=".jpg,.jpeg,.png,.webp" required
                                class="block w-full text-xs text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all cursor-pointer">
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5">
                    <button type="submit"
                        class="w-full py-3.5 bg-primary-600 hover:bg-primary-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary-500/25 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Simpan & Pasang Pas Foto</span>
                    </button>
                    <button type="button" onclick="document.getElementById('upload-photo-modal').classList.add('hidden')"
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-dark-700 font-semibold text-xs rounded-xl transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
