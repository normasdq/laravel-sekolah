@extends('admin.layouts.app')

@section('title', 'Tambah Program Unggulan')
@section('page_title', 'Tambah Program Unggulan')

@section('content')
    <!-- Top Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3" style="gap: 12px;">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-award text-primary mr-2"></i>Tambah Program Unggulan Baru
            </h4>
            <p class="text-muted small mb-0">
                Tambahkan program unggulan akademik, pembinaan karakter, atau ekstrakurikuler sekolah.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Program
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.programs.store') }}">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Detail Program -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-file-lines text-primary mr-2"></i>Detail Program Unggulan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Judul Program <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Contoh: Tahfidz Al-Qur'an Terpadu / Kelas Sains & Robotika">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Deskripsi Program</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5"
                                placeholder="Jelaskan keunggulan dan kegiatan utama dari program ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small text-dark">Kelas Ikon (Font Awesome)</label>
                            <input type="text" name="icon" id="program-icon-input" value="{{ old('icon', 'fas fa-star') }}"
                                class="form-control @error('icon') is-invalid @enderror" placeholder="fas fa-star">
                            <small class="text-muted d-block mt-1">Klik salah satu ikon populer di bawah untuk memilih cepat:</small>
                            <div class="d-flex flex-wrap mt-2" style="gap: 6px;">
                                @foreach (['fas fa-star', 'fas fa-book-open', 'fas fa-Quran', 'fas fa-flask', 'fas fa-laptop-code', 'fas fa-trophy', 'fas fa-microscope', 'fas fa-language', 'fas fa-mosque', 'fas fa-futbol'] as $presetIcon)
                                    <button type="button" class="btn btn-outline-secondary btn-xs py-1 px-2 preset-icon-btn" data-icon="{{ $presetIcon }}">
                                        <i class="{{ $presetIcon }} mr-1"></i> <code>{{ $presetIcon }}</code>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pratinjau Ikon & Pengaturan -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-sliders text-primary mr-2"></i>Pratinjau & Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center p-3 mb-3 rounded border bg-light">
                            <small class="text-muted d-block mb-2 font-weight-bold">Pratinjau Ikon Program</small>
                            <div class="d-inline-flex align-items-center justify-content-center shadow-sm"
                                style="width: 56px; height: 56px; border-radius: 14px; background: #fef3c7; color: #d97706; font-size: 1.5rem;">
                                <i id="program-icon-preview" class="{{ old('icon', 'fas fa-star') }}"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small text-dark">Urutan Tampil</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                                class="form-control @error('order') is-invalid @enderror">
                            <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal.</small>
                        </div>

                        <div class="form-group mb-0 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" value="1" id="is_active"
                                    class="custom-control-input" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="is_active">
                                    Tampilkan Program (Aktif)
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Program aktif akan ditampilkan di halaman beranda website.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Program
                        </button>
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary btn-block mt-2 font-weight-bold">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('program-icon-input');
    const iconPreview = document.getElementById('program-icon-preview');

    iconInput?.addEventListener('input', function() {
        if (iconPreview) {
            iconPreview.className = this.value.trim() || 'fas fa-star';
        }
    });

    document.querySelectorAll('.preset-icon-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.getAttribute('data-icon');
            if (iconInput && iconPreview) {
                iconInput.value = val;
                iconPreview.className = val;
            }
        });
    });
});
</script>
@endpush