@extends('layouts.admin')

@section('title', 'সুপার অ্যাডমিন ওভারভিউ - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">সুপার অ্যাডমিন অ্যানালিটিক্স</h3>
    <p class="text-secondary small mb-0">প্ল্যাটফর্মের সামগ্রিক রাজস্ব, সাবস্ক্রিপশন এবং ব্যবসা প্রতিষ্ঠানের রিয়েলটাইম পরিসংখ্যান</p>
  </div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
  <!-- MRR -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">মাসিক পৌনঃপুনিক আয় (MRR)</span>
      <h3 class="fw-bold text-primary mt-1 mb-0">৳{{ number_format($mrr) }}</h3>
    </div>
  </div>

  <!-- Total Revenue -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">সর্বমোট অর্জিত রাজস্ব</span>
      <h3 class="fw-bold text-success mt-1 mb-0">৳{{ number_format($totalRevenue) }}</h3>
    </div>
  </div>

  <!-- Total Tenants -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">মোট ব্যবসা (Tenants)</span>
      <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalBusinesses }}</h3>
    </div>
  </div>

  <!-- Active Tenants -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">সক্রিয় ব্যবসা</span>
      <h3 class="fw-bold text-success mt-1 mb-0">{{ $activeCount }}</h3>
    </div>
  </div>

  <!-- Trial Tenants -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">ট্রায়ালে আছে</span>
      <h3 class="fw-bold text-warning mt-1 mb-0">{{ $trialCount }}</h3>
    </div>
  </div>

  <!-- Suspended Tenants -->
  <div class="col-md-4 col-xl-2">
    <div class="card bg-white stat-card p-3">
      <span class="text-secondary small fw-semibold">সাসপেন্ডেড</span>
      <h3 class="fw-bold text-danger mt-1 mb-0">{{ $suspendedCount }}</h3>
    </div>
  </div>
</div>

<!-- Recent Tenants & Transactions -->
<div class="row g-4">
  <!-- Recent Tenants -->
  <div class="col-lg-7">
    <div class="card bg-white stat-card">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">সাম্প্রতিক নিবন্ধিত ব্যবসা প্রতিষ্ঠান</h5>
          <a href="{{ route('admin.tenants') }}" class="btn btn-sm btn-outline-primary">সকল দেখুন</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ব্যবসার নাম</th>
                <th>প্যাকেজ</th>
                <th>শাখা সংখ্যা</th>
                <th>স্ট্যাটাস</th>
                <th>নিবন্ধন তারিখ</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTenants as $t)
                <tr>
                  <td class="fw-bold text-dark">{{ $t->name }}</td>
                  <td><span class="badge bg-light text-primary border">{{ $t->activeSubscription->plan->name ?? 'Trial' }}</span></td>
                  <td>{{ $t->branches->count() }} টি শাখা</td>
                  <td>
                    <span class="badge {{ $t->status === 'active' ? 'bg-success' : ($t->status === 'trial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                      {{ strtoupper($t->status) }}
                    </span>
                  </td>
                  <td class="small">{{ $t->created_at->format('d M, Y') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-3 text-muted">কোনো ব্যবসা প্রতিষ্ঠান নেই।</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Transactions -->
  <div class="col-lg-5">
    <div class="card bg-white stat-card">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">সাম্প্রতিক পেমেন্ট</h5>
          <a href="{{ route('admin.transactions') }}" class="btn btn-sm btn-outline-primary">সকল দেখুন</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ট্রানজেকশন ID</th>
                <th>গেটওয়ে</th>
                <th>পরিমাণ</th>
                <th>স্ট্যাটাস</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTransactions as $tx)
                <tr>
                  <td class="small font-monospace fw-bold">{{ $tx->transaction_id }}</td>
                  <td><span class="badge bg-light text-dark border">{{ strtoupper($tx->gateway) }}</span></td>
                  <td class="fw-bold text-success">৳{{ number_format($tx->amount) }}</td>
                  <td>
                    <span class="badge {{ $tx->status === 'success' ? 'bg-success' : 'bg-danger' }}">
                      {{ strtoupper($tx->status) }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-3 text-muted">কোনো পেমেন্ট তথ্য পাওয়া যায়নি।</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
