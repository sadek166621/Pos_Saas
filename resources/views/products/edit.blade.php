@extends('layouts.app')

@section('title', 'প্রোডাক্ট এডিট করুন - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-1 fw-bold">প্রোডাক্ট এডিট করুন</h3>
    <p class="text-secondary small mb-0">{{ $product->name }} (SKU: {{ $product->sku }})</p>
  </div>
  <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" class="icon-sm"></i> ফিরে যান
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4 p-md-5">
    <form action="{{ route('products.update', $product->id) }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Basic Info -->
      <div class="mb-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="package" class="icon-sm text-primary"></i> সাধারণ তথ্য (Basic Info)
        </h5>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">পণ্যের নাম *</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">বারকোড (Barcode)</label>
            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">SKU কোড</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-4">
            <label class="form-label fw-bold">ক্যাটাগরি</label>
            <select name="category_id" class="form-select">
              <option value="">নির্বাচন করুন</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">ব্র্যান্ড</label>
            <select name="brand_id" class="form-select">
              <option value="">নির্বাচন করুন</option>
              @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                  {{ $brand->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">একক / ইউনিট</label>
            <select name="unit_id" class="form-select">
              @foreach($units as $u)
                <option value="{{ $u->id }}" {{ $product->unit_id == $u->id ? 'selected' : '' }}>
                  {{ $u->name }} ({{ $u->code }})
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Pricing Section -->
      <div class="border rounded-4 p-4 mb-4 bg-light">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="tag" class="icon-sm text-primary"></i> পণ্যের মূল্য নির্ধারণ (Pricing)
        </h5>

        <div class="row g-4">
          <div class="col-md-3">
            <label class="form-label fw-bold">ক্রয়মূল্য (৳) *</label>
            <input type="number" step="0.01" name="purchase_price" class="form-control fw-bold" required value="{{ old('purchase_price', $product->purchase_price) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">গ্লোবাল বিক্রয়মূল্য (৳) *</label>
            <input type="number" step="0.01" name="selling_price" class="form-control text-primary fw-bold" required value="{{ old('selling_price', $product->selling_price) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">পাইকারি মূল্য (৳)</label>
            <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">ভ্যাট (%)</label>
            <input type="number" step="0.01" name="vat_percent" class="form-control" value="{{ old('vat_percent', $product->vat_percent) }}">
          </div>
        </div>
      </div>

      <!-- Branch Price Overrides -->
      <div class="border rounded-4 p-4 mb-4 bg-white">
        <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
          <i data-lucide="store" class="icon-sm text-success"></i> শাখা অনুযায়ী স্পেশাল বিক্রয়মূল্য (Price Override)
        </h5>
        <p class="text-secondary small mb-4">যেকোনো নির্দিষ্ট শাখার জন্য ভিন্ন বিক্রয়মূল্য নির্ধারণ করুন</p>

        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="min-width: 250px;">শাখা (Branch)</th>
                <th style="min-width: 280px;">শাখা স্পেশাল প্রাইস (৳)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($branches as $b)
                @php 
                  $override = $product->branchPrices->firstWhere('branch_id', $b->id); 
                @endphp
                <tr>
                  <td class="fw-bold">{{ $b->name }} ({{ $b->code }})</td>
                  <td>
                    <input type="number" step="0.01" name="branch_prices[{{ $b->id }}]" class="form-control" value="{{ $override?->selling_price }}" placeholder="গ্লোবাল প্রাইস (৳{{ $product->selling_price }}) প্রযোজ্য" style="max-width: 260px;">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
          <i data-lucide="check" class="icon-sm"></i> আপডেট করুন
        </button>
        <a href="{{ route('products.index') }}" class="btn btn-light px-4 py-2">বাতিল</a>
      </div>
    </form>
  </div>
</div>
@endsection
