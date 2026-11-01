@extends('admin.layouts.app')

@section('title', 'Tambah Soal CBT')
@section('page_title', 'Tambah Soal Baru')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Butir Soal CBT Baru
            </h4>
            <p class="text-muted small mb-0">
                Susun pertanyaan pilihan ganda beserta opsi jawaban, kunci jawaban, dan bobot poin soal.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.examp.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Bank Soal
            </a>
        </div>
    </div>

    <form action="{{ route('admin.examp.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-circle-question text-primary mr-2"></i>Pertanyaan & Opsi Pilihan Ganda
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Teks Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="question_text" id="summernote" class="form-control @error('question_text') is-invalid @enderror">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-dark">Pilihan A <span class="text-danger">*</span></label>
                                    <input type="text" name="option_a" class="form-control"
                                        value="{{ old('option_a') }}" placeholder="Teks jawaban opsi A" required>
                                </div>
                                <div class="form-group mb-md-0">
                                    <label class="font-weight-bold small text-dark">Pilihan B <span class="text-danger">*</span></label>
                                    <input type="text" name="option_b" class="form-control"
                                        value="{{ old('option_b') }}" placeholder="Teks jawaban opsi B" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-dark">Pilihan C <span class="text-danger">*</span></label>
                                    <input type="text" name="option_c" class="form-control"
                                        value="{{ old('option_c') }}" placeholder="Teks jawaban opsi C" required>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-dark">Pilihan D <span class="text-danger">*</span></label>
                                    <input type="text" name="option_d" class="form-control"
                                        value="{{ old('option_d') }}" placeholder="Teks jawaban opsi D" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-sliders text-primary mr-2"></i>Kunci & Bobot Soal
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="correct_answer" class="form-control" required>
                                <option value="">-- Pilih Kunci Jawaban --</option>
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    <option value="{{ $opt }}" {{ old('correct_answer') === $opt ? 'selected' : '' }}>Opsi {{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Bobot Poin Soal <span class="text-danger">*</span></label>
                            <input type="number" name="points" class="form-control" value="{{ old('points', 2) }}" min="1" required>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Soal CBT
                        </button>
                        <a href="{{ route('admin.examp.index') }}" class="btn btn-outline-secondary btn-block mt-2 font-weight-bold">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 200,
                placeholder: 'Tuliskan pertanyaan di sini...'
            });
        });
    </script>
@endpush
