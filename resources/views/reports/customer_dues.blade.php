@extends('layouts.app')

@section('title', 'কাস্টমার বকেয়া রিপোর্ট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">কাস্টমার বকেয়া রিপোর্ট (Customer Due Report)</h3>
    <p class="text-secondary small mb-0">সকল বাকি থাকা গ্রাহক, ক্রেডিট লিমিট এবং পাওনা টাকা আদায় পর্যবেক্ষণ</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i data-lucide="printer" class="icon-sm"></i> প্রিন্ট রিপোর্ট
    </button>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <span class="text-secondary small fw-semibold">সর্বমোট বকেয়া পাওনা (Total Outstanding Due)</span>
      <h2 class="fw-bold text-danger mt-1 mb-0">৳{{ number_format($totalCustomerDue, 2) }}</h2>
    </div>
    <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 border border-danger">
      {{ $customers->total() }} জন বকেয়া গ্রাহক
    </span>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>গ্রাহকের নাম (Customer)</th>
            <th>মোবাইল নম্বর</th>
            <th>ঠিকানা / শহর</th>
            <th>ক্রেডিট লিমিট (৳)</th>
            <th>বর্তমান বকেয়া (৳)</th>
            <th>অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $c)
            <tr>
              <td class="fw-bold">{{ $c->name }}</td>
              <td class="font-monospace">{{ $c->phone ?: '—' }}</td>
              <td>{{ $c->address ?: '—' }}</td>
              <td>৳{{ number_format($c->credit_limit ?? 0, 2) }}</td>
              <td class="fw-bold text-danger fs-6">৳{{ number_format($c->current_due, 2) }}</td>
              <td>
                <div class="d-flex gap-2">
                  <a href="{{ route('customers.show', $c->id) }}" class="btn btn-xs btn-outline-primary">লেজার ও হিস্ট্রি</a>
                  <button class="btn btn-xs btn-outline-info" onclick="alert('এসএমএস পাঠানো হয়েছে: বকেয়া ৳{{ number_format($c->current_due, 2) }}');">
                    <i data-lucide="send" class="icon-xs"></i> এসএমএস রিমাইন্ডার
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">কোনো গ্রাহকের বকেয়া নেই। চমৎকার!</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $customers->links() }}
    </div>
  </div>
</div>
@endsection
