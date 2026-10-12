@extends('layouts.landing')

@section('title', 'মূল্য ও প্যাকেজসমূহ - SmartPOS SaaS')

@section('content')
<div class="py-5 bg-white border-bottom">
  <div class="container text-center py-4">
    <h1 class="fw-bold mb-3">স্বচ্ছ ও সাশ্রয়ী মূল্যের প্যাকেজ</h1>
    <p class="text-secondary lead mx-auto" style="max-width: 600px;">
      ছোট দোকান থেকে শুরু করে ৫০+ ব্রাঞ্চের রিটেইল চেইনের জন্য ডিজাইন করা পরিমাপযোগ্য SaaS প্ল্যান
    </p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4 justify-content-center">
    @foreach($plans as $plan)
      <div class="col-lg-4 col-md-6">
        <div class="card h-100 border-2 rounded-4 shadow-sm {{ $plan->slug === 'business' ? 'border-primary' : 'border-light-subtle' }}">
          @if($plan->slug === 'business')
            <div class="bg-primary text-white text-center py-1 fw-bold small rounded-top-4">
              ★ সর্বাধিক নির্বাচিত প্ল্যান
            </div>
          @endif
          <div class="card-body p-4 text-center">
            <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
            <p class="text-secondary small mb-4">{{ $plan->description }}</p>
            <div class="mb-4">
              <span class="display-5 fw-bold text-dark">৳{{ number_format($plan->price_monthly) }}</span>
              <span class="text-muted">/মাস</span>
              <div class="text-success small fw-semibold mt-1">বাৎসরিক বিলিংয়ে ২০% ছাড় (৳{{ number_format($plan->price_yearly) }}/বছর)</div>
            </div>

            <ul class="list-unstyled text-start small mb-4 border-top pt-3">
              <li class="py-2 border-bottom">🏢 সর্বোচ্চ ব্রাঞ্চ: <strong>{{ $plan->max_branches }} টি</strong></li>
              <li class="py-2 border-bottom">👥 ইউজার অ্যাকাউন্ট: <strong>{{ $plan->max_users }} জন</strong></li>
              <li class="py-2 border-bottom">📦 প্রোডাক্ট লিমিট: <strong>{{ number_format($plan->max_products) }} টি</strong></li>
              <li class="py-2 border-bottom">🏬 ওয়্যারহাউস: <strong>{{ $plan->max_warehouses }} টি</strong></li>
              <li class="py-2 border-bottom">📱 মাসিক SMS কোটা: <strong>{{ $plan->sms_limit }} টি</strong></li>
              <li class="py-2 border-bottom">⚡ মাল্টি-ব্রাঞ্চ প্রাইস ওভাররাইড</li>
              <li class="py-2 border-bottom">💰 কাস্টমার বাকি খাতা ও লেজার</li>
              <li class="py-2">🛡️ ব্যাকআপ ও প্রায়োরিটি সাপোর্ট</li>
            </ul>

            <a href="{{ route('register') }}" class="btn {{ $plan->slug === 'business' ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2 rounded-pill fw-bold">
              ১৪ দিনের ফ্রি ট্রায়াল নিন
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- ADD-ON TABLE -->
  <div class="mt-5 p-4 bg-white rounded-4 border shadow-sm">
    <h4 class="fw-bold mb-3 text-center">প্রয়োজন অনুযায়ী অ্যাড-অনস (Add-ons)</h4>
    <p class="text-secondary text-center small mb-4">আপনার ব্যবসার প্রয়োজন বাড়লে প্ল্যান পরিবর্তন না করেই অতিরিক্ত সার্ভিস যুক্ত করতে পারবেন</p>
    <div class="table-responsive">
      <table class="table table-hover align-middle text-center mb-0">
        <thead class="table-light">
          <tr>
            <th>অ্যাড-অন সার্ভিস</th>
            <th>মাসিক ফি</th>
            <th>বাৎসরিক ফি</th>
            <th>সুবিধা</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="fw-bold text-start">অতিরিক্ত শাখা (Extra Branch)</td>
            <td>৳৩০০ /মাস</td>
            <td>৳৩,০০০ /বছর</td>
            <td>আরেকটি নতুন আউটলেট বা শোরুম চালুর সুবিধা</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">অতিরিক্ত ইউজার (Extra User)</td>
            <td>৳১০০ /মাস</td>
            <td>৳১,০০০ /বছর</td>
            <td>অতিরিক্ত ম্যানেজার বা ক্যাশিয়ার অ্যাকাউন্ট</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">অতিরিক্ত ৫০০ SMS প্যাক</td>
            <td>৳২৫০ এককালীন</td>
            <td>—</td>
            <td>ইনভয়েস ও বাকি তাগাদার জন্য ভেরিফাইড ব্র্যান্ডেড SMS</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">অতিরিক্ত ৫ GB স্টোরেজ</td>
            <td>৳২০০ /মাস</td>
            <td>৳২,০০০ /বছর</td>
            <td>প্রোডাক্টের হাই-রেজোলিউশন ইমেজ ও রিসিট ব্যাকআপ</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
