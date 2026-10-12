@extends('layouts.app')

@section('title', 'সাপ্লায়ার ও বকেয়া ব্যবস্থাপনা - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">সাপ্লায়ার ও মহাজন খাতা</h3>
    <p class="text-secondary small mb-0">সকল সাপ্লায়ার, বকেয়া পাওনা ও পেমেন্ট হিস্ট্রি</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSupplierModal">
    <i data-lucide="plus-circle" class="icon-sm"></i> নতুন সাপ্লায়ার যোগ
  </button>
</div>

<!-- Total Supplier Due Card -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">মোট সাপ্লায়ার বকেয়া পাওনা</h5>
      <p class="text-secondary small mb-0">সকল সাপ্লায়ারদের কাছে আপনার মোট বাকি</p>
    </div>
    <h3 class="fw-bold text-danger mb-0">৳{{ number_format($totalSupplierDue, 2) }}</h3>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>সাপ্লায়ারের নাম</th>
            <th>কোম্পানি</th>
            <th>মোবাইল</th>
            <th>শহর / ঠিকানা</th>
            <th>বর্তমান বকেয়া (Due)</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suppliers as $s)
            <tr>
              <td class="fw-bold">
                <a href="{{ route('suppliers.show', $s->id) }}">{{ $s->name }}</a>
              </td>
              <td>{{ $s->company_name ?: '—' }}</td>
              <td>{{ $s->phone }}</td>
              <td class="small text-muted">{{ $s->address ?: $s->city }}</td>
              <td class="fw-bold fs-6 {{ $s->current_due > 0 ? 'text-danger' : 'text-success' }}">
                ৳{{ number_format($s->current_due, 2) }}
              </td>
              <td class="text-end">
                <a href="{{ route('suppliers.show', $s->id) }}" class="btn btn-sm btn-outline-primary">লেজার খাতা</a>
                @if($s->current_due > 0)
                  <button class="btn btn-sm btn-success" onclick="openPaySupplierModal({{ $s->id }}, '{{ $s->name }}', {{ $s->current_due }})">বকেয়া পরিশোধ</button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">কোনো সাপ্লায়ার নেই</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $suppliers->links() }}
    </div>
  </div>
</div>

<!-- NEW SUPPLIER MODAL -->
<div class="modal fade" id="newSupplierModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন সাপ্লায়ার যুক্ত করুন</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">সাপ্লায়ারের নাম *</label>
            <input type="text" name="name" class="form-control" required placeholder="উদাঃ টেক্সটাইল মিলস বিডি">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">কোম্পানি নাম</label>
              <input type="text" name="company_name" class="form-control" placeholder="কোম্পানির নাম">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">মোবাইল নম্বর *</label>
              <input type="text" name="phone" class="form-control" required placeholder="০১৭১১XXXXXX">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label small fw-bold">ঠিকানা</label>
              <input type="text" name="address" class="form-control" placeholder="ঠিকানা">
            </div>
            <div class="col-4">
              <label class="form-label small fw-bold">শহর</label>
              <input type="text" name="city" class="form-control" placeholder="ঢাকা">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">পূর্বের বকেয়া (Previous Due ৳)</label>
            <input type="number" step="0.01" name="previous_due" class="form-control" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary fw-bold">সংরক্ষণ করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PAY SUPPLIER MODAL -->
<div class="modal fade" id="paySupplierModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-success text-white rounded-top-4">
        <h5 class="modal-title fw-bold">সাপ্লায়ার বকেয়া পরিশোধ</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="paySupplierForm" method="POST">
        @csrf
        <div class="modal-body p-4">
          <p class="small text-secondary mb-2">সাপ্লায়ার: <strong id="paySupName"></strong></p>
          <div class="mb-3">
            <label class="form-label small fw-bold">পরিশোধের পরিমাণ (৳) *</label>
            <input type="number" step="0.01" name="amount" id="paySupAmount" class="form-control fw-bold fs-5" required min="1">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">পেমেন্ট মেথড</label>
            <select name="payment_method" class="form-select">
              <option value="cash">ক্যাশ (Cash)</option>
              <option value="bank">ব্যাংক (Bank Transfer)</option>
              <option value="bkash">বিকাশ (bKash)</option>
              <option value="nagad">নগদ (Nagad)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">নোট</label>
            <input type="text" name="notes" class="form-control" placeholder="পেমেন্ট স্লিপ নম্বর বা মন্তব্য">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success fw-bold">পরিশোধ নিশ্চিত করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openPaySupplierModal(id, name, due) {
    document.getElementById('paySupplierForm').action = '/suppliers/' + id + '/pay';
    document.getElementById('paySupName').innerText = name + ' (বর্তমান বকেয়া: ৳' + due + ')';
    document.getElementById('paySupAmount').value = due;
    new bootstrap.Modal(document.getElementById('paySupplierModal')).show();
  }
</script>
@endsection
