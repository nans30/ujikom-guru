@php
$assessment = $assessment ?? null;
// Ambil data scores lama jika sedang dalam mode EDIT untuk ditampilkan kembali
$oldScores = $assessment ? $assessment->details->pluck('score', 'category_id')->toArray() : [];

// Logika Auto-fill dari URL (Filter Monitoring)
$selectedSemester = old('semester', $assessment->semester ?? request('semester'));
$selectedYear = old('academic_year', $assessment->academic_year ?? request('academic_year'));
@endphp

<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Informasi Penilaian</h5>

                {{-- Field Guru --}}
                <div class="mb-3">
                    <label class="form-label">Pilih Guru / Staf <span class="text-danger">*</span></label>
                    <select class="form-select @error('evaluatee_id') is-invalid @enderror" name="evaluatee_id">
                        <option value="" selected disabled>-- Pilih Personel --</option>
                        @foreach($evaluatees as $teacher)
                        <option value="{{ $teacher->id }}"
                            {{ old('evaluatee_id', $assessment->evaluatee_id ?? request('teacher_id')) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} {{ $teacher->nip ? '(NIP: ' . $teacher->nip . ')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('evaluatee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Tanggal Penilaian --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                        <input type="date" name="assessment_date" class="form-control @error('assessment_date') is-invalid @enderror"
                            value="{{ old('assessment_date', isset($assessment->assessment_date) ? $assessment->assessment_date->format('Y-m-d') : date('Y-m-d')) }}">
                        @error('assessment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Periode: Semester --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                            {{-- Opsi All/Pilih dihapus agar konsisten dengan filter monitoring --}}
                            <option value="1" {{ $selectedSemester == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                            <option value="2" {{ $selectedSemester == '2' ? 'selected' : '' }}>2 (Genap)</option>
                        </select>
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Periode: Tahun Ajaran --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror"
                            placeholder="Contoh: 2025/2026"
                            value="{{ $selectedYear }}">
                        @error('academic_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan (Opsional)</label>
                    <textarea name="general_notes" class="form-control" rows="4" placeholder="Berikan feedback atau masukan...">{{ old('general_notes', $assessment->general_notes ?? '') }}</textarea>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label">Status Penilaian <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                        <option value="1" {{ old('status', $assessment->status ?? '') == 1 ? 'selected' : '' }}>Simpan sebagai Draft</option>
                        <option value="2" {{ old('status', $assessment->status ?? '') == 2 ? 'selected' : '' }}>Final (Selesai)</option>
                    </select>
                    <small class="text-muted">Status <strong>Final</strong> biasanya tidak dapat diedit kembali.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Indikator Penilaian --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Indikator Penilaian (Star Rating)</h5>
                <p class="text-muted mb-4">Berikan penilaian skor 1 - 5 untuk setiap kriteria di bawah ini.</p>

                @foreach($categories as $category)
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="fw-bold mb-0 text-dark">{{ $category->name }}</label>
                        <span class="badge bg-light text-primary border badge-score-{{ $category->id }}">
                            Skor: {{ old('scores.'.$category->id, $oldScores[$category->id] ?? 0) }}
                        </span>
                    </div>
                    <p class="small text-muted mb-2">{{ $category->description ?? 'Tidak ada deskripsi kriteria.' }}</p>

                    <div class="star-rating d-flex flex-row-reverse justify-content-end">
                        @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star-{{ $category->id }}-{{ $i }}"
                            name="scores[{{ $category->id }}]" value="{{ $i }}"
                            {{ old('scores.'.$category->id, $oldScores[$category->id] ?? '') == $i ? 'checked' : '' }}
                            class="btn-check">
                        <label for="star-{{ $category->id }}-{{ $i }}" class="ti ti-star fs-3 me-1 cursor-pointer rating-star"
                            style="color: #ddd;" onclick="updateScoreLabel({{ $category->id }}, {{ $i }})"></label>
                        @endfor
                    </div>
                    @error('scores.'.$category->id)
                    <span class="text-danger d-block mt-1 small">Wajib mengisi nilai bintang.</span>
                    @enderror
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Tombol Aksi --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="text-end">
            <hr>
            <a href="{{ route('admin.assessment.index') }}" class="btn btn-outline-danger me-2">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="ti ti-device-floppy me-1"></i> Simpan Penilaian
            </button>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .btn-check:checked+.rating-star,
    .btn-check:checked+.rating-star~.rating-star {
        color: #ffc107 !important;
    }

    .rating-star:hover,
    .rating-star:hover~.rating-star {
        color: #ffc107 !important;
    }

    .rating-star {
        transition: color 0.15s ease-in-out;
    }
</style>

<script>
    function updateScoreLabel(id, value) {
        const label = document.querySelector('.badge-score-' + id);
        if (label) {
            label.innerText = 'Skor: ' + value;
        }
    }
</script>