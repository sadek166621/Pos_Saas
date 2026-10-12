@extends('layouts.app')

@section('title', 'ইনভেন্টরি ও স্টক মূল্যায়ন রিপোর্ট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ইনভেন্টরি স্টক মূল্যায়ন (Inventory Valuation Report)</h3>
    <p class="text-secondary small mb-0">সকল শাখা ও গুদামের বর্তমান মজুদ পণ্যের ক্রয় ও বিক্রয় মূল্য বিশ্লেষণ</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i data-lucide="printer" class="icon-sm"></i> প্রিন্ট রিপোর্ট
    </button>
  </div>
</div>

<!-- Valuation Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">মোট মজুদ ক্রয়মূল্য (Cost Valuation)</span>
      <h3 class="fw-bold text-dark mt-1 mb-0">৳{{ number_format($totalValuationCost, 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">মোট মজুদ বিক্রয়মূল্য (Retail Valuation)</span>
      <h3 class="fw-bold text-primary mt-1 mb-0">৳{{ number_format($totalValuationRetail, 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">সম্ভাব্য মোট গ্রস লাভ (Potential Profit)</span>
      <h3 class="fw-bold text-success mt-1 mb-0">৳{{ number_format($potentialProfit, 2) }}</h3>
    </div>
  </div>
</div>

<!-- Stock Table -->
<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>পণ্য (Product)</th>
            <th>বারকোড / SKU</th>
            <th>শাখা / লোকেশন</th>
            <th>বর্তমান মজুদ</th>
            <th>একক ক্রয়মূল্য (৳)</th>
            <th>একক বিক্রয়মূল্য (৳)</th>
            <th>মোট ক্রয়মূল্য (৳)</th>
            <th>মোট বিক্রয়মূল্য (৳)</th>
            <th>স্ট্যাটাস</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stocks as $st)
            @php
              $p = $st->product;
              $totalCost = ($st->quantity ?? 0) * ($p->purchase_price ?? 0);
              $totalRetail = ($st->quantity ?? 0) * ($p->selling_price ?? 0);
            @endphp
            <tr>
              <td class="fw-bold">{{ $p->name ?? 'Unknown' }}</td>
              <td class="font-monospace small">{{ $p->barcode ?? $p->sku }}</td>
              <td><span class="badge bg-light text-primary border">{{ $st->branch->name ?? '—' }}</span></td>
              <td class="fw-bold fs-6 {{ $st->quantity <= ($p->alert_quantity ?? 5) ? 'text-danger' : 'text-dark' }}">
                {{ $st->quantity }} {{ $p->unit->code ?? 'pcs' }}
              </td>
              <td>৳{{ number_format($p->purchase_price ?? 0, 2) }}</td>
              <td>৳{{ number_format($p->selling_price ?? 0, 2) }}</td>
              <td class="fw-bold">৳{{ number_format($totalCost, 2) }}</td>
              <td class="fw-bold text-primary">৳{{ number_format($totalRetail, 2) }}</td>
              <td>
                @if($st->quantity <= 0)
                  <span class="badge bg-danger">স্টক আউট</span>
                @elseif($st->quantity <= ($p->alert_quantity ?? 5))
                  <span class="badge bg-warning text-dark">লো স্টক</span>
                @else
                  <span class="badge bg-success">পর্যাপ্ত</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">কোনো স্টক তথ্য পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
