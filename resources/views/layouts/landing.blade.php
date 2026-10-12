<!DOCTYPE html>
<html lang="bn" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SmartPOS SaaS - বাংলাদেশের ১ নম্বর মাল্টি-ব্রাঞ্চ POS ও বিজনেস সফটওয়্যার')</title>
  <meta name="description" content="POS, Inventory, Purchase, Multi-Branch, Accounting, Customer Due and SMS management SaaS platform for Bangladesh small and medium businesses.">
  <meta property="og:title" content="SmartPOS SaaS - Business Operating System for Bangladesh SMEs">
  <meta property="og:description" content="Run your retail, grocery, fashion, and wholesale business with multi-branch POS, real-time inventory, and due management.">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pos-saas-theme.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

  <style>
    body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    .landing-nav { backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.9); }
    .hero-section {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
      color: white;
      padding: 100px 0 80px 0;
    }
    .feature-card {
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 30px;
      transition: all 0.3s;
      background: white;
    }
    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 35px rgba(0,0,0,0.07);
      border-color: #0d9488;
    }
    .cta-btn-primary {
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      color: white;
      font-weight: 700;
      border-radius: 30px;
      padding: 12px 28px;
      border: none;
    }
    .cta-btn-primary:hover {
      background: linear-gradient(135deg, #0f766e 0%, #047857 100%);
      color: white;
    }
  </style>
</head>
<body class="bg-light">

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top landing-nav border-bottom shadow-sm py-3">
    <div class="container">
      <a class="navbar-brand fw-extrabold fs-3 text-dark d-flex align-items-center gap-2" href="{{ route('home') }}">
        <span class="badge bg-primary text-white p-2 rounded-3">🇧🇩</span> Smart<span class="text-primary">POS</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="landingNavbar">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
          <li class="nav-item"><a class="nav-link px-3 text-dark" href="{{ route('home') }}">হোম</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-dark" href="{{ route('features') }}">ফিচারসমূহ</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-dark" href="{{ route('solutions') }}">সলিউশন</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-dark" href="{{ route('pricing') }}">মূল্য ও প্ল্যান</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-dark" href="{{ route('contact') }}">যোগাযোগ</a></li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          @auth
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary fw-bold px-3">ড্যাশবোর্ড</a>
          @else
            <a href="{{ route('login') }}" class="btn btn-outline-secondary fw-semibold px-3">লগইন</a>
            <a href="{{ route('register') }}" class="btn cta-btn-primary shadow-sm">ফ্রি ট্রায়াল শুরু করুন</a>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <div style="padding-top: 70px;">
    @yield('content')
  </div>

  <!-- FOOTER -->
  <footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <h4 class="fw-bold mb-3 text-white">Smart<span class="text-primary">POS</span> SaaS</h4>
          <p class="text-secondary" style="line-height: 1.8;">
            বাংলাদেশের গ্রোসারি, সুপার শপ, ফ্যাশন, মোবাইল শপ, ফার্মেসি এবং হোলসেল ব্যবসার জন্য তৈরি একটি সম্পূর্ণ ক্লাউড বিজনেস অপারেটিং সিস্টেম।
          </p>
          <div class="badge bg-success-subtle text-success p-2">🇧🇩 Designed for Bangladeshi SMEs</div>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="text-white fw-bold mb-3">প্রোডাক্ট</h6>
          <ul class="list-unstyled text-secondary small">
            <li class="mb-2"><a href="{{ route('features') }}" class="text-secondary text-decoration-none">সুপারফাস্ট POS</a></li>
            <li class="mb-2"><a href="{{ route('features') }}" class="text-secondary text-decoration-none">মাল্টি-ব্রাঞ্চ স্টক</a></li>
            <li class="mb-2"><a href="{{ route('features') }}" class="text-secondary text-decoration-none">কাস্টমার বকেয়া (Due)</a></li>
            <li class="mb-2"><a href="{{ route('features') }}" class="text-secondary text-decoration-none">অ্যাকাউন্টিং ও লাভ-ক্ষতি</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="text-white fw-bold mb-3">ব্যবসার ধরণ</h6>
          <ul class="list-unstyled text-secondary small">
            <li class="mb-2"><a href="{{ route('solutions') }}" class="text-secondary text-decoration-none">সুপারশপ ও গ্রোসারি</a></li>
            <li class="mb-2"><a href="{{ route('solutions') }}" class="text-secondary text-decoration-none">ফ্যাশন ও বুটিক</a></li>
            <li class="mb-2"><a href="{{ route('solutions') }}" class="text-secondary text-decoration-none">ইলেকট্রনিক্স ও মোবাইল</a></li>
            <li class="mb-2"><a href="{{ route('solutions') }}" class="text-secondary text-decoration-none">পাইকারি ও ট্রেডিং</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="text-white fw-bold mb-3">সাপোর্ট ও যোগাযোগ</h6>
          <p class="text-secondary small mb-1"><i class="text-primary me-2">📍</i> ঢাকা, বাংলাদেশ</p>
          <p class="text-secondary small mb-1"><i class="text-primary me-2">📞</i> +৮৮০ ১৭০০-০০০০০০</p>
          <p class="text-secondary small mb-3"><i class="text-primary me-2">✉️</i> support@smartpos.com.bd</p>
          <div class="d-flex gap-3 small">
            <a href="{{ route('terms') }}" class="text-secondary text-decoration-none">Terms & Conditions</a>
            <a href="{{ route('privacy') }}" class="text-secondary text-decoration-none">Privacy Policy</a>
          </div>
        </div>
      </div>
      <div class="border-top border-secondary mt-4 pt-3 text-center text-secondary small">
        © {{ date('Y') }} SmartPOS SaaS. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
