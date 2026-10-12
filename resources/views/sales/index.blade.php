@extends('layouts.app')

@section('title', 'বিক্রয় তালিকা (Sales History) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">বিক্রয় তালিকা ও ইনভয়েস হিস্ট্রি</h3>
    <p class="text-secondary small mb-0">সকল ইনভয়েস, ক্যাশ সংগ্রহ ও পেমেন্ট হিস্ট্রি</p>
  </div>
  <a href="{{ route('pos.index') }}" class="btn btn-success pos-quick-btn">
    <i data-lucide="shopping-cart" class="icon-sm"></i> POS কাউন্টারে যান
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <!-- Filter bar -->
    <form method="GET" action="{{ route('sales.index') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="invoice_no" class="form-control" value="{{ request('invoice_no') }}" placeholder="ইনভয়েস নম্বর (উদাঃ INV-...)">
      </div>
      <div class="col-md-3">
        <select name="branch_id" class="form-select">
          <option value="all">সকল শাখা (All Branches)</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
              {{ $b->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="payment_status" class="form-select">
          <option value="">সকল পেমেন্ট স্ট্যাটাস</option>
          <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>পরিশোধিত (Paid)</option>
          <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>আংশিক বাকি (Partial)</option>
          <option value="due" {{ request('payment_status') === 'due' ? 'selected' : '' }}>বকেয়া (Due)</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">সার্চ</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ইনভয়েস #</th>
            <th>শাখা</th>
            <th>কাস্টমার</th>
            <th>তারিখ ও সময়</th>
            <th>সর্বমোট বিল</th>
            <th>পরিশোধিত</th>
            <th>বকেয়া</th>
            <th>পেমেন্ট মেথড</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $s)
            <tr>
              <td>
                <a href="{{ route('sales.show', $s->id) }}" class="fw-bold text-primary">
                  {{ $s->invoice_no }}
                </a>
              </td>
              <td><span class="badge bg-light text-dark border">{{ $s->branch->name ?? 'HQ' }}</span></td>
              <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
              <td class="small text-muted">{{ $s->sale_date->format('d M Y, h:i A') }}</td>
              <td class="fw-bold fs-6">৳{{ number_format($s->total, 2) }}</td>
              <td class="text-success fw-bold">৳{{ number_format($s->paid, 2) }}</td>
              <td>
                @if($s->due > 0)
                  <span class="text-danger fw-bold">৳{{ number_format($s->due, 2) }}</span>
                @else
                  <span class="text-muted">৳০.০০</span>
                @endif
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ $s->payment_method }}</span>
              </td>
              <td class="text-end">
                <a href="{{ route('sales.show', $s->id) }}" class="btn btn-sm btn-outline-primary" title="বিস্তারিত">
                  <i data-lucide="eye" class="icon-sm"></i>
                </a>
                <a href="{{ route('sales.invoice', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="প্রিন্ট">
                  <i data-lucide="printer" class="icon-sm"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">কোনো বিক্রয় রেকর্ড পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $sales->links() }}
    </div>
  </div>
</div>
@endsection
