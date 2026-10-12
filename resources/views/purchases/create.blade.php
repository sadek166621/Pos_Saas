@extends('layouts.app')

@section('title', 'নতুন পণ্য ক্রয় (New Purchase) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-1 fw-bold">নতুন পণ্য ক্রয় চালান (Purchase Order)</h3>
    <p class="text-secondary small mb-0">সাপ্লায়ার থেকে পণ্য কিনে স্টকে যোগ করুন</p>
  </div>
  <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" class="icon-sm"></i> ফিরে যান
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4 p-md-5">
    <form action="{{ route('purchases.store') }}" method="POST">
      @csrf

      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <label class="form-label fw-bold">সাপ্লায়ার নির্বাচন করুন *</label>
          <select name="supplier_id" class="form-select" required>
            <option value="">নির্বাচন করুন</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->company_name }})</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-bold">গ্রহীতা শাখা (Branch) *</label>
          <select name="branch_id" class="form-select" required>
            @foreach($branches as $b)
              <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- Purchase Item -->
      <div class="border rounded-4 p-4 mb-4 bg-light">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="package-plus" class="icon-sm text-primary"></i> ক্রয়ের আইটেম বিবরণ
        </h5>
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">পণ্য নির্বাচন করুন *</label>
            <select name="items[0][product_id]" class="form-select" required>
              <option value="">নির্বাচন করুন</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (বর্তমান ক্রয়দর: ৳{{ $p->purchase_price }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">পরিমাণ *</label>
            <input type="number" name="items[0][quantity]" class="form-control" required value="20" min="1">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">একক ক্রয়দর (Unit Cost ৳) *</label>
            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control fw-bold" required value="1000">
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="border rounded-4 p-4 mb-4 bg-white">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="wallet" class="icon-sm text-success"></i> পরিশোধ বিবরণী (Payment Details)
        </h5>
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">নগদ প্রদান (Paid Amount ৳) *</label>
            <input type="number" step="0.01" name="paid_amount" class="form-control fw-bold text-success" required value="10000">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">পেমেন্ট মেথড *</label>
            <select name="payment_method" class="form-select" required>
              <option value="cash">ক্যাশ (Cash)</option>
              <option value="bank">ব্যাংক ট্রান্সফার (Bank Transfer)</option>
              <option value="bkash">বিকাশ (bKash)</option>
              <option value="nagad">নগদ (Nagad)</option>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-bold">মন্তব্য / চালান নোট</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="চালান বা বিল সংক্রান্ত কোনো বিশেষ নোট..."></textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
          <i data-lucide="check" class="icon-sm"></i> ক্রয় চালান সংরক্ষণ করুন
        </button>
        <a href="{{ route('purchases.index') }}" class="btn btn-light px-4 py-2">বাতিল</a>
      </div>
    </form>
  </div>
</div>
@endsection
