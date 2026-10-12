@extends('layouts.app')

@section('title', 'ক্রয় চালান বিস্তারিত - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ক্রয় চালান #{{ $purchase->purchase_no }}</h3>
    <p class="text-secondary small mb-0">সাপ্লায়ার: {{ $purchase->supplier->name }} | তারিখ: {{ $purchase->purchase_date->format('d M, Y') }}</p>
  </div>
  <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <small class="text-muted d-block">সর্বমোট বিল</small>
        <h4 class="fw-bold text-dark">৳{{ number_format($purchase->total, 2) }}</h4>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">পরিশোধিত</small>
        <h4 class="fw-bold text-success">৳{{ number_format($purchase->paid, 2) }}</h4>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">বকেয়া (Due)</small>
        <h4 class="fw-bold text-danger">৳{{ number_format($purchase->due, 2) }}</h4>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">গ্রহীতা শাখা</small>
        <h5 class="fw-bold">{{ $purchase->branch->name ?? '—' }}</h5>
      </div>
    </div>

    <h5 class="fw-bold mb-3 border-top pt-3">পণ্যসমূহের বিবরণ</h5>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>পণ্য</th>
            <th>পরিমাণ</th>
            <th>একক ক্রয়দর</th>
            <th>মোট টাকা</th>
          </tr>
        </thead>
        <tbody>
          @foreach($purchase->items as $item)
            <tr>
              <td class="fw-bold">{{ $item->product->name ?? '—' }}</td>
              <td>{{ (float)$item->quantity }}</td>
              <td>৳{{ number_format($item->unit_price, 2) }}</td>
              <td class="fw-bold">৳{{ number_format($item->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
