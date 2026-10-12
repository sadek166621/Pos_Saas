@extends('layouts.app')

@section('title', 'কাস্টমার বকেয়া খাতা (Due Management) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">কাস্টমার বকেয়া খাতা ও বাকি তাগাদা</h3>
    <p class="text-secondary small mb-0">বাজারে বকেয়া থাকা সকল গ্রাহকদের তালিকা ও SMS নোটিফিকেশন</p>
  </div>
  <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">← সকল কাস্টমার</a>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">মোট অনাদায়ী বকেয়া (Total Outstanding)</h5>
      <p class="text-secondary small mb-0">বর্তমানে গ্রাহকদের কাছে পাওনা মোট বাকি টাকা</p>
    </div>
    <h3 class="fw-bold text-danger mb-0">৳{{ number_format($totalDue, 2) }}</h3>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>কাস্টমারের নাম</th>
            <th>মোবাইল নম্বর</th>
            <th>ধরন</th>
            <th>ঠিকানা</th>
            <th>বকেয়া টাকা (Due ৳)</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customersWithDue as $c)
            <tr>
              <td class="fw-bold">
                <a href="{{ route('customers.show', $c->id) }}">{{ $c->name }}</a>
              </td>
              <td class="font-monospace">{{ $c->phone ?: '—' }}</td>
              <td><span class="badge bg-secondary-subtle text-secondary">{{ $c->type }}</span></td>
              <td class="small text-muted">{{ $c->address ?: $c->city }}</td>
              <td class="fw-bold fs-6 text-danger">৳{{ number_format($c->current_due, 2) }}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="alert('SMS সফলভাবে পাঠানো হয়েছে: প্রিয় {{ $c->name }}, {{ $currentTenant->name }}-এ আপনার বকেয়া ৳{{ number_format($c->current_due) }}। অনুগ্রহ করে সময়মতো পরিশোধ করুন।');">
                  📱 SMS রিমাইন্ডার
                </button>
                <button class="btn btn-sm btn-success" onclick="openCollectDueModal({{ $c->id }}, '{{ $c->name }}', {{ $c->current_due }})">
                  বাকি আদায়
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-success fw-bold">✓ কোনো কাস্টমার বকেয়া নেই! সব হিসাব পরিশোধিত।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $customersWithDue->links() }}
    </div>
  </div>
</div>

<!-- DUE MODAL INCLUDED -->
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
