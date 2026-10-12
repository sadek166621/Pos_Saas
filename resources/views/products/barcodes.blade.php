@extends('layouts.app')

@section('title', 'বারকোড লেবেল প্রিন্ট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">বারকোড লেবেল তৈরি ও প্রিন্ট</h3>
    <p class="text-secondary small mb-0">পণ্য প্যাকেটের জন্য স্ট্যান্ডার্ড বারকোড স্টিকার প্রিন্ট করুন</p>
  </div>
  <button class="btn btn-dark" onclick="window.print()">
    <i data-lucide="printer" class="icon-sm"></i> প্রিন্ট স্টিকার
  </button>
</div>

<style>
  @media print {
    .sidebar, .navbar, .footer, .btn, .d-flex { display: none !important; }
    .page-content { padding: 0 !important; }
  }
  .barcode-sticker {
    width: 180px;
    height: 100px;
    border: 1px dashed #cbd5e1;
    padding: 6px;
    text-align: center;
    border-radius: 6px;
    background: white;
  }
  .barcode-lines {
    letter-spacing: 3px;
    font-family: monospace;
    font-size: 14px;
    font-weight: bold;
    background: repeating-linear-gradient(90deg, #000, #000 2px, transparent 2px, transparent 5px);
    height: 32px;
    margin: 4px auto;
    width: 85%;
  }
</style>

<div class="bg-white p-4 rounded-4 border shadow-sm">
  <div class="row g-3">
    @foreach($products as $p)
      <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="barcode-sticker mx-auto">
          <div class="fw-bold text-truncate" style="font-size: 11px;">{{ $p->name }}</div>
          <div class="barcode-lines"></div>
          <div class="font-monospace small" style="font-size: 11px;">{{ $p->barcode }}</div>
          <div class="fw-bold text-primary" style="font-size: 12px;">মূল্য: ৳{{ number_format($p->selling_price) }}</div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
