@extends('layouts.guest')

@section('title', 'ধাপ ১: ব্যবসার তথ্য - অনবোর্ডিং')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-6">
    <div class="card auth-card bg-white border-0 p-4 p-md-5">
      <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
          <span class="badge bg-primary text-white mb-1">ধাপ ১ / ৩</span>
          <h4 class="fw-bold mb-0">আপনার ব্যবসার তথ্য</h4>
        </div>
        <div class="fs-1">🏢</div>
      </div>

      @if($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('onboarding.step1.post') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label small fw-bold">প্রতিষ্ঠানের নাম (Business Name)</label>
          <input type="text" name="business_name" class="form-control" required value="{{ old('business_name') }}" placeholder="উদাঃ রহমান ফ্যাশন ও রিটেইল">
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold">ব্যবসার ধরণ</label>
            <select name="business_type" class="form-select" required>
              <option value="fashion">ফ্যাশন ও গার্মেন্টস (Fashion)</option>
              <option value="grocery">গ্রোসারি ও সুপারশপ (Grocery)</option>
              <option value="electronics">ইলেকট্রনিক্স ও গ্যাজেট (Electronics)</option>
              <option value="pharmacy">ফার্মেসি (Pharmacy)</option>
              <option value="wholesale">পাইকারি ব্যবসা (Wholesale)</option>
              <option value="restaurant">রেস্তোরাঁ / ক্যাফে (Restaurant)</option>
              <option value="other">অন্যান্য ব্যবসা (General Retail)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">অফিসিয়াল ফোন নম্বর</label>
            <input type="text" name="phone" class="form-control" required value="{{ old('phone', auth()->user()->phone) }}" placeholder="০১৭১১XXXXXX">
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">ঠিকানা</label>
            <input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="বাড়ি #১২, রোড #০৫, ধানমন্ডি">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">শহর (City)</label>
            <input type="text" name="city" class="form-control" required value="{{ old('city', 'Dhaka') }}" placeholder="ঢাকা">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">ভ্যাট / BIN নম্বর (ঐচ্ছিক)</label>
          <input type="text" name="bin_number" class="form-control" value="{{ old('bin_number') }}" placeholder="১২৩৪৫৬৭৮৯-০১০১">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
          পরবর্তী ধাপ: প্রথম ব্রাঞ্চ সেটআপ →
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
