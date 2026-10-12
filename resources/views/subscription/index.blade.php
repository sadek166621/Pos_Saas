@extends('layouts.app')

@section('title', 'সাবস্ক্রিপশন ও বিলিং - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">সাবস্ক্রিপশন ও বিলিং (Subscription & Billing)</h3>
    <p class="text-secondary small mb-0">আপনার ব্যবসার প্যাকেজ, কোটা ব্যবহার, নবায়ন ও ইনভয়েস হিস্ট্রি</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#renewModal">
      <i data-lucide="refresh-cw" class="icon-sm"></i> প্ল্যান রিনিউ বা পরিবর্তন করুন
    </button>
  </div>
</div>

<!-- Subscription Status Banner -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <div class="row align-items-center">
      <div class="col-md-7">
        <div class="d-flex align-items-center gap-3 mb-2">
          <h4 class="fw-bold mb-0">বর্তমান প্ল্যান: {{ $plan->name ?? 'Standard' }}</h4>
          <span class="badge {{ ($subscription && $subscription->status === 'active') ? 'bg-success' : 'bg-warning text-dark' }} fs-6">
            {{ strtoupper($subscription->status ?? 'TRIAL') }}
          </span>
        </div>
        <p class="text-secondary mb-1">
          বিলিং সাইকেল: <strong>{{ ucfirst($subscription->billing_cycle ?? 'monthly') }}</strong> | 
          পরবর্তী নবায়ন তারিখ: 
          <strong class="text-primary">
            {{ $subscription && $subscription->current_period_ends_at ? $subscription->current_period_ends_at->format('d M, Y') : 'N/A' }}
          </strong>
        </p>
        @if($subscription && $subscription->status === 'grace_period')
          <div class="alert alert-danger py-2 mt-2 mb-0">
            <i data-lucide="alert-triangle" class="icon-sm me-1"></i> আপনার অ্যাকাউন্টে গ্রেস পিরিয়ড চলছে। দয়া করে এখনই নবায়ন করুন।
          </div>
        @endif
      </div>
      <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <span class="text-muted d-block small">মাসিক ফি</span>
        <h2 class="fw-bold text-dark mb-0">৳{{ number_format($plan->price_monthly ?? 0) }}<span class="fs-6 text-muted">/মাস</span></h2>
      </div>
    </div>
  </div>
</div>

<!-- Quota Usage Progress -->
<div class="row g-3 mb-4">
  <!-- Users -->
  <div class="col-md-3">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-semibold text-secondary small">ইউজার লিমিট</span>
        <span class="badge bg-light text-dark">{{ $userCount }} / {{ $plan->max_users }}</span>
      </div>
      @php $userPct = min(100, round(($userCount / max(1, $plan->max_users)) * 100)); @endphp
      <div class="progress" style="height: 8px;">
        <div class="progress-bar {{ $userPct >= 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $userPct }}%"></div>
      </div>
      <span class="text-muted small mt-1 d-block">{{ $userPct }}% ব্যবহৃত</span>
    </div>
  </div>

  <!-- Branches -->
  <div class="col-md-3">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-semibold text-secondary small">শাখা লিমিট (Branches)</span>
        <span class="badge bg-light text-dark">{{ $branchCount }} / {{ $plan->max_branches }}</span>
      </div>
      @php $branchPct = min(100, round(($branchCount / max(1, $plan->max_branches)) * 100)); @endphp
      <div class="progress" style="height: 8px;">
        <div class="progress-bar {{ $branchPct >= 90 ? 'bg-danger' : 'bg-info' }}" style="width: {{ $branchPct }}%"></div>
      </div>
      <span class="text-muted small mt-1 d-block">{{ $branchPct }}% ব্যবহৃত</span>
    </div>
  </div>

  <!-- Products -->
  <div class="col-md-3">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-semibold text-secondary small">প্রোডাক্ট সংখ্যা</span>
        <span class="badge bg-light text-dark">{{ $productCount }} / {{ $plan->max_products }}</span>
      </div>
      @php $prodPct = min(100, round(($productCount / max(1, $plan->max_products)) * 100)); @endphp
      <div class="progress" style="height: 8px;">
        <div class="progress-bar {{ $prodPct >= 90 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $prodPct }}%"></div>
      </div>
      <span class="text-muted small mt-1 d-block">{{ $prodPct }}% ব্যবহৃত</span>
    </div>
  </div>

  <!-- Warehouses -->
  <div class="col-md-3">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-semibold text-secondary small">ওয়্যারহাউজ লিমিট</span>
        <span class="badge bg-light text-dark">{{ $warehouseCount }} / {{ $plan->max_warehouses }}</span>
      </div>
      @php $whPct = min(100, round(($warehouseCount / max(1, $plan->max_warehouses)) * 100)); @endphp
      <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-warning" style="width: {{ $whPct }}%"></div>
      </div>
      <span class="text-muted small mt-1 d-block">{{ $whPct }}% ব্যবহৃত</span>
    </div>
  </div>
</div>

<!-- Plan Options -->
<h4 class="fw-bold mb-3">প্যাকেজসমূহ (Available Subscription Plans)</h4>
<div class="row g-4 mb-4">
  @foreach($plans as $p)
    <div class="col-md-4">
      <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-4 {{ ($plan->id ?? 0) === $p->id ? 'border border-2 border-primary' : '' }}">
        @if(($plan->id ?? 0) === $p->id)
          <div class="badge bg-primary position-absolute top-0 end-0 m-3">বর্তমান প্যাকেজ</div>
        @endif
        <h4 class="fw-bold text-dark">{{ $p->name }}</h4>
        <p class="text-secondary small">{{ $p->description }}</p>
        <div class="my-3">
          <h2 class="fw-bold text-primary mb-0">৳{{ number_format($p->price_monthly) }}<span class="fs-6 text-muted">/মাস</span></h2>
          <small class="text-muted">বাৎসরিক: ৳{{ number_format($p->price_yearly) }}/বছর</small>
        </div>
        <hr>
        <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> <strong>{{ $p->max_branches }}</strong> টি আউটলেট / শাখা</li>
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> <strong>{{ $p->max_users }}</strong> জন ইউজার ও কর্মচারী</li>
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> <strong>{{ number_format($p->max_products) }}</strong> টি প্রোডাক্ট আপলোড</li>
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> <strong>{{ $p->max_warehouses }}</strong> টি ওয়্যারহাউজ গোডাউন</li>
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> <strong>{{ number_format($p->sms_limit) }}</strong> টি ফ্রি এসএমএস</li>
          <li><i data-lucide="check" class="icon-sm text-success me-2"></i> অ্যাডভান্সড POS ও অফলাইন সিঙ্ক</li>
        </ul>
        <button class="btn {{ ($plan->id ?? 0) === $p->id ? 'btn-outline-primary' : 'btn-primary' }} mt-auto"
                data-bs-toggle="modal" data-bs-target="#renewModal"
                onclick="document.getElementById('planSelect').value = '{{ $p->id }}';">
          {{ ($plan->id ?? 0) === $p->id ? 'রিনিউ করুন' : 'এই প্যাকেজে আপগ্রেড করুন' }}
        </button>
      </div>
    </div>
  @endforeach
</div>

<!-- Addons -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">এড-অন সার্ভিসেস (Add-ons & Extra Quota)</h5>
    <div class="row g-3">
      @foreach($addons as $ad)
        <div class="col-md-3">
          <div class="border rounded-4 p-3 bg-light">
            <h6 class="fw-bold mb-1">{{ $ad->name }}</h6>
            <p class="text-secondary small mb-2">{{ $ad->description }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-bold text-success">৳{{ number_format($ad->price_monthly) }}/মাস</span>
              <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#renewModal">যুক্ত করুন</button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Billing Invoices History -->
<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">সাবস্ক্রিপশন ইনভয়েস হিস্ট্রি (Invoice History)</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ইনভয়েস #</th>
            <th>প্ল্যান</th>
            <th>সাইকেল</th>
            <th>পরিমাণ (৳)</th>
            <th>স্ট্যাটাস</th>
            <th>বিলিং পিরিয়ড</th>
            <th>পরিশোধের তারিখ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td class="fw-bold">{{ $inv->invoice_number }}</td>
              <td>{{ $inv->plan->name ?? 'Standard' }}</td>
              <td><span class="badge bg-light text-dark">{{ ucfirst($inv->billing_cycle) }}</span></td>
              <td class="fw-bold text-primary">৳{{ number_format($inv->total, 2) }}</td>
              <td>
                <span class="badge {{ $inv->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                  {{ strtoupper($inv->status) }}
                </span>
              </td>
              <td class="small">{{ $inv->period_starts_at ? $inv->period_starts_at->format('d M, Y') : '' }} - {{ $inv->period_ends_at ? $inv->period_ends_at->format('d M, Y') : '' }}</td>
              <td>{{ $inv->paid_at ? $inv->paid_at->format('d M, Y h:i A') : '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো সাবস্ক্রিপশন ইনভয়েস পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Renew / Upgrade Modal -->
<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">সাবস্ক্রিপশন নবায়ন / আপগ্রেড</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('subscription.renew') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">প্যাকেজ নির্বাচন করুন</label>
            <select name="plan_id" id="planSelect" class="form-select" required>
              @foreach($plans as $p)
                <option value="{{ $p->id }}" {{ ($plan->id ?? 0) === $p->id ? 'selected' : '' }}>
                  {{ $p->name }} (মাসিক ৳{{ number_format($p->price_monthly) }} / বাৎসরিক ৳{{ number_format($p->price_yearly) }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">বিলিং সাইকেল</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="billing_cycle" id="cycleMonthly" value="monthly" checked>
                <label class="form-check-label" for="cycleMonthly">মাসিক (Monthly)</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="billing_cycle" id="cycleYearly" value="yearly">
                <label class="form-check-label" for="cycleYearly">বাৎসরিক (Yearly - ২০% ডিসকাউন্ট)</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">পেমেন্ট মেথড নির্বাচন করুন</label>
            <div class="row g-2">
              <div class="col-6">
                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer w-100">
                  <input type="radio" name="gateway" value="bkash" checked>
                  <span class="fw-bold text-danger">bKash</span>
                </label>
              </div>
              <div class="col-6">
                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer w-100">
                  <input type="radio" name="gateway" value="nagad">
                  <span class="fw-bold text-warning">Nagad</span>
                </label>
              </div>
              <div class="col-6">
                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer w-100">
                  <input type="radio" name="gateway" value="rocket">
                  <span class="fw-bold text-primary">Rocket</span>
                </label>
              </div>
              <div class="col-6">
                <label class="border rounded-3 p-2 d-flex align-items-center gap-2 cursor-pointer w-100">
                  <input type="radio" name="gateway" value="sslcommerz">
                  <span class="fw-bold text-success">Cards / Net Banking</span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
          <button type="submit" class="btn btn-primary px-4">পেমেন্ট সম্পন্ন করুন</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
