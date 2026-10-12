@extends('layouts.app')

@section('title', 'স্টক মুভমেন্ট লগ - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1 fw-bold">স্টক মুভমেন্ট অডিট লগ</h3>
    <p class="text-secondary small mb-0">প্রতিটি পণ্যের ইন, আউট ও অ্যাডজাস্টমেন্টের সম্পূর্ণ ট্রেইল</p>
  </div>
  <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">← ইনভেন্টরি ওভারভিউ</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>তারিখ ও সময়</th>
            <th>পণ্য</th>
            <th>শাখা</th>
            <th>লেনদেনের ধরণ</th>
            <th>পরিমাণ পরিবর্তন</th>
            <th>মন্তব্য</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movements as $m)
            <tr>
              <td class="small text-muted">{{ $m->created_at->format('d M Y, h:i A') }}</td>
              <td class="fw-bold">{{ $m->product->name ?? '—' }}</td>
              <td><span class="badge bg-light text-dark border">{{ $m->branch->name ?? '—' }}</span></td>
              <td>
                <span class="badge {{ str_contains($m->type, 'sale') ? 'bg-danger-subtle text-danger' : (str_contains($m->type, 'purchase') ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info') }} text-uppercase">
                  {{ $m->type }}
                </span>
              </td>
              <td class="fw-bold fs-6 {{ $m->quantity < 0 ? 'text-danger' : 'text-success' }}">
                {{ $m->quantity > 0 ? '+'.$m->quantity : $m->quantity }}
              </td>
              <td class="small text-muted">{{ $m->notes ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">কোনো স্টক মুভমেন্ট লগ পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $movements->links() }}
    </div>
  </div>
</div>
@endsection
