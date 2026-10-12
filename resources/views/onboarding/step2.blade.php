@extends('layouts.guest')

@section('title', 'ধাপ ২: প্রধান ব্রাঞ্চ সেটআপ - অনবোর্ডিং')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-6">
    <div class="card auth-card bg-white border-0 p-4 p-md-5">
      <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
          <span class="badge bg-success text-white mb-1">ধাপ ২ / ৩</span>
          <h4 class="fw-bold mb-0">প্রথম আউটলেট / ব্রাঞ্চ তৈরি</h4>
        </div>
        <div class="fs-1">🏬</div>
      </div>

      <div class="alert alert-info py-2 small mb-4">
        প্রতিষ্ঠানের নাম: <strong>{{ $tenant->name }}</strong>। আপনার প্রধান শো-রুম বা আউটলেটের তথ্য দিন। পরবর্তীতে আপনি আরও ব্রাঞ্চ যোগ করতে পারবেন।
      </div>

      <form action="{{ route('onboarding.step2.post') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">ব্রাঞ্চের নাম</label>
            <input type="text" name="branch_name" class="form-control" required value="{{ old('branch_name', 'প্রধান শাখা (HQ)') }}" placeholder="উদাঃ ধানমন্ডি শাখা (HQ)">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">ব্রাঞ্চ কোড</label>
            <input type="text" name="branch_code" class="form-control" required value="{{ old('branch_code', 'HQ-01') }}" placeholder="HQ-01">
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">ব্রাঞ্চের ঠিকানা</label>
            <input type="text" name="branch_address" class="form-control" required value="{{ old('branch_address', $tenant->address) }}" placeholder="ঠিকানা লিখুন">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">শহর</label>
            <input type="text" name="city" class="form-control" required value="{{ old('city', $tenant->city) }}" placeholder="ঢাকা">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">ব্রাঞ্চের হেল্পলাইন ফোন নম্বর</label>
          <input type="text" name="branch_phone" class="form-control" value="{{ old('branch_phone', $tenant->phone) }}" placeholder="০১৭১১XXXXXX">
        </div>

        <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3 shadow-sm">
          পরবর্তী ধাপ: প্ল্যান নির্বাচন ও সমাপ্ত →
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
