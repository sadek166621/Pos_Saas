@extends('layouts.app')

@section('title', 'নতুন প্রোডাক্ট যোগ করুন - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-1 fw-bold">নতুন প্রোডাক্ট যোগ করুন</h3>
    <p class="text-secondary small mb-0">পণ্যটির নাম, প্রাইস, বারকোড এবং ব্রাঞ্চ স্টক দিন</p>
  </div>
  <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" class="icon-sm"></i> ফিরে যান
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4 p-md-5">
    <form action="{{ route('products.store') }}" method="POST">
      @csrf

      <!-- Basic Product Information -->
      <div class="mb-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="package" class="icon-sm text-primary"></i> সাধারণ তথ্য (Basic Info)
        </h5>
        
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">পণ্যের নাম *</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="উদাঃ প্রিমিয়াম কটন পাঞ্জাবি">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">বারকোড (Barcode)</label>
            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="খালি রাখলে অটো তৈরি হবে">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">SKU কোড</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="SKU-XXXXXX">
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label fw-bold">ক্যাটাগরি</label>
            <select name="category_id" class="form-select">
              <option value="">নির্বাচন করুন</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">ব্র্যান্ড</label>
            <select name="brand_id" class="form-select">
              <option value="">নির্বাচন করুন</option>
              @foreach($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">একক / ইউনিট</label>
            <select name="unit_id" class="form-select">
              @foreach($units as $u)
                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Pricing Card Section -->
      <div class="border rounded-4 p-4 mb-4 bg-light">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="tag" class="icon-sm text-primary"></i> পণ্যের মূল্য নির্ধারণ (Pricing)
        </h5>
        
        <div class="row g-4">
          <div class="col-md-3">
            <label class="form-label fw-bold">ক্রয়মূল্য (Purchase Cost ৳) *</label>
            <input type="number" step="0.01" name="purchase_price" class="form-control fw-bold" required value="{{ old('purchase_price', 0) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">গ্লোবাল বিক্রয়মূল্য (Retail ৳) *</label>
            <input type="number" step="0.01" name="selling_price" class="form-control fw-bold text-primary" required value="{{ old('selling_price', 0) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">পাইকারি মূল্য (Wholesale ৳)</label>
            <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">ভ্যাট পার্সেন্টেজ (%)</label>
            <input type="number" step="0.01" name="vat_percent" class="form-control" value="0">
          </div>
        </div>
      </div>

      <!-- Branch-specific opening stock & price override -->
      <div class="border rounded-4 p-4 mb-4 bg-white">
        <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
          <i data-lucide="store" class="icon-sm text-success"></i> শাখা অনুযায়ী প্রারম্ভিক স্টক ও স্পেশাল প্রাইস
        </h5>
        <p class="text-secondary small mb-4">প্রতিটি শাখার জন্য আলাদা স্টক এবং প্রয়োজন হলে আলাদা বিক্রয়মূল্য দিতে পারেন (যেমন: ঢাকা ১০৫০৳, সিলেট ১১০০৳)</p>
        
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="min-width: 220px;">শাখা (Branch)</th>
                <th style="min-width: 200px;">প্রারম্ভিক স্টক (Opening Stock)</th>
                <th style="min-width: 250px;">শাখা স্পেশাল প্রাইস (Price Override ৳)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($branches as $b)
                <tr>
                  <td class="fw-bold">{{ $b->name }} ({{ $b->code }})</td>
                  <td>
                    <input type="number" name="opening_stocks[{{ $b->id }}]" class="form-control" value="0" min="0" style="max-width: 180px;">
                  </td>
                  <td>
                    <input type="number" step="0.01" name="branch_prices[{{ $b->id }}]" class="form-control" placeholder="যেমন: গ্লোবাল থেকে ভিন্ন হলে লিখুন" style="max-width: 240px;">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
          <i data-lucide="check" class="icon-sm"></i> সংরক্ষণ করুন
        </button>
        <a href="{{ route('products.index') }}" class="btn btn-light px-4 py-2">বাতিল</a>
      </div>
    </form>
  </div>
</div>
@endsection
