@extends('admin.layouts.app')

@section('title', 'Bank Soal CBT')
@section('page_title', 'Bank Soal CBT')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-laptop-code text-primary mr-2"></i>Bank Soal Ujian Seleksi (CBT)
            </h4>
            <p class="text-muted small mb-0">
                Kelola butir soal pilihan ganda, kunci jawaban, bobot poin, atau buat paket soal otomatis dengan bantuan AI.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <button type="submit" form="bulk-delete-form" id="bulk-delete-btn" class="btn btn-danger btn-sm shadow-sm font-weight-bold"
                style="display:none;" onclick="return confirm('Yakin hapus soal yang dipilih?')">
                <i class="fas fa-trash mr-1"></i> Hapus Terpilih
            </button>
            <button type="button" class="btn btn-info btn-sm shadow-sm font-weight-bold" data-toggle="modal" data-target="#aiExamModal"
                style="background: linear-gradient(135deg, #2563eb, #06b6d4); border: none;">
                <i class="fas fa-wand-magic-sparkles mr-1"></i> Generate Soal AI
            </button>
            <a href="{{ route('admin.examp.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Soal Manual
            </a>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm mb-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-circle-question text-primary mr-2"></i>Daftar Bank Soal Pilihan Ganda
            </h3>
            <span class="badge badge-light border px-2 py-1">
                Total: <strong>{{ $questions->total() }}</strong> Butir Soal
            </span>
        </div>
        <div class="card-body table-responsive p-0">
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.examp.bulk-delete') }}">
                @csrf
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px" class="text-center"><input type="checkbox" id="select-all"></th>
                            <th style="width: 55px" class="text-center">No</th>
                            <th>Pertanyaan Soal</th>
                            <th class="text-center" style="width: 130px;">Kunci Jawaban</th>
                            <th class="text-center" style="width: 110px;">Bobot Poin</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $q)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $q->id }}"></td>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="text-dark font-weight-semibold" style="max-width: 620px; line-height: 1.5;">
                                        {{ Str::limit(strip_tags($q->question_text), 110) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-3 py-1 font-weight-bold">{{ $q->correct_answer }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $q->points }} Poin</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.examp.edit', $q->id) }}" class="btn btn-info btn-xs" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs" title="Hapus"
                                        onclick="if(confirm('Hapus soal ini?')) document.getElementById('delete-q-{{ $q->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada data soal yang tersedia di Bank Soal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            @foreach($questions as $q)
                <form id="delete-q-{{ $q->id }}" action="{{ route('admin.examp.destroy', $q->id) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>
        @if ($questions->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $questions->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- ==================== MODAL AI GENERATOR SOAL CBT ==================== -->
    <div class="modal fade" id="aiExamModal" tabindex="-1" role="dialog" aria-labelledby="aiExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <!-- Modal Header -->
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e3a8a, #0284c7);">
                    <h5 class="modal-title font-weight-bold" id="aiExamModalLabel">
                        <i class="fas fa-wand-magic-sparkles mr-2 text-warning"></i> AI Generator Bank Soal CBT
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <!-- Form Input Parameters -->
                    <div id="ai-form-step">
                        <p class="text-muted small mb-3">
                            Tentukan parameter mata pelajaran dan materi. AI akan merumuskan paket soal pilihan ganda (A, B, C, D) lengkap dengan kunci jawaban dan pembahasan secara instan.
                        </p>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Mata Pelajaran <span class="text-danger">*</span></label>
                                <select id="ai-subject" class="form-control form-control-sm">
                                    <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                                    <option value="Matematika">Matematika</option>
                                    <option value="Ilmu Pengetahuan Alam">Ilmu Pengetahuan Alam (IPA)</option>
                                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                    <option value="Bahasa Inggris">Bahasa Inggris</option>
                                    <option value="Tes Potensi Akademik / Logika">Tes Potensi Akademik / Logika</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Topik / Bahasan Khusus <span class="text-danger">*</span></label>
                                <input type="text" id="ai-topic" class="form-control form-control-sm"
                                    placeholder="Contoh: Fiqih Thaharah, Aljabar Dasar, Sistem Tata Surya" value="Rukun Shalat dan Zakat">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Tingkat Kesulitan</label>
                                <select id="ai-difficulty" class="form-control form-control-sm">
                                    <option value="mudah">Mudah</option>
                                    <option value="sedang" selected>Sedang</option>
                                    <option value="sulit">Sulit</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Jumlah Soal</label>
                                <input type="number" id="ai-count" class="form-control form-control-sm" min="1" max="10" value="5">
                                <small class="text-muted">Maksimal 10 soal per generate</small>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Poin Tiap Soal</label>
                                <input type="number" id="ai-points" class="form-control form-control-sm" min="1" max="100" value="2">
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-secondary btn-sm mr-2" data-dismiss="modal">Tutup</button>
                            <button type="button" id="btn-start-ai-generate" class="btn btn-primary btn-sm font-weight-bold px-4"
                                style="background: linear-gradient(135deg, #2563eb, #0284c7); border: none;">
                                <i class="fas fa-play mr-1"></i> Mulai Generate Soal
                            </button>
                        </div>
                    </div>

                    <!-- Loading Animation Step -->
                    <div id="ai-loading-step" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3.5rem; height: 3.5rem;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Kecerdasan Buatan Sedang Bekerja...</h6>
                        <p class="text-muted small mb-0">Merumuskan butir pertanyaan, kunci jawaban, dan pembahasan kontekstual.</p>
                    </div>

                    <!-- Result Preview Step -->
                    <div id="ai-result-step" class="d-none">
                        <div id="ai-result-alert" class="alert alert-info py-2 px-3 small mb-3">
                            <i class="fas fa-info-circle mr-1"></i> <span id="ai-alert-text">Soal berhasil dibuat. Silakan tinjau sebelum disimpan.</span>
                        </div>

                        <div id="ai-questions-list" style="max-height: 400px; overflow-y: auto;" class="pr-2 space-y-3">
                            <!-- Injected via JavaScript -->
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <button type="button" id="btn-back-to-form" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Atur Ulang
                            </button>
                            <div>
                                <span class="mr-3 font-weight-bold text-muted small" id="ai-summary-count">5 Soal</span>
                                <button type="button" id="btn-save-ai-batch" class="btn btn-success btn-sm font-weight-bold px-4 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan Semua ke Bank Soal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select all checkbox logic
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    function updateBulkBtn() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (bulkDeleteBtn) {
            bulkDeleteBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkBtn();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBtn);
    });

    // AI Exam Generator Logic
    const btnStart = document.getElementById('btn-start-ai-generate');
    const formStep = document.getElementById('ai-form-step');
    const loadingStep = document.getElementById('ai-loading-step');
    const resultStep = document.getElementById('ai-result-step');
    const questionsList = document.getElementById('ai-questions-list');
    const btnBack = document.getElementById('btn-back-to-form');
    const btnSaveBatch = document.getElementById('btn-save-ai-batch');
    const summaryCount = document.getElementById('ai-summary-count');
    const alertText = document.getElementById('ai-alert-text');

    let generatedQuestions = [];

    btnStart?.addEventListener('click', function () {
        const subject = document.getElementById('ai-subject').value;
        const topic = document.getElementById('ai-topic').value.trim();
        const difficulty = document.getElementById('ai-difficulty').value;
        const count = document.getElementById('ai-count').value;
        const points = document.getElementById('ai-points').value;

        if (!topic) {
            alert('Mohon isi topik atau bahasan khusus materi soal.');
            return;
        }

        formStep.classList.add('d-none');
        loadingStep.classList.remove('d-none');
        resultStep.classList.add('d-none');

        fetch("{{ route('admin.examp.ai-generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ subject, topic, difficulty, count, points })
        })
        .then(res => res.json())
        .then(res => {
            loadingStep.classList.add('d-none');
            if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                generatedQuestions = res.data;
                renderQuestionsPreview(generatedQuestions);
                alertText.textContent = res.message || 'Soal berhasil di-generate. Silakan tinjau sebelum disimpan.';
                resultStep.classList.remove('d-none');
            } else {
                alert(res.message || 'Gagal membuat soal. Silakan coba kembali.');
                formStep.classList.remove('d-none');
            }
        })
        .catch(err => {
            loadingStep.classList.add('d-none');
            formStep.classList.remove('d-none');
            alert('Terjadi kesalahan koneksi server: ' + err.message);
        });
    });

    function renderQuestionsPreview(list) {
        questionsList.innerHTML = '';
        summaryCount.textContent = list.length + ' Soal Siap Disimpan';

        list.forEach((q, idx) => {
            const card = document.createElement('div');
            card.className = 'card border mb-3 shadow-none';
            card.style.borderRadius = '10px';
            card.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-primary px-2 py-1">Soal #${idx + 1} (${q.points} Poin)</span>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-q" data-idx="${idx}" title="Hapus soal ini dari paket">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="font-weight-bold text-dark mb-2">${escapeHtml(q.question_text)}</p>
                    <div class="row small text-muted">
                        <div class="col-md-6 mb-1 ${q.correct_answer === 'A' ? 'font-weight-bold text-success' : ''}">
                            A. ${escapeHtml(q.option_a)} ${q.correct_answer === 'A' ? '<i class="fas fa-check-circle text-success ml-1"></i>' : ''}
                        </div>
                        <div class="col-md-6 mb-1 ${q.correct_answer === 'B' ? 'font-weight-bold text-success' : ''}">
                            B. ${escapeHtml(q.option_b)} ${q.correct_answer === 'B' ? '<i class="fas fa-check-circle text-success ml-1"></i>' : ''}
                        </div>
                        <div class="col-md-6 mb-1 ${q.correct_answer === 'C' ? 'font-weight-bold text-success' : ''}">
                            C. ${escapeHtml(q.option_c)} ${q.correct_answer === 'C' ? '<i class="fas fa-check-circle text-success ml-1"></i>' : ''}
                        </div>
                        <div class="col-md-6 mb-1 ${q.correct_answer === 'D' ? 'font-weight-bold text-success' : ''}">
                            D. ${escapeHtml(q.option_d)} ${q.correct_answer === 'D' ? '<i class="fas fa-check-circle text-success ml-1"></i>' : ''}
                        </div>
                    </div>
                    ${q.explanation ? `<div class="bg-light p-2 rounded mt-2 small text-secondary"><strong>Pembahasan:</strong> ${escapeHtml(q.explanation)}</div>` : ''}
                </div>
            `;
            questionsList.appendChild(card);
        });

        // Bind remove buttons
        document.querySelectorAll('.btn-remove-q').forEach(btn => {
            btn.addEventListener('click', function () {
                const removeIdx = parseInt(this.getAttribute('data-idx'));
                generatedQuestions.splice(removeIdx, 1);
                if (generatedQuestions.length === 0) {
                    btnBack.click();
                } else {
                    renderQuestionsPreview(generatedQuestions);
                }
            });
        });
    }

    btnBack?.addEventListener('click', function () {
        resultStep.classList.add('d-none');
        formStep.classList.remove('d-none');
    });

    btnSaveBatch?.addEventListener('click', function () {
        if (!generatedQuestions || generatedQuestions.length === 0) {
            alert('Tidak ada soal yang tersedia untuk disimpan.');
            return;
        }

        btnSaveBatch.disabled = true;
        btnSaveBatch.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

        fetch("{{ route('admin.examp.ai-save-batch') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ questions: generatedQuestions })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                alert(res.message);
                window.location.reload();
            } else {
                alert(res.message || 'Gagal menyimpan soal.');
                btnSaveBatch.disabled = false;
                btnSaveBatch.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Semua ke Bank Soal';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan: ' + err.message);
            btnSaveBatch.disabled = false;
            btnSaveBatch.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Semua ke Bank Soal';
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>
@endpush

