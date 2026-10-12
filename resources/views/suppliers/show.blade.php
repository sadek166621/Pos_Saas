@extends('layouts.app')

@section('title', 'সাপ্লায়ার খাতা ও লেজার - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">{{ $supplier->name }} - লেজার খাতা</h3>
    <p class="text-secondary small mb-0">{{ $supplier->company_name }} | ফোন: {{ $supplier->phone }}</p>
  </div>
  <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 text-center">
      <small class="text-secondary">বর্তমান মোট বকেয়া (Due)</small>
      <h2 class="fw-bold text-danger my-2">৳{{ number_format($supplier->current_due, 2) }}</h2>
      <span class="badge bg-danger-subtle text-danger">পরিশোধযোগ্য</span>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <h5 class="fw-bold mb-3">সাপ্লায়ার পরিচিতি</h5>
      <div class="row small text-secondary">
        <div class="col-6 mb-2">কোম্পানি: <strong>{{ $supplier->company_name ?: '—' }}</strong></div>
        <div class="col-6 mb-2">ইমেইল: <strong>{{ $supplier->email ?: '—' }}</strong></div>
        <div class="col-6 mb-2">মোবাইল: <strong>{{ $supplier->phone }}</strong></div>
        <div class="col-6 mb-2">ঠিকানা: <strong>{{ $supplier->address ?: $supplier->city }}</strong></div>
      </div>
    </div>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">লেনদেন ও লেজার স্টেটমেন্ট (Ledger)</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>তারিখ</th>
            <th>বিবরণ / রেফারেন্স</th>
            <th>ধরণ</th>
            <th>জমা / ডেবিট (পরিশোধ ৳)</th>
            <th>খরচ / ক্রেডিট (পণ্য ক্রয় ৳)</th>
            <th>ব্যালেন্স (Due ৳)</th>
            <th>মন্তব্য</th>
          </tr>
        </thead>
        <tbody>
          @forelse($supplier->ledgers as $l)
            <tr>
              <td class="small">{{ $l->created_at->format('d M Y, h:i A') }}</td>
              <td class="font-monospace fw-bold">{{ $l->reference_no ?: '—' }}</td>
              <td><span class="badge bg-light text-dark border text-uppercase">{{ $l->type }}</span></td>
              <td class="text-success fw-bold">
                {{ $l->debit > 0 ? '৳'.number_format($l->debit, 2) : '—' }}
              </td>
              <td class="text-danger fw-bold">
                {{ $l->credit > 0 ? '৳'.number_format($l->credit, 2) : '—' }}
              </td>
              <td class="fw-bold fs-6">৳{{ number_format($l->balance, 2) }}</td>
              <td class="small text-muted">{{ $l->notes ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো লেজার রেকর্ড পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
