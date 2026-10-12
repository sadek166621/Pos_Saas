@extends('layouts.guest')

@section('title', 'লগইন - SmartPOS SaaS')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-9 col-lg-7 col-xl-5">
    <div class="card auth-card bg-white border-0 p-4 p-md-5">
      <div class="text-center mb-4">
        <a href="{{ route('home') }}" class="fs-2 fw-extrabold text-dark text-decoration-none d-inline-flex align-items-center gap-2">
          <span class="badge bg-primary text-white p-2 rounded-3">🇧🇩</span> Smart<span class="text-primary">POS</span>
        </a>
        <h4 class="fw-bold mt-3 mb-1">অ্যাকাউন্টে লগইন করুন</h4>
        <p class="text-secondary small">আপনার ব্যবসা পরিচালনা করতে ক্রেডেনশিয়াল দিন</p>
      </div>

      @if($errors->any())
        <div class="alert alert-danger py-2 small mb-4">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST" id="loginForm">
        @csrf
        <div class="mb-3">
          <label class="form-label small fw-bold">ইমেইল ঠিকানা</label>
          <input type="email" name="email" id="emailInput" class="form-control" required value="{{ old('email', 'admin@possaas.com') }}" placeholder="name@example.com">
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <label class="form-label small fw-bold">পাসওয়ার্ড</label>
            <a href="javascript:;" onclick="alert('পাসওয়ার্ড ভুলে গেলে সাপোর্ট নাম্বারে যোগাযোগ করুন বা password123 ব্যবহার করুন।');" class="small text-primary text-decoration-none">পাসওয়ার্ড ভুলে গেছেন?</a>
          </div>
          <div class="input-group">
            <input type="password" name="password" id="passwordInput" class="form-control" required value="password123" placeholder="••••••••">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
              <span id="eyeText">👁️</span>
            </button>
          </div>
        </div>
        <div class="form-check mb-4">
          <input type="checkbox" name="remember" class="form-check-input" id="rememberMe" checked>
          <label class="form-check-label small" for="rememberMe">মনে রাখুন (Remember me)</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm mb-3">
          লগইন করুন →
        </button>
      </form>

      <!-- DEMO QUICK LOGIN BUTTONS -->
      <div class="p-3 bg-light rounded-4 border mb-3">
        <small class="fw-bold text-muted d-block mb-2 text-center text-uppercase" style="font-size: 11px;">⚡ এক ক্লিকে সরাসরি লগইন করুন</small>
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-sm btn-outline-warning text-dark text-start d-flex justify-content-between align-items-center" onclick="fillAndSubmit('admin@possaas.com', 'password123')">
            <span><strong>🛡️ সুপার অ্যাডমিন:</strong> প্ল্যাটফর্ম কন্ট্রোল</span>
            <span class="badge bg-warning text-dark">লগইন</span>
          </button>
          <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center" onclick="fillAndSubmit('owner@rahmanfashion.com', 'password123')">
            <span><strong>👑 ব্যবসার মালিক (Owner):</strong> রহমান ফ্যাশন</span>
            <span class="badge bg-primary">লগইন</span>
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary text-start d-flex justify-content-between align-items-center" onclick="fillAndSubmit('manager.dhaka@rahmanfashion.com', 'password123')">
            <span><strong>🏢 ঢাকা শাখা ম্যানেজার:</strong> কাজী তারিকুল</span>
            <span class="badge bg-secondary">লগইন</span>
          </button>
          <button type="button" class="btn btn-sm btn-outline-success text-start d-flex justify-content-between align-items-center" onclick="fillAndSubmit('cashier1@rahmanfashion.com', 'password123')">
            <span><strong>🛒 কাউন্টার ক্যাশিয়ার:</strong> সাব্বির হোসেন</span>
            <span class="badge bg-success">লগইন</span>
          </button>
        </div>
      </div>

      <div class="text-center small text-secondary">
        নতুন ব্যবসা শুরু করছেন? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">১৪ দিনের ট্রায়াল খুলুন</a>
      </div>
    </div>
  </div>
</div>

<script>
  function fillAndSubmit(email, pass) {
    document.getElementById('emailInput').value = email;
    document.getElementById('passwordInput').value = pass;
    document.getElementById('loginForm').submit();
  }

  function togglePasswordVisibility() {
    const pwd = document.getElementById('passwordInput');
    const eye = document.getElementById('eyeText');
    if (pwd.type === 'password') {
      pwd.type = 'text';
      eye.textContent = '🙈';
    } else {
      pwd.type = 'password';
      eye.textContent = '👁️';
    }
  }
</script>
@endsection
