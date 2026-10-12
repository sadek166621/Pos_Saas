@extends('layouts.app')

@section('title', 'কাস্টমার লেজার খাতা - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">{{ $customer->name }} - বাকি ও লেজার হিস্ট্রি</h3>
    <p class="text-secondary small mb-0">মোবাইল: {{ $customer->phone ?: '—' }} | কাস্টমার টাইপ: {{ strtoupper($customer->type) }}</p>
  </div>
  <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 text-center">
      <small class="text-secondary">বর্তমান মোট বকেয়া (Due)</small>
      <h2 class="fw-bold text-danger my-2">৳{{ number_format($customer->current_due, 2) }}</h2>
      <div class="small text-muted">লয়্যালটি পয়েন্ট: <strong>{{ $customer->loyalty_points }}</strong></div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <h5 class="fw-bold mb-3">কাস্টমার প্রোফাইল</h5>
      <div class="row small text-secondary">
        <div class="col-6 mb-2">ক্রেডিট লিমিট: <strong>৳{{ number_format($customer->credit_limit, 2) }}</strong></div>
        <div class="col-6 mb-2">ইমেইল: <strong>{{ $customer->email ?: '—' }}</strong></div>
        <div class="col-6 mb-2">ঠিকানা: <strong>{{ $customer->address ?: '—' }}</strong></div>
        <div class="col-6 mb-2">মোট অর্ডারের সংখ্যা: <strong>{{ $customer->sales->count() }} টি</strong></div>
      </div>
    </div>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">লেজার ও লেনদেন বিবরণী (Customer Ledger)</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>তারিখ ও সময়</th>
            <th>রেফারেন্স</th>
            <th>ধরণ</th>
            <th>বাকি বৃদ্ধি / ডেবিট (৳)</th>
            <th>বাকি আদায় / ক্রেডিট (৳)</th>
            <th>অবশিষ্ট ব্যালেন্স (Due ৳)</th>
            <th>মন্তব্য</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customer->ledgers as $l)
            <tr>
              <td class="small">{{ $l->created_at->format('d M Y, h:i A') }}</td>
              <td class="font-monospace fw-bold">{{ $l->reference_no ?: '—' }}</td>
              <td><span class="badge bg-light text-dark border text-uppercase">{{ $l->type }}</span></td>
              <td class="text-danger fw-bold">
                {{ $l->debit > 0 ? '+৳'.number_format($l->debit, 2) : '—' }}
              </td>
              <td class="text-success fw-bold">
                {{ $l->credit > 0 ? '-৳'.number_format($l->credit, 2) : '—' }}
              </td>
              <td class="fw-bold fs-6">৳{{ number_format($l->balance, 2) }}</td>
              <td class="small text-muted">{{ $l->notes ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো লেনদেন রেকর্ড পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
