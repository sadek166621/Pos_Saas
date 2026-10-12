@extends('layouts.app')

@section('title', 'ইনভয়েস বিস্তারিত - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ইনভয়েস #{{ $sale->invoice_no }}</h3>
    <p class="text-secondary small mb-0">শাখা: {{ $sale->branch->name }} | তারিখ: {{ $sale->sale_date->format('d M Y, h:i A') }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="btn btn-dark">
      <i data-lucide="printer" class="icon-sm"></i> রিসিট প্রিন্ট
    </a>
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">← ফিরে যান</a>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">বিক্রিত পণ্যসমূহ</h5>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>পণ্য</th>
                <th>পরিমাণ</th>
                <th>একক দর</th>
                <th>মোট</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sale->items as $item)
                <tr>
                  <td class="fw-bold">{{ $item->product_name }}</td>
                  <td>{{ (float)$item->quantity }}</td>
                  <td>৳{{ number_format($item->unit_price, 2) }}</td>
                  <td class="fw-bold">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row justify-content-end mt-4">
          <div class="col-md-5">
            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>সাবটোটাল:</span>
              <span>৳{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
              <div class="d-flex justify-content-between small text-danger mb-1">
                <span>ছাড়:</span>
                <span>-৳{{ number_format($sale->discount_amount, 2) }}</span>
              </div>
            @endif
            @if($sale->vat_amount > 0)
              <div class="d-flex justify-content-between small text-secondary mb-1">
                <span>ভ্যাট:</span>
                <span>+৳{{ number_format($sale->vat_amount, 2) }}</span>
              </div>
            @endif
            <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
              <span>সর্বমোট:</span>
              <span class="text-primary">৳{{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between text-success small mt-1">
              <span>পরিশোধিত:</span>
              <span>৳{{ number_format($sale->paid, 2) }}</span>
            </div>
            @if($sale->due > 0)
              <div class="d-flex justify-content-between text-danger fw-bold small mt-1">
                <span>বকেয়া (Due):</span>
                <span>৳{{ number_format($sale->due, 2) }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- RETURN ACTION -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <h5 class="fw-bold mb-3 text-danger">পণ্য রিটার্ন ও রিফান্ড প্রসেস</h5>
      <form action="{{ route('sales.return', $sale->id) }}" method="POST">
        @csrf
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold">রিটার্নযোগ্য পণ্য *</label>
            <select name="items[0][sale_item_id]" class="form-select" required>
              @foreach($sale->items as $it)
                <option value="{{ $it->id }}">{{ $it->product_name }} (মূল্য: ৳{{ $it->unit_price }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">রিটার্ন পরিমাণ *</label>
            <input type="number" name="items[0][quantity]" class="form-control" required value="1" min="1">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">রিফান্ড টাইপ</label>
            <select name="refund_type" class="form-select">
              <option value="cash">নগদ ফেরত (Cash Refund)</option>
              <option value="store_credit">স্টোর ক্রেডিট (Store Credit)</option>
              <option value="due_deduct">বাকি থেকে মাইনাস (Due Deduct)</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold">রিটার্নের কারণ *</label>
          <input type="text" name="reason" class="form-control" required placeholder="উদাঃ সাইজ ছোট হওয়া বা পণ্যে ত্রুটি">
        </div>
        <button type="submit" class="btn btn-outline-danger fw-bold" onclick="return confirm('আপনি কি নিশ্চিত যে পণ্য রিটার্ন ও স্টক রিস্টোর করবেন?');">
          রিটার্ন নিশ্চিত করুন
        </button>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Customer & Cashier Info -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 mb-4">
      <h5 class="fw-bold mb-3">অর্ডার বিবরণী</h5>
      <div class="small text-secondary">
        <div class="mb-2">কাস্টমার: <strong>{{ $sale->customer->name ?? 'সাধারণ গ্রাহক (Walk-in)' }}</strong></div>
        <div class="mb-2">ফোন: <strong>{{ $sale->customer->phone ?? '—' }}</strong></div>
        <div class="mb-2">ক্যাশিয়ার: <strong>{{ $sale->cashier->name ?? 'Cashier' }}</strong></div>
        <div class="mb-2">পেমেন্ট মেথড: <strong class="text-uppercase">{{ $sale->payment_method }}</strong></div>
      </div>
    </div>

    <!-- Payments Breakdown -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <h5 class="fw-bold mb-3">পেমেন্ট হিস্ট্রি</h5>
      <ul class="list-group list-group-flush small">
        @foreach($sale->payments as $p)
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <div>
              <span class="text-uppercase fw-bold">{{ $p->method }}</span>
              <div class="text-muted" style="font-size: 11px;">{{ $p->paid_at->format('d M, h:i A') }}</div>
            </div>
            <strong class="text-success">৳{{ number_format($p->amount, 2) }}</strong>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endsection
