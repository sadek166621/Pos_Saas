@extends('layouts.app')

@section('title', 'কাস্টমার ও CRM - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">কাস্টমার ও বাকি খাতা (Customer CRM)</h3>
    <p class="text-secondary small mb-0">সকল গ্রাহক, লয়্যালটি পয়েন্ট ও বকেয়া হিসেব</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('customers.dues') }}" class="btn btn-outline-danger">
      <i data-lucide="alert-circle" class="icon-sm"></i> বকেয়া তালিকা (Dues)
    </a>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
      <i data-lucide="user-plus" class="icon-sm"></i> নতুন কাস্টমার
    </button>
  </div>
</div>

<!-- Total Customer Due -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">মোট কাস্টমার বকেয়া (Customer Due)</h5>
      <p class="text-secondary small mb-0">বাজারে গ্রাহকদের কাছে পাওনা মোট বাকি টাকা</p>
    </div>
    <h3 class="fw-bold text-danger mb-0">৳{{ number_format($totalCustomerDue, 2) }}</h3>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>কাস্টমারের নাম</th>
            <th>মোবাইল</th>
            <th>ধরন (Type)</th>
            <th>লয়্যালটি পয়েন্ট</th>
            <th>বর্তমান বকেয়া (Due ৳)</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $c)
            <tr>
              <td class="fw-bold">
                <a href="{{ route('customers.show', $c->id) }}">{{ $c->name }}</a>
              </td>
              <td>{{ $c->phone ?: '—' }}</td>
              <td>
                <span class="badge {{ $c->type === 'vip' ? 'bg-primary' : ($c->type === 'wholesale' ? 'bg-info' : 'bg-secondary') }} text-uppercase">
                  {{ $c->type }}
                </span>
              </td>
              <td>
                <span class="badge bg-warning-subtle text-dark">⭐ {{ $c->loyalty_points }} পয়েন্ট</span>
              </td>
              <td class="fw-bold fs-6 {{ $c->current_due > 0 ? 'text-danger' : 'text-success' }}">
                ৳{{ number_format($c->current_due, 2) }}
              </td>
              <td class="text-end">
                <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-outline-primary">লেজার হিস্ট্রি</a>
                @if($c->current_due > 0)
                  <button class="btn btn-sm btn-success" onclick="openCollectDueModal({{ $c->id }}, '{{ $c->name }}', {{ $c->current_due }})">বাকি আদায়</button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">কোনো কাস্টমার পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $customers->links() }}
    </div>
  </div>
</div>

<!-- NEW CUSTOMER MODAL -->
<div class="modal fade" id="newCustomerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন কাস্টমার নিবন্ধন</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">কাস্টমারের নাম *</label>
            <input type="text" name="name" class="form-control" required placeholder="উদাঃ রফিকুল ইসলাম">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">মোবাইল নম্বর</label>
              <input type="text" name="phone" class="form-control" placeholder="০১৭১১XXXXXX">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">ইমেইল</label>
              <input type="email" name="email" class="form-control" placeholder="user@gmail.com">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">কাস্টমার টাইপ</label>
              <select name="type" class="form-select">
                <option value="regular">সাধারণ (Regular)</option>
                <option value="vip">ভিআইপি (VIP)</option>
                <option value="wholesale">পাইকারি (Wholesale)</option>
                <option value="corporate">কর্পোরেট (Corporate)</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">পূর্বের বকেয়া (Previous Due ৳)</label>
              <input type="number" step="0.01" name="previous_due" class="form-control" value="0">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">ঠিকানা</label>
            <input type="text" name="address" class="form-control" placeholder="মিরপুর, ঢাকা">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary fw-bold">সংরক্ষণ করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- COLLECT DUE MODAL -->
<div class="modal fade" id="collectDueModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-success text-white rounded-top-4">
        <h5 class="modal-title fw-bold">কাস্টমার বাকি টাকা আদায়</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="collectDueForm" method="POST">
        @csrf
        <div class="modal-body p-4">
          <p class="small text-secondary mb-2">গ্রাহক: <strong id="collectCustName"></strong></p>
          <div class="mb-3">
            <label class="form-label small fw-bold">আদায়কৃত টাকা (৳) *</label>
            <input type="number" step="0.01" name="amount" id="collectCustAmount" class="form-control fw-bold fs-5" required min="1">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">পেমেন্ট মেথড</label>
            <select name="payment_method" class="form-select">
              <option value="cash">ক্যাশ (Cash)</option>
              <option value="bkash">বিকাশ (bKash)</option>
              <option value="nagad">নগদ (Nagad)</option>
              <option value="bank">ব্যাংক (Bank)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">মন্তব্য / মানি রিসিট নম্বর</label>
            <input type="text" name="notes" class="form-control" placeholder="মানি রিসিট নং বা মন্তব্য">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success fw-bold">বাকি আদায় নিশ্চিত করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openCollectDueModal(id, name, due) {
    document.getElementById('collectDueForm').action = '/customers/' + id + '/collect-due';
    document.getElementById('collectCustName').innerText = name + ' (বর্তমান বকেয়া: ৳' + due + ')';
    document.getElementById('collectCustAmount').value = due;
    new bootstrap.Modal(document.getElementById('collectDueModal')).show();
  }
</script>
@endsection
