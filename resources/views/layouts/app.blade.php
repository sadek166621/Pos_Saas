<!DOCTYPE html>
<html lang="bn" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SmartPOS SaaS') - Bangladesh Business OS</title>

  <!-- Color Modes (Forced Light Theme) -->
  <script src="{{ asset('assets/js/color-modes.js') }}"></script>

  <!-- Google Fonts: Hind Siliguri & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/flatpickr/flatpickr.min.css') }}">

  <!-- Layout Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pos-saas-theme.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

  <style>
    body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    .currency-symbol { font-family: 'Hind Siliguri', sans-serif; }
    .branch-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .pos-quick-btn { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white !important; font-weight: 600; border: none; }
    .pos-quick-btn:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .stat-card:hover { transform: translateY(-3px); }
  </style>

  @stack('styles')
</head>
<body>
  <div class="main-wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar">
      <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
          Smart<span>POS</span>
        </a>
        <div class="sidebar-toggler not-active">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="sidebar-body">
        <div class="px-3 py-2 mb-2 bg-light-subtle rounded border border-light-subtle mx-2">
          <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px;">ব্যবসা প্রতিষ্ঠান</small>
          <div class="fw-bold text-truncate text-primary" style="font-size: 13px;">
            {{ $currentTenant->name ?? 'SmartPOS SaaS' }}
          </div>
          <span class="badge bg-success-subtle text-success branch-badge mt-1">
            {{ $activeBranch->name ?? 'Main Branch' }}
          </span>
        </div>

        <ul class="nav" id="sidebarNav">
          <li class="nav-item nav-category">মূল মেনু</li>
          <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="nav-link">
              <i class="link-icon" data-lucide="layout-dashboard"></i>
              <span class="link-title">ড্যাশবোর্ড</span>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('pos.index') }}" class="nav-link pos-quick-btn rounded py-2 px-3 mx-2 my-1">
              <i class="link-icon text-white" data-lucide="shopping-cart"></i>
              <span class="link-title text-white fw-bold">POS কাউন্টার</span>
            </a>
          </li>

          <li class="nav-item nav-category">বিক্রি ও অর্ডার</li>
          <li class="nav-item {{ request()->routeIs('sales.index', 'sales.show') ? 'active' : '' }}">
            <a href="{{ route('sales.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="receipt"></i>
              <span class="link-title">বিক্রয় তালিকা</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('sales.returns') ? 'active' : '' }}">
            <a href="{{ route('sales.returns') }}" class="nav-link">
              <i class="link-icon" data-lucide="rotate-ccw"></i>
              <span class="link-title">পণ্য রিটার্ন</span>
            </a>
          </li>

          <li class="nav-item nav-category">প্রোডাক্ট ও স্টক</li>
          <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#productsMenu" role="button">
              <i class="link-icon" data-lucide="package"></i>
              <span class="link-title">প্রোডাক্টস</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('products.*') ? 'show' : '' }}" id="productsMenu" data-bs-parent="#sidebarNav">
              <ul class="nav sub-menu">
                <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link">সকল প্রোডাক্ট</a></li>
                <li class="nav-item"><a href="{{ route('products.create') }}" class="nav-link">নতুন প্রোডাক্ট যোগ</a></li>
                <li class="nav-item"><a href="{{ route('products.branch-pricing') }}" class="nav-link">ব্রাঞ্চ-ভিত্তিক প্রাইস</a></li>
                <li class="nav-item"><a href="{{ route('products.barcodes') }}" class="nav-link">বারকোড প্রিন্ট</a></li>
              </ul>
            </div>
          </li>

          <li class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#inventoryMenu" role="button">
              <i class="link-icon" data-lucide="warehouse"></i>
              <span class="link-title">ইনভেন্টরি ও স্টক</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('inventory.*') ? 'show' : '' }}" id="inventoryMenu" data-bs-parent="#sidebarNav">
              <ul class="nav sub-menu">
                <li class="nav-item"><a href="{{ route('inventory.index') }}" class="nav-link">স্টক ওভারভিউ</a></li>
                <li class="nav-item"><a href="{{ route('inventory.transfers') }}" class="nav-link">ব্রাঞ্চ স্টক ট্রান্সফার</a></li>
                <li class="nav-item"><a href="{{ route('inventory.adjustments') }}" class="nav-link">স্টক অ্যাডজাস্টমেন্ট</a></li>
                <li class="nav-item"><a href="{{ route('inventory.movements') }}" class="nav-link">স্টক মুভমেন্ট লগ</a></li>
              </ul>
            </div>
          </li>

          <li class="nav-item nav-category">ক্রয় ও সাপ্লায়ার</li>
          <li class="nav-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
            <a href="{{ route('purchases.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="truck"></i>
              <span class="link-title">পণ্য ক্রয় (Purchase)</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <a href="{{ route('suppliers.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="users"></i>
              <span class="link-title">সাপ্লায়ার ও বকেয়া</span>
            </a>
          </li>

          <li class="nav-item nav-category">কাস্টমার ও CRM</li>
          <li class="nav-item {{ request()->routeIs('customers.index', 'customers.show') ? 'active' : '' }}">
            <a href="{{ route('customers.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="user-check"></i>
              <span class="link-title">কাস্টমার তালিকা</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('customers.dues') ? 'active' : '' }}">
            <a href="{{ route('customers.dues') }}" class="nav-link">
              <i class="link-icon" data-lucide="alert-circle"></i>
              <span class="link-title">কাস্টমার বকেয়া (Due)</span>
            </a>
          </li>

          <li class="nav-item nav-category">হিসাব ও কর্মচারী</li>
          <li class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <a href="{{ route('expenses.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="file-minus"></i>
              <span class="link-title">দৈনিক খরচ (Expense)</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('accounts.*', 'accounting.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#accMenu" role="button">
              <i class="link-icon" data-lucide="dollar-sign"></i>
              <span class="link-title">অ্যাকাউন্টিং</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('accounts.*', 'accounting.*') ? 'show' : '' }}" id="accMenu" data-bs-parent="#sidebarNav">
              <ul class="nav sub-menu">
                <li class="nav-item"><a href="{{ route('accounts.index') }}" class="nav-link">ক্যাশ ও ব্যাংক অ্যাকাউন্ট</a></li>
                <li class="nav-item"><a href="{{ route('accounting.profit-loss') }}" class="nav-link">লাভ-ক্ষতি (P&L)</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <a href="{{ route('employees.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="briefcase"></i>
              <span class="link-title">কর্মচারী ও কমিশন</span>
            </a>
          </li>

          <li class="nav-item nav-category">রিপোর্ট ও শাখা</li>
          <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#reportMenu" role="button">
              <i class="link-icon" data-lucide="bar-chart-2"></i>
              <span class="link-title">ব্যবসায়িক রিপোর্ট</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reportMenu" data-bs-parent="#sidebarNav">
              <ul class="nav sub-menu">
                <li class="nav-item"><a href="{{ route('reports.sales') }}" class="nav-link">বিক্রয় রিপোর্ট</a></li>
                <li class="nav-item"><a href="{{ route('reports.inventory') }}" class="nav-link">স্টক ভ্যালুয়েশন</a></li>
                <li class="nav-item"><a href="{{ route('reports.customer-dues') }}" class="nav-link">বকেয়া রিপোর্ট</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
            <a href="{{ route('branches.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="git-branch"></i>
              <span class="link-title">মাল্টি-ব্রাঞ্চ (Multi-Branch)</span>
            </a>
          </li>

          <li class="nav-item nav-category">সিস্টেম ও প্যাকেজ</li>
          <li class="nav-item {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
            <a href="{{ route('subscription.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="credit-card"></i>
              <span class="link-title">সাবস্ক্রিপশন প্ল্যান</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="settings"></i>
              <span class="link-title">সেটিংস ও SMS</span>
            </a>
          </li>

          @if(auth()->user()->isSuperAdmin())
            <li class="nav-item nav-category">SUPER ADMIN</li>
            <li class="nav-item">
              <a href="{{ route('admin.dashboard') }}" class="nav-link text-warning">
                <i class="link-icon text-warning" data-lucide="shield-alert"></i>
                <span class="link-title fw-bold">সুপার অ্যাডমিন প্যানেল</span>
              </a>
            </li>
          @endif
        </ul>
      </div>
    </nav>

    <!-- PAGE WRAPPER -->
    <div class="page-wrapper">

      <!-- NAVBAR -->
      <nav class="navbar">
        <div class="navbar-content d-flex justify-content-between align-items-center">

          <!-- Branch Switcher Form -->
          <div class="d-flex align-items-center">
            <form action="{{ route('switch-branch') }}" method="POST" class="d-inline-flex align-items-center gap-2">
              @csrf
              <i data-lucide="map-pin" class="text-primary icon-sm"></i>
              <span class="fw-semibold text-secondary d-none d-sm-inline" style="font-size: 13px;">শাখা নির্বাচন:</span>
              <select name="branch_id" class="form-select form-select-sm fw-bold border-primary shadow-sm" onchange="this.form.submit()" style="width: auto; min-width: 170px;">
                <option value="all" {{ session('active_branch_id') === 'all' ? 'selected' : '' }}>সকল শাখা (All Branches)</option>
                @foreach($allBranches ?? [] as $b)
                  <option value="{{ $b->id }}" {{ session('active_branch_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                  </option>
                @endforeach
              </select>
            </form>
          </div>

          <!-- Topbar Actions -->
          <ul class="navbar-nav d-flex align-items-center gap-3">

            <!-- POS Quick Shortcut -->
            <li class="nav-item d-none d-md-block">
              <a href="{{ route('pos.index') }}" class="btn btn-sm pos-quick-btn d-flex align-items-center gap-1 shadow-sm px-3 py-1">
                <i data-lucide="shopping-bag" class="icon-sm"></i> POS ওপেন করুন
              </a>
            </li>


            <!-- User Profile Dropdown -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 14px;">
                  {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="d-none d-md-block text-start">
                  <div class="fw-bold lh-1" style="font-size: 13px;">{{ auth()->user()->name }}</div>
                  <small class="text-muted text-capitalize" style="font-size: 11px;">{{ auth()->user()->role }}</small>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <div class="px-3 py-2 border-bottom">
                  <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
                  <p class="text-secondary small mb-0">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('subscription.index') }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                  <i data-lucide="credit-card" class="icon-sm"></i> প্ল্যান ও সাবস্ক্রিপশন
                </a>
                <a href="{{ route('settings.index') }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                  <i data-lucide="settings" class="icon-sm"></i> সেটিংস
                </a>
                <div class="border-top my-1"></div>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                    <i data-lucide="log-out" class="icon-sm"></i> লগআউট
                  </button>
                </form>
              </div>
            </li>

          </ul>
        </div>
      </nav>

      <!-- PAGE CONTENT CONTAINER -->
      <div class="page-content">

        <!-- Flash Notifications -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" class="icon-md"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('warning'))
          <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-triangle" class="icon-md"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-octagon" class="icon-md"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('info'))
          <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i data-lucide="info" class="icon-md"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <!-- Main Body -->
        @yield('content')

      </div>

      <!-- FOOTER -->
      <footer class="footer d-flex flex-row align-items-center justify-content-between px-4 py-3 border-top small">
        <p class="text-secondary mb-1 mb-md-0">
          © {{ date('Y') }} <strong>SmartPOS SaaS</strong> — সর্বাধুনিক বাংলাদেশ-ফার্স্ট বিজনেস অপারেটিং সিস্টেম।
        </p>
        <p class="text-secondary">Made for Bangladesh SMEs 🇧🇩</p>
      </footer>

    </div>
  </div>

  <!-- Core JS -->
  <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
  <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>

  @stack('scripts')
</body>
</html>
