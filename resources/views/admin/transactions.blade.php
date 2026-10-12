@extends('layouts.admin')

@section('title', 'প্ল্যাটফর্ম পেমেন্ট ট্রানজেকশন - Super Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">পেমেন্ট ট্রানজেকশন হিস্ট্রি (Billing Transactions)</h3>
    <p class="text-secondary small mb-0">bKash, Nagad, Rocket, SSLCommerz ও ব্যাংক মারফত সকল গ্রাহকের সাবস্ক্রিপশন ফি জমা রসিদ</p>
  </div>
</div>

<div class="card bg-white stat-card">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ট্রানজেকশন ID</th>
            <th>ব্যবসার নাম (Client)</th>
            <th>পেমেন্ট গেটওয়ে</th>
            <th>পরিমাণ (৳)</th>
            <th>কারেন্সি</th>
            <th>স্ট্যাটাস</th>
            <th>গেটওয়ে রেফারেন্স</th>
            <th>তারিখ ও সময়</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $tx)
            <tr>
              <td class="fw-bold font-monospace text-primary">{{ $tx->transaction_id }}</td>
              <td>
                <div class="fw-bold">{{ $tx->tenant->name ?? 'Deleted Tenant' }}</div>
                <small class="text-muted">{{ $tx->tenant->phone ?? '' }}</small>
              </td>
              <td>
                <span class="badge bg-light text-dark border fw-bold">
                  {{ strtoupper($tx->gateway) }}
                </span>
              </td>
              <td class="fw-bold text-success fs-6">৳{{ number_format($tx->amount, 2) }}</td>
              <td><span class="badge bg-light text-secondary">{{ $tx->currency }}</span></td>
              <td>
                <span class="badge {{ $tx->status === 'success' ? 'bg-success' : ($tx->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                  {{ strtoupper($tx->status) }}
                </span>
              </td>
              <td class="small font-monospace">{{ $tx->gateway_reference ?: '—' }}</td>
              <td class="small">{{ $tx->created_at->format('d M, Y h:i A') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">কোনো পেমেন্ট ট্রানজেকশন পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $transactions->links() }}
    </div>
  </div>
</div>
@endsection
