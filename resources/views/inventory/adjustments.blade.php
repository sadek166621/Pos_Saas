@extends('layouts.app')

@section('title', 'স্টক অ্যাডজাস্টমেন্ট ও ড্যামেজ - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">স্টক অ্যাডজাস্টমেন্ট ও ড্যামেজ রেকর্ড</h3>
    <p class="text-secondary small mb-0">নষ্ট পণ্য বাদ দেওয়া অথবা ফিজিক্যাল অডিট অনুযায়ী স্টক মিলকরণ</p>
  </div>
  <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">← ইনভেন্টরি</a>
</div>

<div class="row g-4">
  <div class="col-md-5">
    <div class="card bg-white border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">নতুন অ্যাডজাস্টমেন্ট এন্ট্রি</h5>
        <form action="{{ route('inventory.store-adjustment') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label small fw-bold">অ্যাডজাস্টমেন্টের ধরণ *</label>
            <select name="type" class="form-select" required>
              <option value="damage">পণ্য নষ্ট / ড্যামেজ (Damage / Loss - বিয়োগ)</option>
              <option value="subtraction">স্টক ঘাটতি (Physical Shortage - বিয়োগ)</option>
              <option value="addition">স্টক বাড়তি (Surplus Found - যোগ)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">পণ্য নির্বাচন করুন *</label>
            <select name="product_id" class="form-select" required>
              @foreach(\App\Models\Product::where('tenant_id', $currentTenant->id)->where('status', true)->get() as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">পরিমাণ (Quantity) *</label>
            <input type="number" step="0.5" name="quantity" class="form-control" required value="1" min="0.5">
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">কারণ / মন্তব্য</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="উদাঃ প্যাকেজিং ছেঁড়া বা মেয়াদোত্তীর্ণ পণ্য নষ্ট হয়েছে"></textarea>
          </div>

          <button type="submit" class="btn btn-warning w-100 fw-bold">অ্যাডজাস্টমেন্ট কার্যকর করুন</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card bg-white border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">সাম্প্রতিক অ্যাডজাস্টমেন্ট ইতিহাস</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>আইডি #</th>
                <th>ধরণ</th>
                <th>পণ্য ও পরিমাণ</th>
                <th>তারিখ</th>
                <th>মন্তব্য</th>
              </tr>
            </thead>
            <tbody>
              @forelse($adjustments as $adj)
                <tr>
                  <td class="fw-bold font-monospace">{{ $adj->adjustment_no }}</td>
                  <td>
                    <span class="badge {{ $adj->type === 'damage' ? 'bg-danger' : 'bg-warning' }} text-uppercase">
                      {{ $adj->type }}
                    </span>
                  </td>
                  <td>
                    @foreach($adj->items as $it)
                      <div class="small fw-bold">{{ $it->product->name ?? 'Product' }}: {{ (float)$it->quantity }} টি</div>
                    @endforeach
                  </td>
                  <td class="small text-muted">{{ $adj->created_at->format('d M, Y') }}</td>
                  <td class="small text-muted">{{ $adj->notes ?: '—' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">কোনো অ্যাডজাস্টমেন্ট রেকর্ড নেই</td>
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
