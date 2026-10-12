@extends('layouts.guest')

@section('title', 'ফ্রি রেজিস্ট্রেশন - SmartPOS SaaS')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-5">
    <div class="card auth-card bg-white border-0 p-4 p-md-5">
      <div class="text-center mb-4">
        <a href="{{ route('home') }}" class="fs-2 fw-extrabold text-dark text-decoration-none d-inline-flex align-items-center gap-2">
          <span class="badge bg-primary text-white p-2 rounded-3">🇧🇩</span> Smart<span class="text-primary">POS</span>
        </a>
        <h4 class="fw-bold mt-3 mb-1">১৪ দিনের ফ্রি ট্রায়াল শুরু করুন</h4>
        <p class="text-secondary small">আপনার ব্যবসা ডিজিটাল করার প্রথম ধাপ</p>
      </div>

      @if($errors->any())
        <div class="alert alert-danger py-2 small mb-4">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label small fw-bold">আপনার পূর্ণ নাম</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="মোঃ রফিকুল ইসলাম">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold">মোবাইল নম্বর (বাংলাদেশ)</label>
          <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" placeholder="০১৭১১XXXXXX">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold">ইমেইল ঠিকানা</label>
          <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="name@business.com">
        </div>
        <div class="row g-2 mb-4">
          <div class="col-6">
            <label class="form-label small fw-bold">পাসওয়ার্ড</label>
            <input type="password" name="password" class="form-control" required placeholder="কমপক্ষে ৬ ডিজিট">
          </div>
          <div class="col-6">
            <label class="form-label small fw-bold">পুনরায় পাসওয়ার্ড</label>
            <input type="password" name="password_confirmation" class="form-control" required placeholder="একই পাসওয়ার্ড">
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm mb-3">
          একাউন্ট তৈরি করুন ও পরবর্তী ধাপে যান →
        </button>
      </form>

      <div class="text-center small text-secondary">
        ইতিমধ্যে একাউন্ট আছে? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">লগইন করুন</a>
      </div>
    </div>
  </div>
</div>
@endsection
