<!DOCTYPE html>
<html lang="bn" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'সুপার অ্যাডমিন প্যানেল - SmartPOS SaaS')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pos-saas-theme.css') }}">

  <style>
    body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, select, textarea {
      font-family: 'Hind Siliguri', 'Inter', -apple-system, sans-serif;
    }
    .admin-sidebar { background: #0f172a; color: #94a3b8; }
    .admin-sidebar .sidebar-brand { color: #f8fafc; font-weight: 700; }
    .admin-sidebar .sidebar-brand span { color: #38bdf8; }
    .admin-sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin: 2px 12px; }
    .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { color: #fff; background: #1e293b; }
    .admin-sidebar .link-icon { width: 18px; height: 18px; margin-right: 12px; }
    .stat-card { border: none; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
  </style>

  @stack('styles')
</head>
<body class="bg-light">
  <div class="main-wrapper">

    <!-- ADMIN SIDEBAR -->
    <nav class="sidebar admin-sidebar">
      <div class="sidebar-header border-bottom border-secondary border-opacity-25">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
          Smart<span>POS</span> <small class="badge bg-danger fs-xs ms-1">SUPER ADMIN</small>
        </a>
      </div>
      <div class="sidebar-body py-3">
        <ul class="nav">
          <li class="nav-item nav-category text-uppercase text-secondary small px-3 mb-2">প্ল্যাটফর্ম পরিচালনা</li>
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="link-icon" data-lucide="layout-dashboard"></i>
              <span class="link-title">ওভারভিউ ড্যাশবোর্ড</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.tenants') }}" class="nav-link {{ request()->routeIs('admin.tenants') ? 'active' : '' }}">
              <i class="link-icon" data-lucide="building-2"></i>
              <span class="link-title">ব্যবসা প্রতিষ্ঠান (Tenants)</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.plans') }}" class="nav-link {{ request()->routeIs('admin.plans') ? 'active' : '' }}">
              <i class="link-icon" data-lucide="layers"></i>
              <span class="link-title">প্যাকেজ ও মূল্য নির্ধারণ</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
              <i class="link-icon" data-lucide="wallet"></i>
              <span class="link-title">পেমেন্ট ট্রানজেকশন</span>
            </a>
          </li>

          <li class="nav-item nav-category text-uppercase text-secondary small px-3 mt-4 mb-2">সুইচ ও প্রস্থান</li>
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-info">
              <i class="link-icon" data-lucide="store"></i>
              <span class="link-title">ব্যবসায়িক ড্যাশবোর্ডে যান</span>
            </a>
          </li>
          <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST" id="adminLogoutForm" class="d-none">
              @csrf
            </form>
            <a href="javascript:;" onclick="document.getElementById('adminLogoutForm').submit()" class="nav-link text-danger">
              <i class="link-icon" data-lucide="log-out"></i>
              <span class="link-title">লগআউট করুন</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- PAGE CONTENT -->
    <div class="page-wrapper">
      <nav class="navbar bg-white border-bottom shadow-sm">
        <div class="navbar-content d-flex justify-content-between align-items-center w-100 px-4">
          <div class="fw-bold text-dark fs-5">
            সুপার অ্যাডমিন কন্ট্রোল সেন্টার
          </div>
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success-subtle text-success border border-success px-3 py-1">
              Super Admin: {{ auth()->user()->name }}
            </span>
          </div>
        </div>
      </nav>

      <div class="page-content p-4">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i data-lucide="check-circle" class="icon-sm me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i data-lucide="alert-triangle" class="icon-sm me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
  @stack('scripts')
</body>
</html>
