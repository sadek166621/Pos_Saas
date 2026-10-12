@extends('layouts.app')

@section('title', 'কর্মচারী ও সেলস টিম - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">কর্মচারী ও ইউজার অ্যাকাউন্ট (Employees)</h3>
    <p class="text-secondary small mb-0">শাখাভিত্তিক কর্মচারী, বেতন ও কমিশন কাঠামো</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('employees.commissions') }}" class="btn btn-outline-info">
      <i data-lucide="percent" class="icon-sm"></i> কমিশন রিপোর্ট
    </a>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newEmployeeModal">
      <i data-lucide="user-plus" class="icon-sm"></i> নতুন কর্মচারী নিয়োগ
    </button>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="fw-bold mb-1">ইউজার অ্যাকাউন্ট কোটা</h5>
      <p class="text-secondary small mb-0">প্ল্যান লিমিট: {{ $quota['current'] }} / {{ $quota['max'] }} জন ইউজার ব্যবহৃত</p>
    </div>
    <span class="badge bg-primary fs-6">{{ $quota['current'] }} জন সক্রিয়</span>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>নাম</th>
            <th>পদবী</th>
            <th>শাখা</th>
            <th>মোবাইল</th>
            <th>ইমেইল / লগইন আইডি</th>
            <th>মূল বেতন (৳)</th>
            <th>কমিশন রেট</th>
            <th>স্ট্যাটাস</th>
          </tr>
        </thead>
        <tbody>
          @forelse($employees as $e)
            <tr>
              <td class="fw-bold">{{ $e->name }}</td>
              <td><span class="badge bg-light text-dark border">{{ $e->designation }}</span></td>
              <td><span class="badge bg-light text-primary border">{{ $e->branch->name ?? 'All' }}</span></td>
              <td>{{ $e->phone ?: '—' }}</td>
              <td class="font-monospace small">{{ $e->email }}</td>
              <td class="fw-bold">৳{{ number_format($e->salary, 2) }}</td>
              <td class="fw-bold text-success">{{ (float)$e->commission_rate }}%</td>
              <td>
                <span class="badge {{ $e->status ? 'bg-success' : 'bg-danger' }}">
                  {{ $e->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">কোনো কর্মচারী পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $employees->links() }}
    </div>
  </div>
</div>

<!-- NEW EMPLOYEE MODAL -->
<div class="modal fade" id="newEmployeeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">নতুন কর্মচারী নিয়োগ ও লগইন তৈরি</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">পূর্ণ নাম *</label>
            <input type="text" name="name" class="form-control" required placeholder="উদাঃ কাজী তারিকুল">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">মোবাইল নম্বর *</label>
              <input type="text" name="phone" class="form-control" required placeholder="০১৭১১XXXXXX">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">ইমেইল (লগইন ইউজারনেম) *</label>
              <input type="email" name="email" class="form-control" required placeholder="staff@business.com">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">কর্মরত শাখা *</label>
              <select name="branch_id" class="form-select" required>
                @foreach($branches as $b)
                  <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">রোল / পারমিশন *</label>
              <select name="role" class="form-select" required>
                <option value="cashier">ক্যাশিয়ার (Cashier)</option>
                <option value="manager">ম্যানেজার (Manager)</option>
                <option value="salesman">সেলসম্যান (Salesman)</option>
                <option value="accountant">হিসাবরক্ষক (Accountant)</option>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">পদবী (Designation) *</label>
              <input type="text" name="designation" class="form-control" required placeholder="উদাঃ সিনিয়র ক্যাশিয়ার">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">লগইন পাসওয়ার্ড *</label>
              <input type="password" name="password" class="form-control" required placeholder="কমপক্ষে ৬ ডিজিট">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">মাসিক মূল বেতন (৳) *</label>
              <input type="number" step="0.01" name="salary" class="form-control fw-bold" required value="15000">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">বিক্রয়ের ওপর কমিশন (%)</label>
              <input type="number" step="0.1" name="commission_rate" class="form-control" value="0.5">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary fw-bold">নিয়োগ সম্পন্ন করুন ✓</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
