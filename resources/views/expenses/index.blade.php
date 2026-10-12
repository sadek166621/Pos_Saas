@extends('layouts.app')

@section('title', 'দৈনিক খরচ (Expense) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">দৈনিক খরচ ও ক্যাটাগরি (Expenses)</h3>
    <p class="text-secondary small mb-0">দোকান ভাড়া, বিদ্যুৎ, যাতায়াত ও অন্যান্য খরচের হিসাব</p>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
      + খরচের ক্যাটাগরি
    </button>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newExpenseModal">
      <i data-lucide="plus-circle" class="icon-sm"></i> নতুন খরচ এন্ট্রি
    </button>
  </div>
</div>

<!-- Total Expense Card -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">মোট রেকর্ডকৃত খরচ</h5>
      <p class="text-secondary small mb-0">সকল ক্যাটাগরি মিলিয়ে মোট ব্যয়</p>
    </div>
    <h3 class="fw-bold text-danger mb-0">৳{{ number_format($totalExpense, 2) }}</h3>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ভাউচার #</th>
            <th>খরচের খাত / শিরোনাম</th>
            <th>ক্যাটাগরি</th>
            <th>শাখা</th>
            <th>তারিখ</th>
            <th>পেমেন্ট মেথড</th>
            <th>টাকার পরিমাণ (৳)</th>
            <th>মন্তব্য</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $exp)
            <tr>
              <td class="font-monospace fw-bold text-secondary">{{ $exp->expense_no }}</td>
              <td class="fw-bold">{{ $exp->title }}</td>
              <td><span class="badge bg-light text-dark border">{{ $exp->category->name ?? '—' }}</span></td>
              <td><span class="badge bg-light text-primary border">{{ $exp->branch->name ?? '—' }}</span></td>
              <td class="small">{{ $exp->date->format('d M, Y') }}</td>
              <td><span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ $exp->payment_method }}</span></td>
              <td class="fw-bold text-danger fs-6">৳{{ number_format($exp->amount, 2) }}</td>
              <td class="small text-muted">{{ $exp->notes ?: '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">কোনো খরচের রেকর্ড নেই</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $expenses->links() }}
    </div>
  </div>
</div>

<!-- NEW EXPENSE MODAL -->
<div class="modal fade" id="newExpenseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন খরচ এন্ট্রি</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('expenses.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">খরচের শিরোনাম / বিবরণ *</label>
            <input type="text" name="title" class="form-control" required placeholder="উদাঃ এপ্রিল মাসের বিদ্যুৎ বিল">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">ক্যাটাগরি *</label>
              <select name="category_id" class="form-select" required>
                @foreach($categories as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">টাকার পরিমাণ (৳) *</label>
              <input type="number" step="0.01" name="amount" class="form-control fw-bold" required min="1">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">পেমেন্ট মেথড</label>
              <select name="payment_method" class="form-select">
                <option value="cash">ক্যাশ (ড্রয়ার থেকে)</option>
                <option value="bank">ব্যাংক</option>
                <option value="bkash">বিকাশ</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">তারিখ</label>
              <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">মন্তব্য</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="অতিরিক্ত বিবরণ..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary fw-bold">খরচ সংরক্ষণ করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- NEW CATEGORY MODAL -->
<div class="modal fade" id="newCategoryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-secondary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন খরচের ক্যাটাগরি</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('expenses.categories.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">ক্যাটাগরির নাম *</label>
            <input type="text" name="name" class="form-control" required placeholder="উদাঃ দোকান ভাড়া, চা-নাস্তা, যাতায়াত">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">বিবরণ</label>
            <input type="text" name="description" class="form-control" placeholder="সংক্ষিপ্ত বিবরণ">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-secondary fw-bold">ক্যাটাগরি তৈরি করুন</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
