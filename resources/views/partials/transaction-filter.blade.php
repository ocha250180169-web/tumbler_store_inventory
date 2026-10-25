{{-- Form filter bersama untuk daftar penjualan & pembelian. Variabel: $action, $statuses, $placeholder --}}
<form method="GET" action="{{ $action }}" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="q" class="form-label small mb-1">Cari</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label small mb-1">Status</label>
            <select id="status" name="status" class="form-select form-select-sm">
                <option value="">Semua</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label small mb-1">Dari tanggal</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label small mb-1">Sampai tanggal</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm">Filter</button>
            <a href="{{ $action }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </div>
</form>
