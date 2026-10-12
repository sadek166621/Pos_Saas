@extends('layouts.app')

@section('title', 'পণ্য রিটার্ন তালিকা - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">পণ্য রিটার্ন ও রিফান্ড হিস্ট্রি</h3>
    <p class="text-secondary small mb-0">গ্রাহকদের ফেরতকৃত পণ্য ও রিফান্ড বিবরণ</p>
  </div>
  <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">← সকল বিক্রি</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>রিটার্ন #</th>
            <th>মূল ইনভয়েস #</th>
            <th>শাখা</th>
            <th>কাস্টমার</th>
            <th>ফেরত পণ্যসমূহ</th>
            <th>রিফান্ড টাকা (৳)</th>
            <th>রিফান্ড টাইপ</th>
            <th>কারণ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($returns as $r)
            <tr>
              <td class="fw-bold font-monospace text-danger">{{ $r->return_no }}</td>
              <td>
                <a href="{{ route('sales.show', $r->sale_id) }}" class="fw-bold text-primary">
                  {{ $r->sale->invoice_no ?? '—' }}
                </a>
              </td>
              <td><span class="badge bg-light text-dark border">{{ $r->branch->name ?? '—' }}</span></td>
              <td>{{ $r->customer->name ?? 'Walk-in' }}</td>
              <td>
                @foreach($r->items as $it)
                  <div class="small fw-bold">{{ $it->product->name ?? '—' }}: {{ (float)$it->quantity }} টি</div>
                @endforeach
              </td>
              <td class="fw-bold text-danger">৳{{ number_format($r->refund_amount, 2) }}</td>
              <td><span class="badge bg-secondary text-uppercase">{{ $r->refund_type }}</span></td>
              <td class="small text-muted">{{ $r->reason ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">কোনো রিটার্ন রেকর্ড নেই</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $returns->links() }}
    </div>
  </div>
</div>
@endsection
