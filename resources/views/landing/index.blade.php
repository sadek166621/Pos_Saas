@extends('layouts.landing')

@section('title', 'SmartPOS SaaS - বাংলাদেশের ১ নম্বর বিজনেস অপারেটিং সিস্টেম')

@section('content')
<!-- HERO SECTION -->
<section class="hero-section text-center position-relative overflow-hidden">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <span class="badge bg-primary bg-opacity-25 text-white border border-primary px-3 py-2 rounded-pill mb-3 fw-semibold">
          🇧🇩 বাংলাদেশের রিটেইল ও এসএমই ব্যবসার জন্য প্রথম পূর্ণাঙ্গ ক্লাউড SaaS
        </span>
        <h1 class="display-4 fw-extrabold mb-4 text-white" style="line-height: 1.25;">
          একটি প্ল্যাটফর্ম থেকেই পরিচালনা করুন আপনার সম্পূর্ণ ব্যবসা
        </h1>
        <p class="lead text-light opacity-75 mb-5 mx-auto" style="max-width: 820px; font-size: 1.25rem;">
          অ্যাডভান্সড POS, ইনভেন্টরি, পারচেজ, কাস্টমার বকেয়া খাতা, মাল্টি-ব্রাঞ্চ কন্ট্রোল, ক্যাশ ড্রয়ার এবং রিয়েল-টাইম অ্যাকাউন্টিং—সবকিছু এখন আপনার আঙুলের ডগায়।
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="{{ route('register') }}" class="btn cta-btn-primary btn-lg shadow-lg px-4 py-3 fs-5">
            🚀 ১৪ দিনের ফ্রি ট্রায়াল শুরু করুন
          </a>
          <a href="{{ route('pricing') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fs-5">
            প্ল্যান ও মূল্য তালিকা দেখুন
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HIGHLIGHT STATS -->
<div class="container" style="margin-top: -30px;">
  <div class="row g-3">
    <div class="col-md-3 col-6">
      <div class="bg-white p-4 rounded-4 shadow-sm text-center border">
        <h3 class="fw-bold text-primary mb-1">০.১ সেকেন্ড</h3>
        <p class="text-secondary small mb-0">দ্রুততম বারকোড স্ক্যান ও বিলিং</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="bg-white p-4 rounded-4 shadow-sm text-center border">
        <h3 class="fw-bold text-success mb-1">১০০% ডেটা সুরক্ষা</h3>
        <p class="text-secondary small mb-0">আইসোলেটেড মাল্টি-টেন্যান্ট সিকিউরিটি</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="bg-white p-4 rounded-4 shadow-sm text-center border">
        <h3 class="fw-bold text-info mb-1">মাল্টি-ব্রাঞ্চ</h3>
        <p class="text-secondary small mb-0">ঢাকা, চট্টগ্রাম, সিলেট রিয়েল-টাইম স্টক</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="bg-white p-4 rounded-4 shadow-sm text-center border">
        <h3 class="fw-bold text-warning mb-1">বকেয়া খাতা</h3>
        <p class="text-secondary small mb-0">অটোমেটিক SMS রিমাইন্ডার ও লেজার</p>
      </div>
    </div>
  </div>
</div>

