@extends('layouts.app')

@section('title', 'প্রোডাক্ট তালিকা - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">প্রোডাক্ট ও ইনভেন্টরি ক্যাটালগ</h3>
    <p class="text-secondary small mb-0">সকল প্রোডাক্টের তালিকা, মূল্য ও ব্রাঞ্চের স্টক স্থিতি</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('products.branch-pricing') }}" class="btn btn-outline-info">
      <i data-lucide="tag" class="icon-sm"></i> ব্রাঞ্চ প্রাইসিং
    </a>
    <a href="{{ route('products.barcodes') }}" class="btn btn-outline-secondary">
      <i data-lucide="printer" class="icon-sm"></i> বারকোড প্রিন্ট
    </a>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
      <i data-lucide="plus" class="icon-sm"></i> নতুন প্রোডাক্ট যোগ
    </a>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <!-- Filters -->
    <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-3">
      <div class="col-md-6">
        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="নাম, বারকোড বা SKU দিয়ে খুঁজুন...">
      </div>
      <div class="col-md-4">
        <select name="category_id" class="form-select">
          <option value="">সকল ক্যাটাগরি</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">সার্চ</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>বারকোড / SKU</th>
            <th>পণ্যের নাম</th>
            <th>ক্যাটাগরি</th>
            <th>ব্র্যান্ড</th>
            <th>ক্রয়মূল্য</th>
            <th>বিক্রয়মূল্য (গ্লোবাল)</th>
            <th>বর্তমান শাখা স্টক</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $p)
            <tr>
              <td>
                <span class="badge bg-light text-dark border font-monospace">{{ $p->barcode ?: $p->sku }}</span>
              </td>
              <td class="fw-bold">{{ $p->name }}</td>
              <td><span class="badge bg-light text-secondary border">{{ $p->category->name ?? '—' }}</span></td>
              <td>{{ $p->brand->name ?? '—' }}</td>
              <td class="text-secondary">৳{{ number_format($p->purchase_price, 2) }}</td>
              <td class="fw-bold text-primary">৳{{ number_format($p->selling_price, 2) }}</td>
              <td>
                @php $stock = $p->getStockForBranch($branch->id ?? 0); @endphp
                <span class="badge {{ $stock <= $p->min_stock_alert ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-2 py-1">
                  {{ $stock }} {{ $p->unit->code ?? 'pcs' }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="এডিট">
                  <i data-lucide="edit-2" class="icon-sm"></i>
                </a>
                <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি এই পণ্যটি ডিলিট করতে চান?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="ডিলিট">
                    <i data-lucide="trash-2" class="icon-sm"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">কোনো প্রোডাক্ট পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $products->links() }}
    </div>
  </div>
</div>
@endsection
