@php
$item = $item ?? null;
@endphp

<div class="col-sm-12">
    <div class="mb-3">
        <label class="form-label">Nama Item <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="item_name"
            value="{{ old('item_name', $item->item_name ?? '') }}"
            placeholder="Contoh: Voucher Telat 15 Menit">
        @error('item_name')
        <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Harga Poin <span class="text-danger">*</span></label>
                <input class="form-control" type="number" name="point_cost"
                    value="{{ old('point_cost', $item->point_cost ?? '') }}"
                    placeholder="Contoh: 50">
                @error('point_cost')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Menit Dispensasi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input class="form-control" type="number" name="extra_minutes"
                        value="{{ old('extra_minutes', $item->extra_minutes ?? 0) }}"
                        placeholder="Contoh: 15">
                    <span class="input-group-text">Menit</span>
                </div>
                <small class="text-muted">Toleransi telat yang diberikan.</small>
                @error('extra_minutes')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Batas Tukar</label>
                <input class="form-control" type="number" name="stock_limit"
                    value="{{ old('stock_limit', $item->stock_limit ?? '') }}"
                    placeholder="Kosongkan jika bebas">
                @error('stock_limit')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select" name="status">
            <option value="1" {{ old('status', $item->status ?? '') == 1 ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('status', $item->status ?? '') == 0 ? 'selected' : '' }}>Non-Aktif</option>
        </select>
    </div>

    <hr>
    <div class="text-end">
        <a href="{{ route('admin.item.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <button type="submit" class="btn btn-primary">Simpan Item</button>
    </div>
</div>