<!-- KEY FEATURES -->
<section class="py-5 mt-4">
  <div class="container">
    <div class="text-center mb-5">
      <h6 class="text-primary text-uppercase fw-bold">ফিচারসমূহ</h6>
      <h2 class="fw-bold">আপনার ব্যবসার প্রতিটি প্রয়োজনীয়তার পূর্ণাঙ্গ সমাধান</h2>
      <p class="text-secondary">ব্যবসাকে স্মার্ট, গতিশীল ও লাভজনক করার সব ডিজিটাল টুলস</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 d-inline-block mb-3 fs-3">⚡</div>
          <h4 class="fw-bold mb-3">হাই-স্পিড POS ও স্প্লিট পেমেন্ট</h4>
          <p class="text-secondary">
            কিবোর্ড শর্টকাট, ফাস্ট সার্চ এবং বারকোড স্ক্যানিং। একই অর্ডারে ক্যাশ, বিকাশ ও কার্ডে স্প্লিট পেমেন্ট গ্রহণ করুন মাত্র কয়েক ক্লিকে।
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 d-inline-block mb-3 fs-3">🏬</div>
          <h4 class="fw-bold mb-3">মাল্টি-ব্রাঞ্চ ও ব্রাঞ্চ-ভিত্তিক প্রাইসিং</h4>
          <p class="text-secondary">
            সকল ব্রাঞ্চের স্টক কেন্দ্রীয়ভাবে ট্র্যাক করুন। একই প্রোডাক্টের জন্য ঢাকা, চট্টগ্রাম বা সিলেটে আলাদা বিক্রয়মূল্য নির্ধারণ করার সুযোগ।
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 d-inline-block mb-3 fs-3">📒</div>
          <h4 class="fw-bold mb-3">কাস্টমার ও সাপ্লায়ার বকেয়া খাতা</h4>
          <p class="text-secondary">
            খাতার দিন শেষ! কাস্টমার ও সাপ্লায়ারের প্রতিটি লেনদেনের স্বয়ংক্রিয় লেজার, বাকি কালেকশন ও বাংলায় SMS রিমাইন্ডার পাঠানোর সুবিধা।
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-4 d-inline-block mb-3 fs-3">📦</div>
          <h4 class="fw-bold mb-3">স্মার্ট ইনভেন্টরি ও ট্রান্সফার</h4>
          <p class="text-secondary">
            লো-স্টক অ্যালার্ট, স্টক মুভমেন্ট লগ, ব্রাঞ্চ টু ব্রাঞ্চ স্টক স্থানান্তর এবং নষ্ট/ড্যামেজ পণ্যের অ্যাডজাস্টমেন্ট নির্ভুলভাবে হিসাব রাখুন।
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-info bg-opacity-10 text-info p-3 rounded-4 d-inline-block mb-3 fs-3">💰</div>
          <h4 class="fw-bold mb-3">ক্যাশ ড্রয়ার ও দৈনিক লাভ-ক্ষতি</h4>
          <p class="text-secondary">
            ক্যাশিয়ার শিফট শুরু ও শেষ করার ক্যাশ অডিট। দিনশেষে এক্সপেক্টেড ক্যাশ বনাম অ্যাকচুয়াল ক্যাশের হিসাব এবং রিয়েল-টাইম প্রফিট/লস রিপোর্ট।
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="bg-secondary bg-opacity-10 text-dark p-3 rounded-4 d-inline-block mb-3 fs-3">🖨️</div>
          <h4 class="fw-bold mb-3">বাংলা ও থার্মাল ইনভয়েস</h4>
          <p class="text-secondary">
            ৫৮মিমি ও ৮০মিমি থার্মাল রিসিট প্রিন্টারের সাথে শতভাগ সামঞ্জস্যপূর্ণ। এছাড়া সাধারণ প্রিন্টারের জন্য রয়েছে পেশাদার A4 সাইজ ইনভয়েস।
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRICING SECTION -->
<section class="py-5 bg-white border-top border-bottom">
  <div class="container">
    <div class="text-center mb-5">
      <h6 class="text-primary text-uppercase fw-bold">সাবস্ক্রিপশন প্ল্যান</h6>
      <h2 class="fw-bold">সুলভ মূল্যে আপনার ব্যবসার উপযোগী প্যাকেজ</h2>
      <p class="text-secondary">কোনো লুকানো চার্জ নেই। প্রতি প্ল্যানে রয়েছে ১৪ দিনের ফ্রি ট্রায়াল।</p>
    </div>

    <div class="row g-4 justify-content-center">
      @foreach($plans as $plan)
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-2 rounded-4 shadow-sm {{ $plan->slug === 'business' ? 'border-primary' : 'border-light-subtle' }}">
            @if($plan->slug === 'business')
              <div class="bg-primary text-white text-center py-1 fw-bold small rounded-top-4">
                ★ সবচেয়ে জনপ্রিয় প্যাকেজ
              </div>
            @endif
            <div class="card-body p-4 text-center">
              <h4 class="fw-bold mb-2">{{ $plan->name }}</h4>
              <p class="text-secondary small mb-4">{{ $plan->description }}</p>
              <div class="mb-4">
                <span class="display-5 fw-bold text-dark">৳{{ number_format($plan->price_monthly) }}</span>
                <span class="text-muted">/মাসিক</span>
                <div class="text-success small fw-semibold mt-1">বাৎসরিক: ৳{{ number_format($plan->price_yearly) }}</div>
              </div>

              <ul class="list-unstyled text-start small mb-4 border-top pt-3">
                <li class="py-2 border-bottom">🏢 সর্বোচ্চ ব্রাঞ্চ: <strong>{{ $plan->max_branches }} টি</strong></li>
                <li class="py-2 border-bottom">👥 ইউজার অ্যাকাউন্ট: <strong>{{ $plan->max_users }} জন</strong></li>
                <li class="py-2 border-bottom">📦 প্রোডাক্ট লিমিট: <strong>{{ number_format($plan->max_products) }} টি</strong></li>
                <li class="py-2 border-bottom">🏬 ওয়্যারহাউস: <strong>{{ $plan->max_warehouses }} টি</strong></li>
                <li class="py-2 border-bottom">📱 প্রতি মাসে SMS: <strong>{{ $plan->sms_limit }} টি</strong></li>
                <li class="py-2 border-bottom">⚡ থার্মাল ও A4 রিসিট প্রিন্ট</li>
                <li class="py-2">🛡️ ডেইলি ব্যাকআপ ও ২৪/৭ সাপোর্ট</li>
              </ul>

              <a href="{{ route('register') }}" class="btn {{ $plan->slug === 'business' ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2 rounded-pill fw-bold">
                ট্রায়াল শুরু করুন
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="py-5 text-center bg-dark text-white">
  <div class="container py-4">
    <h2 class="display-6 fw-bold mb-3">ব্যবসা পরিচালনায় আনুন যুগান্তকারী পরিবর্তন</h2>
    <p class="lead text-secondary mb-4 mx-auto" style="max-width: 600px;">
      আজই যুক্ত হোন স্মার্ট ব্যবসায়ীদের তালিকায়। কোনো ক্রেডিট কার্ডের প্রয়োজন নেই।
    </p>
    <a href="{{ route('register') }}" class="btn cta-btn-primary btn-lg px-5 py-3 shadow">
      ফ্রি একাউন্ট তৈরি করুন
    </a>
  </div>
</section>
@endsection
