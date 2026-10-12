@extends('layouts.admin')

@section('title', 'প্যাকেজ ও মূল্য নির্ধারণ - Super Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">সাবস্ক্রিপশন প্যাকেজ ও লিমিট কনফিগারেশন</h3>
    <p class="text-secondary small mb-0">কোড পরিবর্তন ছাড়াই যে কোনো প্ল্যানের মূল্য, ব্রাঞ্চ সংখ্যা, ইউজার লিমিট এবং ফিচার আপডেট করুন</p>
  </div>
</div>

<div class="row g-4">
  @foreach($plans as $p)
    <div class="col-lg-4">
      <div class="card bg-white stat-card h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h4 class="fw-bold text-dark mb-0">{{ $p->name }}</h4>
          <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $p->is_active ? 'সক্রিয়' : 'বন্ধ' }}
          </span>
        </div>
        <div class="my-3">
          <h2 class="fw-bold text-primary mb-0">৳{{ number_format($p->price_monthly) }}<span class="fs-6 text-muted">/মাস</span></h2>
          <small class="text-muted">বাৎসরিক: ৳{{ number_format($p->price_yearly) }}/বছর</small>
        </div>
        <hr>
        <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
          <li class="d-flex justify-content-between">
            <span class="text-secondary">শাখা লিমিট (Branches):</span>
            <strong class="text-dark">{{ $p->max_branches }} টি</strong>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-secondary">ইউজার লিমিট:</span>
            <strong class="text-dark">{{ $p->max_users }} জন</strong>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-secondary">প্রোডাক্ট লিমিট:</span>
            <strong class="text-dark">{{ number_format($p->max_products) }} টি</strong>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-secondary">মাসিক ফ্রি এসএমএস:</span>
            <strong class="text-dark">{{ number_format($p->sms_limit) }} টি</strong>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-secondary">গ্রাহক সংখ্যা:</span>
            <span class="badge bg-light text-primary border">{{ $p->subscriptions_count }} টি সাবস্ক্রিপশন</span>
          </li>
        </ul>

        <button class="btn btn-outline-primary mt-auto w-100" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $p->id }}">
          <i data-lucide="edit-3" class="icon-sm"></i> প্যাকেজ এডিট করুন
        </button>
      </div>
    </div>

    <!-- Edit Plan Modal -->
    <div class="modal fade" id="editPlanModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">প্ল্যান পরিবর্তন: {{ $p->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('admin.plans.update', $p->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body p-4">
              <div class="mb-3">
                <label class="form-label small fw-semibold">প্ল্যানের নাম</label>
                <input type="text" name="name" class="form-control" value="{{ $p->name }}" required>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">মাসিক ফি (৳)</label>
                  <input type="number" name="price_monthly" class="form-control" value="{{ (int)$p->price_monthly }}" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">বাৎসরিক ফি (৳)</label>
                  <input type="number" name="price_yearly" class="form-control" value="{{ (int)$p->price_yearly }}" required>
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">সর্বোচ্চ শাখা (Branches)</label>
                  <input type="number" name="max_branches" class="form-control" value="{{ $p->max_branches }}" required min="1">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">সর্বোচ্চ ইউজার</label>
                  <input type="number" name="max_users" class="form-control" value="{{ $p->max_users }}" required min="1">
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">সর্বোচ্চ প্রোডাক্ট</label>
                  <input type="number" name="max_products" class="form-control" value="{{ $p->max_products }}" required min="100">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">মাসিক ফ্রি SMS</label>
                  <input type="number" name="sms_limit" class="form-control" value="{{ $p->sms_limit }}" required min="0">
                </div>
              </div>

              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activePlan{{ $p->id }}" {{ $p->is_active ? 'checked' : '' }}>
                <label class="form-check-label small" for="activePlan{{ $p->id }}">প্যাকেজটি সক্রিয় রাখুন</label>
              </div>
            </div>
            <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
              <button type="submit" class="btn btn-primary px-4">সংরক্ষণ করুন</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
