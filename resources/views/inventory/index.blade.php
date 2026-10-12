@extends('layouts.app')

@section('title', 'ইনভেন্টরি ও স্টক স্থিতি - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ইনভেন্টরি ও স্টক স্থিতি</h3>
    <p class="text-secondary small mb-0">প্রতিটি শাখার বর্তমান পণ্য স্টক ও রি-অর্ডার এলার্ট</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('inventory.transfers') }}" class="btn btn-outline-info">
      <i data-lucide="repeat" class="icon-sm"></i> ব্রাঞ্চ স্টক ট্রান্সফার
    </a>
    <a href="{{ route('inventory.adjustments') }}" class="btn btn-outline-warning">
      <i data-lucide="sliders" class="icon-sm"></i> স্টক অ্যাডজাস্টমেন্ট
    </a>
    <a href="{{ route('inventory.movements') }}" class="btn btn-outline-secondary">
      <i data-lucide="list" class="icon-sm"></i> স্টক মুভমেন্ট লগ
    </a>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <!-- Branch Filter -->
    <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <select name="branch_id" class="form-select" onchange="this.form.submit()">
          <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>সকল শাখা (All Branches)</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>
              {{ $b->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-8 text-md-end">
        @if($lowStockCount > 0)
          <span class="badge bg-danger p-2 fs-6">⚠️ লো-স্টক অ্যালার্ট: {{ $lowStockCount }} টি পণ্য রি-অর্ডারের নিচে আছে!</span>
        @else
          <span class="badge bg-success p-2 fs-6">✓ সকল পণ্যের পর্যাপ্ত স্টক রয়েছে</span>
        @endif
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>পণ্য</th>
            <th>বারকোড / SKU</th>
            <th>শাখা</th>
            <th>ওয়্যারহাউস</th>
            <th>বর্তমান স্টক</th>
            <th>অ্যালার্ট লিমিট</th>
            <th>স্ট্যাটাস</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stocks as $s)
            <tr>
              <td class="fw-bold">{{ $s->product->name ?? '—' }}</td>
              <td><span class="badge bg-light text-dark border font-monospace">{{ $s->product->barcode ?: $s->product->sku }}</span></td>
              <td><span class="badge bg-light text-primary border">{{ $s->branch->name ?? '—' }}</span></td>
              <td>{{ $s->warehouse->name ?? 'Default Hub' }}</td>
              <td class="fw-bold fs-6">
                {{ (float)$s->quantity }} {{ $s->product->unit->code ?? 'pcs' }}
              </td>
              <td>{{ (float)$s->alert_quantity }}</td>
              <td>
                @if($s->quantity <= $s->alert_quantity)
                  <span class="badge bg-danger">লো-স্টক (Low Stock)</span>
                @else
                  <span class="badge bg-success">পর্যাপ্ত স্টক</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো স্টক ডাটা পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $stocks->links() }}
    </div>
  </div>
</div>
@endsection
