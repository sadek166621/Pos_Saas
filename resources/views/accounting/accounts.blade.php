@extends('layouts.app')

@section('title', 'ক্যাশ ও ব্যাংক অ্যাকাউন্টস - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">ক্যাশ, ব্যাংক ও ওয়ালেট অ্যাকাউন্টস</h3>
    <p class="text-secondary small mb-0">সকল আর্থিক ফান্ড, ড্রয়ার ও বিকাশ মার্চেন্ট হিসাব</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAccountModal">
    <i data-lucide="plus-circle" class="icon-sm"></i> নতুন ফান্ড/অ্যাকাউন্ট যোগ
  </button>
</div>

<!-- Total Liquidity -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">মোট নগদ ও ব্যাংক ব্যালেন্স</h5>
      <p class="text-secondary small mb-0">সকল অ্যাকাউন্ট মিলিয়ে মোট জমা স্থিতি</p>
    </div>
    <h3 class="fw-bold text-success mb-0">৳{{ number_format($totalBalance, 2) }}</h3>
  </div>
</div>

<div class="row g-4">
  @forelse($accounts as $acc)
    <div class="col-md-4">
      <div class="card h-100 border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="badge {{ $acc->type === 'cash' ? 'bg-success' : ($acc->type === 'bank' ? 'bg-primary' : 'bg-danger') }} text-uppercase">
            {{ $acc->type }}
          </span>
          <i data-lucide="credit-card" class="text-secondary icon-md"></i>
        </div>
        <h5 class="fw-bold mb-1">{{ $acc->name }}</h5>
        <p class="text-secondary small mb-3">
          {{ $acc->bank_name ? $acc->bank_name.' | ' : '' }} A/C: {{ $acc->account_number ?: 'Main Drawer' }}
        </p>
        <div class="border-top pt-3">
          <small class="text-secondary">বর্তমান ব্যালেন্স:</small>
          <h3 class="fw-bold text-dark mt-1">৳{{ number_format($acc->balance, 2) }}</h3>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-5 text-muted">কোনো অ্যাকাউন্ট পাওয়া যায়নি</div>
  @endforelse
</div>

<!-- NEW ACCOUNT MODAL -->
<div class="modal fade" id="newAccountModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন ফান্ড / অ্যাকাউন্ট তৈরি</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('accounts.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">অ্যাকাউন্টের নাম *</label>
            <input type="text" name="name" class="form-control" required placeholder="উদাঃ সিটি ব্যাংক কারেন্ট বা বিকাশ মার্চেন্ট">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">অ্যাকাউন্টের ধরণ *</label>
            <select name="type" class="form-select" required>
              <option value="cash">ক্যাশ ড্রয়ার (Cash in Hand)</option>
              <option value="bank">ব্যাংক একাউন্ট (Bank Account)</option>
              <option value="mobile_banking">মোবাইল ব্যাংকিং (bKash/Nagad Merchant)</option>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">অ্যাকাউন্ট নম্বর</label>
              <input type="text" name="account_number" class="form-control" placeholder="১২৩৪৫৬৭৮">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">ব্যাংকের নাম</label>
              <input type="text" name="bank_name" class="form-control" placeholder="সিটি ব্যাংক পিএলসি">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">শুরুর ব্যালেন্স (Opening Balance ৳)</label>
            <input type="number" step="0.01" name="opening_balance" class="form-control" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary fw-bold">অ্যাকাউন্ট তৈরি করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
