@extends('layouts.admin')

@section('title', 'ব্যবসা প্রতিষ্ঠান পরিচালনা - Super Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">নিবন্ধিত ব্যবসা প্রতিষ্ঠান (Tenants Management)</h3>
    <p class="text-secondary small mb-0">সকল SaaS ক্লায়েন্ট, ব্রাঞ্চ লিমিট, সাবস্ক্রিপশন স্ট্যাটাস এবং অ্যাকাউন্ট অ্যাকশন</p>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="card bg-white stat-card mb-4">
  <div class="card-body p-3">
    <form action="{{ route('admin.tenants') }}" method="GET" class="row g-2">
      <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="ব্যবসার নাম, মোবাইল অথবা ইমেইল দিয়ে সার্চ করুন..." value="{{ request('q') }}">
      </div>
      <div class="col-md-4">
        <select name="status" class="form-select">
          <option value="">সকল স্ট্যাটাস</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
          <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>ট্রায়াল (Trial)</option>
          <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>সাসপেন্ডেড (Suspended)</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i data-lucide="search" class="icon-sm"></i> সার্চ করুন
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Tenants Table -->
<div class="card bg-white stat-card">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ব্যবসা / ডোমেইন</th>
            <th>যোগাযোগ (Mobile/Email)</th>
            <th>বর্তমান প্যাকেজ</th>
            <th>শাখা ও ইউজার</th>
            <th>স্ট্যাটাস</th>
            <th>নিবন্ধন তারিখ</th>
            <th>অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tenants as $t)
            <tr>
              <td>
                <div class="fw-bold text-dark fs-6">{{ $t->name }}</div>
                <small class="text-muted font-monospace">{{ $t->slug }}.possaas.com</small>
              </td>
              <td>
                <div>{{ $t->phone ?: '—' }}</div>
                <small class="text-muted">{{ $t->email ?: '—' }}</small>
              </td>
              <td>
                <span class="badge bg-light text-primary border fs-xs">
                  {{ $t->activeSubscription->plan->name ?? 'Standard' }}
                </span>
              </td>
              <td>
                <span class="badge bg-light text-dark border">{{ $t->branches->count() }} ব্রাঞ্চ</span>
                <span class="badge bg-light text-secondary border">{{ $t->users->count() }} ইউজার</span>
              </td>
              <td>
                <span class="badge {{ $t->status === 'active' ? 'bg-success' : ($t->status === 'trial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                  {{ strtoupper($t->status) }}
                </span>
              </td>
              <td class="small">{{ $t->created_at->format('d M, Y') }}</td>
              <td>
                <div class="d-flex align-items-center gap-1">
                  <a href="{{ route('admin.tenants.impersonate', $t->id) }}" class="btn btn-xs btn-outline-primary" title="এই টেন্যান্টের POS ও ড্যাশবোর্ডে প্রবেশ করুন">
                    স্টোর দেখুন
                  </a>
                  <form action="{{ route('admin.tenants.toggle', $t->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত এই ব্যবসার স্ট্যাটাস পরিবর্তন করতে চান?');" class="d-inline">
                    @csrf
                    @if($t->status === 'active')
                      <button type="submit" class="btn btn-xs btn-outline-danger">সাসপেন্ড</button>
                    @else
                      <button type="submit" class="btn btn-xs btn-outline-success">সক্রিয়</button>
                    @endif
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো ব্যবসা প্রতিষ্ঠান পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $tenants->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
