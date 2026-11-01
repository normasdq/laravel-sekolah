@extends('admin.layouts.app')

@section('page_title', 'Detail Pendaftar')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-user-graduate text-primary mr-2"></i>Detail Pendaftar: {{ $registration->name }}
            </h4>
            <p class="text-muted small mb-0">
                No. Pendaftaran: <code>{{ $registration->registration_number ?? '-' }}</code> &bull; NISN: <code>{{ $registration->nisn }}</code>
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pendaftar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Data Pribadi -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-user mr-2 text-amber-600"></i>Data Pribadi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td style="width:180px" class="font-weight-semibold text-muted">Nama Lengkap</td>
                            <td>{{ $registration->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">NISN</td>
                            <td><code>{{ $registration->nisn }}</code></td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Jenis Kelamin</td>
                            <td>{{ $registration->gender_label }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Tempat, Tanggal Lahir</td>
                            <td>{{ $registration->birth_place }}, {{ $registration->birth_date?->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Asal Sekolah</td>
                            <td>{{ $registration->school_origin }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Telepon</td>
                            <td>{{ $registration->phone }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Email</td>
                            <td>{{ $registration->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Alamat</td>
                            <td>{{ $registration->address ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-users mr-2 text-info"></i>Data Orang Tua</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td style="width:180px" class="font-weight-semibold text-muted">Nama Orang Tua</td>
                            <td>{{ $registration->parent_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold text-muted">Telepon Orang Tua</td>
                            <td>{{ $registration->parent_phone ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Ringkasan Nilai Seleksi & SPK SAW -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-scale-balanced mr-2 text-primary"></i>Nilai Seleksi & SPK Metode SAW
                    </h3>
                    @if (in_array($registration->status, ['verified', 'lulus', 'tidak_lulus']))
                        <div>
                            <a href="{{ route('admin.spk.scores', ['search' => $registration->nisn]) }}" class="btn btn-xs btn-info font-weight-bold mr-1">
                                <i class="fas fa-pen-to-square mr-1"></i> Input / Edit Nilai SAW
                            </a>
                            <a href="{{ route('admin.spk.index') }}" class="btn btn-xs btn-outline-primary font-weight-bold">
                                <i class="fas fa-trophy mr-1"></i> Lihat Peringkat
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if (!in_array($registration->status, ['verified', 'lulus', 'tidak_lulus']))
                        <div class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i> Penilaian seleksi SPK SAW akan tersedia setelah berkas pendaftar diverifikasi.
                        </div>
                    @else
                        @php
                            $scoresByCriterion = $registration->sawScores->keyBy('saw_criterion_id');
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-3">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th class="text-center">Bobot</th>
                                        <th class="text-center">Nilai Mentah</th>
                                        <th class="text-center">Nilai Terbobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($criteria ?? [] as $c)
                                        @php
                                            $rawVal = null;
                                            if ($c->source === 'cbt_score') {
                                                $rawVal = $registration->examResult?->score;
                                            } else {
                                                $rawVal = $scoresByCriterion->get($c->id)?->value;
                                            }
                                            $weightedVal = ($sawCandidate['is_complete'] ?? false)
                                                ? ($sawCandidate['weighted_scores'][$c->id] ?? null)
                                                : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $c->code }}</strong> — {{ $c->name }}
                                                @if ($c->source === 'cbt_score')
                                                    <span class="badge badge-info ml-1">CBT Otomatis</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ (int) round($c->weight) }}%</td>
                                            <td class="text-center font-weight-bold">
                                                @if ($rawVal !== null)
                                                    {{ (int) round((float) $rawVal) }}
                                                @else
                                                    <span class="badge badge-warning text-dark">Belum Diisi</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $weightedVal !== null ? (int) round((float) $weightedVal * 100) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (!empty($sawCandidate) && ($sawCandidate['is_complete'] ?? false))
                            <div class="p-3 rounded border d-flex justify-content-between align-items-center {{ $sawCandidate['is_passed'] ? 'bg-light border-success' : 'bg-light border-danger' }}">
                                <div>
                                    <span class="small text-muted d-block">Skor Akhir Preferensi SAW (V) &bull; Peringkat <strong>#{{ $sawCandidate['rank'] }}</strong> dari {{ $sawCandidate['total_ranked'] }}</span>
                                    <span class="h5 font-weight-bold mb-0 {{ $sawCandidate['is_passed'] ? 'text-success' : 'text-danger' }}">
                                        {{ (int) round($sawCandidate['preference_score'] * 100) }}
                                    </span>
                                    <small class="text-muted ml-1">(Ambang Batas: {{ (int) round($sawCandidate['threshold'] * 100) }})</small>
                                </div>
                                <div>
                                    @if ($sawCandidate['is_passed'])
                                        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> REKOMENDASI: LULUS</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2"><i class="fas fa-times-circle mr-1"></i> REKOMENDASI: TIDAK LULUS</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning small mb-0">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Nilai kriteria belum lengkap. Silakan lengkapi nilai melalui tombol <strong>Input / Edit Nilai SAW</strong> di atas untuk melihat skor preferensi dan peringkat.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Dokumen Upload -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-file-alt mr-2 text-secondary"></i>Dokumen Terupload
                    </h3>
                    @if ($registration->kartu_keluarga || $registration->ijazah || $registration->akte_kelahiran)
                        <button type="button" id="btn-run-ai-audit" class="btn btn-sm text-white font-weight-bold shadow-sm"
                            style="background: linear-gradient(135deg, #1d4ed8, #0284c7); border: none;">
                            <i class="fas fa-robot mr-1"></i> Audit Berkas dengan AI
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <small class="d-block text-muted">Pas Foto Calon Siswa</small>
                            @if ($registration->pas_foto)
                                <a href="{{ route('admin.registrations.document', [$registration->id, 'pas_foto']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-image"></i> Lihat Foto</a>
                            @else
                                <span class="badge badge-secondary">Belum Upload</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="d-block text-muted">Kartu Keluarga</small>
                            @if ($registration->kartu_keluarga)
                                <a href="{{ route('admin.registrations.document', [$registration->id, 'kartu_keluarga']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            @else
                                <span class="badge badge-secondary">Belum Upload</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="d-block text-muted">Ijazah / SKL</small>
                            @if ($registration->ijazah)
                                <a href="{{ route('admin.registrations.document', [$registration->id, 'ijazah']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            @else
                                <span class="badge badge-secondary">Belum Upload</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="d-block text-muted">Akte Kelahiran</small>
                            @if ($registration->akte_kelahiran)
                                <a href="{{ route('admin.registrations.document', [$registration->id, 'akte_kelahiran']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>
                            @else
                                <span class="badge badge-secondary">Belum Upload</span>
                            @endif
                        </div>
                    </div>

                    <!-- AI Audit Result Box (Dynamic) -->
                    <div id="ai-audit-wrapper" class="mt-4 pt-3 border-top d-none">
                        <!-- Loading State -->
                        <div id="ai-audit-loading" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary mb-2" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="small text-muted mb-0 font-weight-bold">
                                <i class="fas fa-brain mr-1 text-primary"></i> AI Vision sedang membaca dan mencocokkan dokumen berkas...
                            </p>
                        </div>

                        <!-- Result Container -->
                        <div id="ai-audit-result" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-wand-magic-sparkles mr-1 text-warning"></i> Hasil Analisis AI Dokumen
                                </h6>
                                <span id="ai-audit-badge" class="badge px-3 py-1 font-weight-bold">Status</span>
                            </div>

                            <div id="ai-audit-summary-alert" class="alert small mb-3">
                                <span id="ai-audit-summary-text"></span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm small mb-2">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Dokumen</th>
                                            <th>Hasil Baca OCR</th>
                                            <th>Data Formulir</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ai-audit-table-body">
                                        <!-- Injected via JS -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="bg-light p-2.5 rounded border mt-2 small d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-secondary">Rekomendasi Catatan AI:</strong>
                                    <span id="ai-suggested-note" class="text-dark ml-1"></span>
                                </div>
                                <button type="button" id="btn-copy-ai-note" class="btn btn-outline-info btn-xs text-nowrap ml-2" title="Salin catatan ini ke form catatan verifikasi">
                                    <i class="fas fa-copy mr-1"></i> Salin ke Catatan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-clipboard-check mr-2 text-success"></i>Status
                        Verifikasi</h3>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <i
                            class="fas {{ $registration->status == 'verified' ? 'fa-check-circle' : ($registration->status == 'rejected' ? 'fa-times-circle' : 'fa-hourglass-half') }} text-4xl text-{{ $registration->status_color }} mb-2"></i>
                        <h4 class="font-weight-bold text-{{ $registration->status_color }}">
                            {{ $registration->status_label }}</h4>
                        <small class="text-muted">Terdaftar: {{ $registration->created_at->format('d F Y, H:i') }}</small>
                    </div>

                    <!-- TOMBOL KHUSUS: VERIFIKASI DOKUMEN LENGKAP -->
                    @if ($registration->status == 'pending' && $registration->documents_complete)
                        <div class="alert alert-info p-2">
                            <small><strong>Info:</strong> Dokumen terlihat lengkap. Klik tombol di bawah untuk memverifikasi
                                dan mengirim jadwal tes.</small>
                        </div>

                        <form method="POST"
                            action="{{ route('admin.registrations.verify-documents', $registration->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-check-double mr-1"></i> Verifikasi & Kirim Jadwal
                            </button>
                        </form>
                    @endif

                    <!-- Form Ubah Status Manual -->
                    <form method="POST" action="{{ route('admin.registrations.update', $registration) }}">
                        @csrf @method('PUT')
                        <div class="form-group mt-3">
                            <label class="font-weight-semibold">Ubah Status Manual</label>
                            <select name="status" required class="form-control">
                                <option value="pending" {{ $registration->status === 'pending' ? 'selected' : '' }}>
                                    Menunggu</option>
                                <option value="verified" {{ $registration->status === 'verified' ? 'selected' : '' }}>
                                    Terverifikasi</option>
                                <option value="rejected" {{ $registration->status === 'rejected' ? 'selected' : '' }}>
                                    Ditolak</option>
                                <option value="lulus" {{ $registration->status === 'lulus' ? 'selected' : '' }}>
                                    Lulus</option>
                                <option value="tidak_lulus" {{ $registration->status === 'tidak_lulus' ? 'selected' : '' }}>
                                    Tidak Lulus</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Catatan (Untuk Siswa)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan verifikasi / Jadwal Tes">{{ old('notes', $registration->notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save mr-2"></i>Simpan
                            Status</button>
                    </form>
                </div>
            </div>

            <!-- Biaya Pendaftaran Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-money-bill mr-2 text-success"></i>Biaya Pendaftaran</h3>
                </div>
                <div class="card-body">
                    @if ($registration->payment_status === 'paid')
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle text-4xl text-emerald-500 mb-2"></i>
                            <h4 class="font-weight-bold text-emerald-600">Lunas</h4>
                            <small class="text-muted">Dibayar: {{ $registration->paid_at?->format('d F Y, H:i') }}</small>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.registrations.update-payment', $registration) }}">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-semibold">Nominal Biaya Daftar Ulang (Rp)</label>
                                <input type="number" name="payment_amount" class="form-control"
                                    value="{{ old('payment_amount', $registration->payment_amount ?? '') }}"
                                    min="0" step="5000" placeholder="Contoh: 150000">
                                <small class="text-muted">Kosongkan jika gratis. Pembayaran daftar ulang hanya muncul setelah status siswa "Lulus".</small>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-semibold">Status Pembayaran</label>
                                <select name="payment_status" class="form-control">
                                    <option value="unpaid" {{ $registration->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="paid" {{ $registration->payment_status === 'paid' ? 'selected' : '' }}>Lunas (Manual)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-save mr-2"></i>Simpan Biaya
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-default"><i
                class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar</a>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAudit = document.getElementById('btn-run-ai-audit');
    const wrapper = document.getElementById('ai-audit-wrapper');
    const loading = document.getElementById('ai-audit-loading');
    const resultBox = document.getElementById('ai-audit-result');
    const badge = document.getElementById('ai-audit-badge');
    const summaryAlert = document.getElementById('ai-audit-summary-alert');
    const summaryText = document.getElementById('ai-audit-summary-text');
    const tableBody = document.getElementById('ai-audit-table-body');
    const suggestedNote = document.getElementById('ai-suggested-note');
    const btnCopyNote = document.getElementById('btn-copy-ai-note');
    const notesTextarea = document.querySelector('textarea[name="notes"]');

    btnAudit?.addEventListener('click', function () {
        wrapper.classList.remove('d-none');
        loading.classList.remove('d-none');
        resultBox.classList.add('d-none');
        btnAudit.disabled = true;
        btnAudit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menganalisis...';

        fetch("{{ route('admin.registrations.ai-audit', $registration) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(res => {
            loading.classList.add('d-none');
            btnAudit.disabled = false;
            btnAudit.innerHTML = '<i class="fas fa-rotate mr-1"></i> Audit Ulang dengan AI';

            if (res.status === 'success' && res.data) {
                const data = res.data;
                const status = (data.overall_status || 'MATCH').toUpperCase();
                const confidence = data.overall_confidence || 95;

                badge.className = 'badge px-3 py-1 font-weight-bold';
                summaryAlert.className = 'alert small mb-3';

                if (status === 'MATCH') {
                    badge.classList.add('badge-success');
                    badge.textContent = `Valid & Cocok (${confidence}%)`;
                    summaryAlert.classList.add('alert-success');
                } else if (status === 'WARNING') {
                    badge.classList.add('badge-warning');
                    badge.textContent = `Perlu Cek Ulang (${confidence}%)`;
                    summaryAlert.classList.add('alert-warning');
                } else {
                    badge.classList.add('badge-danger');
                    badge.textContent = `Tidak Sesuai (${confidence}%)`;
                    summaryAlert.classList.add('alert-danger');
                }

                summaryText.textContent = data.summary_notes || 'Analisis berkas selesai.';
                suggestedNote.textContent = data.suggested_admin_note || '-';

                tableBody.innerHTML = '';
                if (Array.isArray(data.checks)) {
                    data.checks.forEach(c => {
                        const tr = document.createElement('tr');
                        const isOk = c.status === 'valid';
                        tr.innerHTML = `
                            <td class="font-weight-bold text-nowrap">${escapeHtml(c.document)}</td>
                            <td>${escapeHtml(c.detected_info || '-')}</td>
                            <td>${escapeHtml(c.expected_info || '-')}</td>
                            <td class="text-center text-nowrap">
                                <span class="badge ${isOk ? 'badge-success' : 'badge-warning'}">
                                    ${isOk ? 'Cocok' : 'Perhatian'}
                                </span>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                }

                resultBox.classList.remove('d-none');
            } else {
                alert(res.message || 'Gagal melakukan audit berkas.');
            }
        })
        .catch(err => {
            loading.classList.add('d-none');
            btnAudit.disabled = false;
            btnAudit.innerHTML = '<i class="fas fa-robot mr-1"></i> Audit Berkas dengan AI';
            alert('Terjadi kesalahan saat memproses audit AI: ' + err.message);
        });
    });

    btnCopyNote?.addEventListener('click', function () {
        const text = suggestedNote.textContent;
        if (text && notesTextarea) {
            notesTextarea.value = text;
            btnCopyNote.innerHTML = '<i class="fas fa-check mr-1 text-success"></i> Tersalin!';
            setTimeout(() => {
                btnCopyNote.innerHTML = '<i class="fas fa-copy mr-1"></i> Salin ke Catatan';
            }, 2000);
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>
@endpush

