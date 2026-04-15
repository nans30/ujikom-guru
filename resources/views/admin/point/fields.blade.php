@php
$point = $point ?? null;
@endphp

<div class="row">
    <div class="col-sm-12">
        <div class="mb-3">
            <label class="form-label">Nama Aturan / Kejadian <span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="name"
                value="{{ old('name', $point->name ?? '') }}"
                placeholder="Contoh: Datang Tepat Waktu atau Keterlambatan Pagi">
            <small class="text-muted">Tuliskan deskripsi singkat aturan poin ini.</small>
            @error('name')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Kriteria Waktu <span class="text-danger">*</span></label>
                    <select class="form-select" name="condition_operator">
                        <option value="" disabled selected>-- Pilih Kriteria --</option>
                        <option value="<" {{ old('condition_operator', $point->condition_operator ?? '') == '<' ? 'selected' : '' }}>
                            Sebelum / Lebih Awal Dari (<)
                                </option>
                        <option value=">" {{ old('condition_operator', $point->condition_operator ?? '') == '>' ? 'selected' : '' }}>
                            Melebihi / Sesudah (>)
                        </option>
                        <option value="BETWEEN" {{ old('condition_operator', $point->condition_operator ?? '') == 'BETWEEN' ? 'selected' : '' }}>
                            Di Antara Waktu (Range)
                        </option>
                    </select>
                    @error('condition_operator')
                    <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Patokan Waktu <span class="text-danger">*</span></label>
                    {{-- Diubah kembali ke type="text" agar mendukung format jam tunggal dan rentang jam untuk BETWEEN --}}
                    <input class="form-control" type="text" name="condition_value"
                        value="{{ old('condition_value', $point->condition_value ?? '') }}"
                        placeholder="Cth: 08:00 atau 07:45-08:00">
                    <small class="text-muted">Gunakan pemisah strip (-) jika memilih <b>Di Antara Waktu</b>. Contoh: <code>07:45-08:00</code></small>
                    @error('condition_value')
                    <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Reward / Penalty Poin <span class="text-danger">*</span></label>
                    <input class="form-control" type="number" name="point_modifier"
                        value="{{ old('point_modifier', $point->point_modifier ?? '') }}"
                        placeholder="Contoh: 10 atau -5">
                    <small class="text-muted">Gunakan tanda minus (-) untuk memotong poin.</small>
                    @error('point_modifier')
                    <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Status Aturan <span class="text-danger">*</span></label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $point->status ?? '') == 1 ? 'selected' : '' }}>
                    Aktif (Gunakan Aturan Ini)
                </option>
                <option value="0" {{ old('status', $point->status ?? '') == 0 ? 'selected' : '' }}>
                    Non-Aktifkan
                </option>
            </select>
            @error('status')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <hr>

        <div class="text-end">
            <a href="{{ route('admin.point.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i> Simpan Aturan Poin
            </button>
        </div>
    </div>
</div>