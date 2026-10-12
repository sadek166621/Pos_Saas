@extends('layouts.app')

@section('title', 'বিক্রয় রিপোর্ট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">বিক্রয় ও রাজস্ব রিপোর্ট (Sales Report)</h3>
    <p class="text-secondary small mb-0">শাখাভিত্তিক এবং নির্দিষ্ট সময়কালের বিস্তারিত সেলস বিশ্লেষণ</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i data-lucide="printer" class="icon-sm"></i> প্রিন্ট রিপোর্ট
    </button>
  </div>
</div>

<!-- Filters -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <form action="{{ route('reports.sales') }}" method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-semibold">শুরুর তারিখ</label>
        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">শেষের তারিখ</label>
        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">শাখা নির্বাচন করুন</label>
        <select name="branch_id" class="form-select">
          <option value="all" {{ ($branchId === 'all' || !$branchId) ? 'selected' : '' }}>সকল শাখা (All Branches)</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->city }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i data-lucide="filter" class="icon-sm"></i> ফিল্টার করুন
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">সর্বমোট বিক্রয় (Total Sales)</span>
      <h3 class="fw-bold text-primary mt-1 mb-0">৳{{ number_format($totalSales, 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">নগদ ও ডিজিটাল আদায় (Total Collected)</span>
      <h3 class="fw-bold text-success mt-1 mb-0">৳{{ number_format($totalPaid, 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">নতুন বকেয়া (Outstanding Due)</span>
      <h3 class="fw-bold text-danger mt-1 mb-0">৳{{ number_format($totalDue, 2) }}</h3>
    </div>
  </div>
</div>

<!-- Sales Table -->
<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ইনভয়েস #</th>
            <th>তারিখ ও সময়</th>
            <th>আউটলেট / শাখা</th>
            <th>গ্রাহক (Customer)</th>
            <th>বিক্রেতা (Cashier)</th>
            <th>মোট (৳)</th>
            <th>আদায় (৳)</th>
            <th>বকেয়া (৳)</th>
            <th>পেমেন্ট</th>
            <th>অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $s)
            <tr>
              <td class="fw-bold text-primary font-monospace">{{ $s->invoice_number }}</td>
              <td class="small">{{ $s->sale_date ? $s->sale_date->format('d M, Y h:i A') : '' }}</td>
              <td><span class="badge bg-light text-dark border">{{ $s->branch->name ?? '—' }}</span></td>
              <td>{{ $s->customer->name ?? 'Walk-in Customer' }}</td>
              <td class="small">{{ $s->cashier->name ?? '—' }}</td>
              <td class="fw-bold">৳{{ number_format($s->total, 2) }}</td>
              <td class="text-success fw-bold">৳{{ number_format($s->paid, 2) }}</td>
              <td class="text-danger fw-bold">৳{{ number_format($s->due, 2) }}</td>
              <td><span class="badge bg-light text-primary border">{{ strtoupper($s->payment_method) }}</span></td>
              <td>
                <a href="{{ route('sales.show', $s->id) }}" class="btn btn-xs btn-outline-primary">রসিদ</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center py-4 text-muted">উক্ত সময়কালে কোনো বিক্রয় তথ্য পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $sales->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
