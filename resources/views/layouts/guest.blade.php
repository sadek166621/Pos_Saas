<!DOCTYPE html>
<html lang="bn" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title', 'SmartPOS SaaS') - লগইন ও রেজিস্ট্রেশন</title>

  <!-- Color Modes (Forced Light Theme) -->
  <script src="{{ asset('assets/js/color-modes.js') }}"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pos-saas-theme.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

  <style>
    body {
      font-family: 'Hind Siliguri', 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top right, #f1f5f9, #e2e8f0);
    }
    .auth-card {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.6);
    }
  </style>
</head>
<body>
  <div class="container py-4">
    @yield('content')
  </div>

  <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
