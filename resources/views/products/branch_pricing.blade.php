@extends('layouts.app')

@section('title', 'ব্রাঞ্চ-ভিত্তিক পণ্য মূল্য - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ব্রাঞ্চ-ভিত্তিক স্পেশাল মূল্য নির্ধারণ (Branch Price Matrix)</h3>
    <p class="text-secondary small mb-0">একই পণ্যের জন্য বিভিন্ন শহরে/শাখায় আলাদা বিক্রয়মূল্য ওভাররাইড করুন</p>
  </div>
  <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">← প্রোডাক্ট তালিকা</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="alert alert-info py-2 small mb-4">
      💡 <strong>নিয়ম:</strong> কোনো শাখায় আলাদা মূল্য সেট করা থাকলে POS কাউন্টারে সেই শাখার নির্ধারিত মূল্য স্বয়ংক্রিয়ভাবে কার্যকর হবে। আলাদা মূল্য না থাকলে গ্লোবাল রিটেইল মূল্য কার্যকর থাকবে।
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>পণ্যের নাম</th>
            <th>গ্লোবাল মূল্য (Global Price)</th>
            @foreach($branches as $b)
              <th>{{ $b->name }}</th>
            @endforeach
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $p)
            <tr>
              <td class="fw-bold">{{ $p->name }}</td>
              <td class="fw-bold text-dark">৳{{ number_format($p->selling_price, 2) }}</td>
              @foreach($branches as $b)
                <td>
                  @php 
                    $override = $p->branchPrices->firstWhere('branch_id', $b->id); 
                  @endphp
                  @if($override && $override->selling_price > 0)
                    <span class="badge bg-success-subtle text-success fs-6 fw-bold">
                      ৳{{ number_format($override->selling_price, 2) }}
                    </span>
                  @else
                    <span class="text-muted small">৳{{ number_format($p->selling_price, 2) }} (গ্লোবাল)</span>
                  @endif
                </td>
              @endforeach
              <td class="text-end">
                <a href="{{ route('products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">মূল্য পরিবর্তন</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
