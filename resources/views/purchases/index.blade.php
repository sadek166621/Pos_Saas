@extends('layouts.app')

@section('title', 'পণ্য ক্রয় (Purchases) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">পণ্য ক্রয় ও সাপ্লায়ার চালান (Purchases)</h3>
    <p class="text-secondary small mb-0">সাপ্লায়ারদের কাছ থেকে পণ্য ক্রয়, ইনভয়েস ও স্টক গ্রহণ</p>
  </div>
  <a href="{{ route('purchases.create') }}" class="btn btn-primary">
    <i data-lucide="plus-circle" class="icon-sm"></i> নতুন পণ্য ক্রয় করুন
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>চালান #</th>
            <th>সাপ্লায়ার</th>
            <th>শাখা</th>
            <th>তারিখ</th>
            <th>সর্বমোট বিল</th>
            <th>পরিশোধিত</th>
            <th>বকেয়া</th>
            <th>স্ট্যাটাস</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($purchases as $p)
            <tr>
              <td class="fw-bold text-primary font-monospace">{{ $p->purchase_no }}</td>
              <td class="fw-bold">{{ $p->supplier->name ?? '—' }}</td>
              <td><span class="badge bg-light text-dark border">{{ $p->branch->name ?? '—' }}</span></td>
              <td class="small">{{ $p->purchase_date->format('d M, Y') }}</td>
              <td class="fw-bold">৳{{ number_format($p->total, 2) }}</td>
              <td class="text-success">৳{{ number_format($p->paid, 2) }}</td>
              <td>
                @if($p->due > 0)
                  <span class="text-danger fw-bold">৳{{ number_format($p->due, 2) }}</span>
                @else
                  <span class="text-muted">৳০.০০</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $p->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }} text-uppercase">
                  {{ $p->payment_status }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('purchases.show', $p->id) }}" class="btn btn-sm btn-outline-primary">বিস্তারিত</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">কোনো ক্রয়ের রেকর্ড পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $purchases->links() }}
    </div>
  </div>
</div>
@endsection
