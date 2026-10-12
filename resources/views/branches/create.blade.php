@extends('layouts.app')

@section('title', 'নতুন ব্রাঞ্চ যোগ করুন - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">নতুন ব্রাঞ্চ বা আউটলেট তৈরি</h3>
    <p class="text-secondary small mb-0">নতুন শাখার কোড, ঠিকানা ও ম্যানেজার নির্ধারণ করুন</p>
  </div>
  <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <form action="{{ route('branches.store') }}" method="POST">
      @csrf

      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="form-label small fw-bold">ব্রাঞ্চের নাম *</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="উদাঃ চট্টগ্রাম জিইসি শাখা">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">ব্রাঞ্চ কোড *</label>
          <input type="text" name="code" class="form-control" required value="{{ old('code') }}" placeholder="উদাঃ CTG-01">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="form-label small fw-bold">ঠিকানা *</label>
          <input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="জিইসি মোড়, চট্টগ্রাম">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">শহর *</label>
          <input type="text" name="city" class="form-control" required value="{{ old('city', 'Chittagong') }}" placeholder="চট্টগ্রাম">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label small fw-bold">হেল্পলাইন ফোন নম্বর</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="০১৭১১XXXXXX">
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold">ব্রাঞ্চ ম্যানেজার নির্বাচন</label>
          <select name="manager_id" class="form-select">
            <option value="">ম্যানেজার নির্ধারণ করুন</option>
            @foreach($managers as $m)
              <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role }})</option>
            @endforeach
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">ব্রাঞ্চ সক্রিয় করুন ✓</button>
    </form>
  </div>
</div>
@endsection
