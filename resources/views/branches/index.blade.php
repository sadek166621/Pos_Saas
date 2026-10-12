@extends('layouts.app')

@section('title', 'মাল্টি-ব্রাঞ্চ ব্যবস্থাপনা - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">মাল্টি-ব্রাঞ্চ ব্যবস্থাপনা (Multi-Branch System)</h3>
    <p class="text-secondary small mb-0">আপনার ব্যবসার সকল আউটলেট, শোরুম ও ব্রাঞ্চ পরিচালনা করুন</p>
  </div>
  <div>
    @if($quota['allowed'])
      <a href="{{ route('branches.create') }}" class="btn btn-primary">
        <i data-lucide="plus-circle" class="icon-sm"></i> নতুন ব্রাঞ্চ যোগ করুন
      </a>
    @else
      <a href="{{ route('subscription.index') }}" class="btn btn-warning fw-bold">
        ⚡ প্ল্যান আপগ্রেড করুন (লিমিট পূর্ণ: {{ $quota['current'] }}/{{ $quota['max'] }})
      </a>
    @endif
  </div>
</div>

<!-- Plan Limit Meter -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h5 class="fw-bold mb-1">ব্রাঞ্চ কোটা স্থিতি</h5>
        <p class="text-secondary small mb-0">
          আপনার বর্তমান <strong>{{ $tenant->activeSubscription->plan->name ?? 'Default' }}</strong> প্ল্যানে সর্বোচ্চ <strong>{{ $quota['max'] }}</strong> টি ব্রাঞ্চের অনুমোদন রয়েছে। আপনি ইতিমধ্যে <strong>{{ $quota['current'] }}</strong> টি ব্রাঞ্চ সক্রিয় করেছেন।
        </p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="fs-4 fw-bold {{ $quota['current'] >= $quota['max'] ? 'text-danger' : 'text-primary' }}">
          {{ $quota['current'] }} / {{ $quota['max'] }}
        </span>
        <div class="progress mt-2" style="height: 8px;">
          <div class="progress-bar {{ $quota['current'] >= $quota['max'] ? 'bg-danger' : 'bg-primary' }}" style="width: {{ min(100, ($quota['current'] / max(1, $quota['max'])) * 100) }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Branches Grid -->
<div class="row g-4">
  @foreach($branches as $b)
    <div class="col-lg-4 col-md-6">
      <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-light text-dark border font-monospace">{{ $b->code }}</span>
              @if($b->is_main)
                <span class="badge bg-primary">প্রধান শাখা (HQ)</span>
              @else
                <span class="badge bg-success">সক্রিয় শাখা</span>
              @endif
            </div>
            <h4 class="fw-bold mb-2">{{ $b->name }}</h4>
            <p class="text-secondary small mb-3">📍 {{ $b->address }}, {{ $b->city }}</p>

            <ul class="list-unstyled small border-top pt-2 text-secondary">
              <li class="py-1">📞 ফোন: <strong>{{ $b->phone ?: '—' }}</strong></li>
              <li class="py-1">👤 ব্রাঞ্চ ম্যানেজার: <strong>{{ $b->manager->name ?? 'নিযুক্ত নেই' }}</strong></li>
              <li class="py-1">🏬 সংযুক্ত ওয়্যারহাউস: <strong>{{ $b->warehouses->count() }} টি</strong></li>
              <li class="py-1">👥 কর্মরত কর্মচারী: <strong>{{ $b->users->count() }} জন</strong></li>
            </ul>
          </div>

          <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('branches.edit', $b->id) }}" class="btn btn-sm btn-outline-secondary">এডিট করুন</a>
            <form action="{{ route('switch-branch') }}" method="POST">
              @csrf
              <input type="hidden" name="branch_id" value="{{ $b->id }}">
              <button type="submit" class="btn btn-sm btn-outline-primary">এই শাখায় প্রবেশ</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
