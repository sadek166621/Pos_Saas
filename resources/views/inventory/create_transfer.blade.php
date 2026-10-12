@extends('layouts.app')

@section('title', 'নতুন স্টক ট্রান্সফার - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-1 fw-bold">নতুন স্টক ট্রান্সফার চালান তৈরি</h3>
    <p class="text-secondary small mb-0">এক শাখা থেকে অন্য শাখায় পণ্য পাঠিয়ে স্টক ব্যালেন্স করুন</p>
  </div>
  <a href="{{ route('inventory.transfers') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" class="icon-sm"></i> ফিরে যান
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4 p-md-5">
    <form action="{{ route('inventory.store-transfer') }}" method="POST">
      @csrf

      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <label class="form-label fw-bold">প্রেরক শাখা (From Branch) *</label>
          <select name="from_branch_id" class="form-select" required>
            @foreach($branches as $b)
              <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">প্রাপক শাখা (To Branch) *</label>
          <select name="to_branch_id" class="form-select" required>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>
                {{ $b->name }} ({{ $b->code }})
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- Item selection -->
      <div class="border rounded-4 p-4 mb-4 bg-light">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="arrow-right-left" class="icon-sm text-primary"></i> স্থানান্তরিত পণ্যের বিবরণ
        </h5>
        <div class="row g-4">
          <div class="col-md-8">
            <label class="form-label fw-bold">পণ্য নির্বাচন করুন *</label>
            <select name="items[0][product_id]" class="form-select" required>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (ক্রয়মূল্য: ৳{{ $p->purchase_price }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">পরিমাণ (Quantity) *</label>
            <input type="number" name="items[0][quantity]" class="form-control fw-bold" required value="10" min="1">
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-bold">মন্তব্য / ট্রান্সফার কারণ</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="উদাঃ চট্টগ্রাম শাখায় পণ্যের বাড়তি চাহিদার কারণে পাঠানো হলো..."></textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
          <i data-lucide="check" class="icon-sm"></i> ট্রান্সফার সম্পন্ন করুন
        </button>
        <a href="{{ route('inventory.transfers') }}" class="btn btn-light px-4 py-2">বাতিল</a>
      </div>
    </form>
  </div>
</div>
@endsection
