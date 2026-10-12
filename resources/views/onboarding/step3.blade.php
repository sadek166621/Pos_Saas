@extends('layouts.guest')

@section('title', 'ধাপ ৩: সাবস্ক্রিপশন প্ল্যান - অনবোর্ডিং')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card auth-card bg-white border-0 p-4 p-md-5">
      <div class="text-center mb-4">
        <span class="badge bg-warning text-dark px-3 py-1 mb-2">চূড়ান্ত ধাপ ৩ / ৩</span>
        <h3 class="fw-bold mb-1">পছন্দের প্যাকেজ নির্বাচন করুন</h3>
        <p class="text-secondary small">সকল প্ল্যানে ১৪ দিনের আনলিমিটেড ফ্রি ট্রায়াল রয়েছে। কোনো পেমেন্ট ছাড়াই শুরু করতে পারবেন।</p>
      </div>

      <form action="{{ route('onboarding.step3.post') }}" method="POST">
        @csrf
        <div class="row g-4 mb-4">
          @foreach($plans as $plan)
            <div class="col-md-4">
              <label class="card h-100 border-2 rounded-4 p-3 text-center position-relative cursor-pointer shadow-sm {{ $plan->slug === 'business' ? 'border-primary bg-primary bg-opacity-10' : 'border-light-subtle' }}" style="cursor: pointer;">
                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="form-check-input position-absolute top-0 end-0 m-3" {{ $plan->slug === 'business' ? 'checked' : '' }} required>
                <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                <h3 class="fw-bold text-dark my-2">৳{{ number_format($plan->price_monthly) }}<span class="small fs-6 text-muted">/মাস</span></h3>
                <p class="text-secondary small mb-3">{{ $plan->description }}</p>
                <div class="small text-start border-top pt-2">
                  <div class="mb-1">🏢 <strong>{{ $plan->max_branches }}</strong> টি ব্রাঞ্চ</div>
                  <div class="mb-1">👥 <strong>{{ $plan->max_users }}</strong> জন ইউজার</div>
                  <div class="mb-1">📦 <strong>{{ number_format($plan->max_products) }}</strong> প্রোডাক্ট</div>
                  <div class="mb-1">📱 <strong>{{ $plan->sms_limit }}</strong> টি SMS/মাস</div>
                </div>
              </label>
            </div>
          @endforeach
        </div>

        <div class="row justify-content-center mb-4">
          <div class="col-md-6 text-center">
            <label class="form-label small fw-bold">বিলিং সাইকেল</label>
            <div class="d-flex justify-content-center gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="billing_cycle" id="monthly" value="monthly" checked>
                <label class="form-check-label" for="monthly">মাসিক বিলিং</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="billing_cycle" id="yearly" value="yearly">
                <label class="form-check-label text-success fw-bold" for="yearly">বাৎসরিক বিলিং (২০% ছাড়)</label>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-primary btn-lg px-5 py-2 fw-bold rounded-pill shadow">
            🎉 অনবোর্ডিং সমাপ্ত করুন ও ড্যাশবোর্ডে প্রবেশ করুন
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
