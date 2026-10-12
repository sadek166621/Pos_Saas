@extends('layouts.app')

@section('title', 'ব্রাঞ্চ এডিট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ব্রাঞ্চ তথ্য এডিট</h3>
    <p class="text-secondary small mb-0">{{ $branch->name }}</p>
  </div>
  <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <form action="{{ route('branches.update', $branch->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="form-label small fw-bold">ব্রাঞ্চের নাম *</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name', $branch->name) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">ব্রাঞ্চ কোড *</label>
          <input type="text" name="code" class="form-control" required value="{{ old('code', $branch->code) }}">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="form-label small fw-bold">ঠিকানা *</label>
          <input type="text" name="address" class="form-control" required value="{{ old('address', $branch->address) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">শহর *</label>
          <input type="text" name="city" class="form-control" required value="{{ old('city', $branch->city) }}">
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label small fw-bold">ফোন নম্বর</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">ব্রাঞ্চ ম্যানেজার</label>
          <select name="manager_id" class="form-select">
            <option value="">নির্বাচন করুন</option>
            @foreach($managers as $m)
              <option value="{{ $m->id }}" {{ $branch->manager_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold">স্ট্যাটাস</label>
          <select name="status" class="form-select">
            <option value="active" {{ $branch->status === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
            <option value="inactive" {{ $branch->status === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">আপডেট করুন ✓</button>
    </form>
  </div>
</div>
@endsection
