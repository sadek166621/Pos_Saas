@extends('layouts.app')

@section('title', 'ব্রাঞ্চ স্টক ট্রান্সফার - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ব্রাঞ্চ-টু-ব্রাঞ্চ স্টক ট্রান্সফার</h3>
    <p class="text-secondary small mb-0">এক শাখা থেকে অন্য শাখায় পণ্য স্থানান্তর ও প্রাপ্তি রেকর্ড</p>
  </div>
  <a href="{{ route('inventory.create-transfer') }}" class="btn btn-primary">
    <i data-lucide="send" class="icon-sm"></i> নতুন স্টক ট্রান্সফার করুন
  </a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ট্রান্সফার #</th>
            <th>প্রেরক শাখা (From)</th>
            <th>প্রাপক শাখা (To)</th>
            <th>তারিখ</th>
            <th>আইটেম ও পরিমাণ</th>
            <th>স্ট্যাটাস</th>
            <th>মন্তব্য</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transfers as $t)
            <tr>
              <td class="fw-bold font-monospace text-primary">{{ $t->transfer_no }}</td>
              <td><span class="badge bg-light text-dark border">{{ $t->fromBranch->name ?? '—' }}</span></td>
              <td><span class="badge bg-light text-success border">➔ {{ $t->toBranch->name ?? '—' }}</span></td>
              <td class="small">{{ $t->transfer_date->format('d M, Y') }}</td>
              <td>
                @foreach($t->items as $item)
                  <div class="small">
                    <strong>{{ $item->product->name ?? 'Product' }}</strong>: {{ (float)$item->quantity_transferred }} টি
                  </div>
                @endforeach
              </td>
              <td>
                <span class="badge bg-success text-white text-uppercase">{{ $t->status }}</span>
              </td>
              <td class="small text-muted">{{ $t->notes ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো স্টক ট্রান্সফার রেকর্ড নেই</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $transfers->links() }}
    </div>
  </div>
</div>
@endsection
