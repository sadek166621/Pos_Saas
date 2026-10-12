@extends('layouts.app')

@section('title', 'সেলস কমিশন রিপোর্ট - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">কর্মচারী সেলস কমিশন (Sales Commission)</h3>
    <p class="text-secondary small mb-0">চলতি মাসের বিক্রয় লক্ষ্যমাত্রা এবং অর্জিত সেলস কমিশন পর্যালোচনা</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" class="icon-sm"></i> কর্মচারী তালিকা
    </a>
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i data-lucide="printer" class="icon-sm"></i> প্রিন্ট রিপোর্ট
    </button>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">চলতি মাসের মোট কমিশন প্রদেয়</span>
      <h3 class="fw-bold text-success mt-1 mb-0">৳{{ number_format(collect($commissionData)->sum('commission_earned'), 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">মোট সেলস টিম বিক্রয়</span>
      <h3 class="fw-bold text-primary mt-1 mb-0">৳{{ number_format(collect($commissionData)->sum('monthly_sales'), 2) }}</h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3">
      <span class="text-secondary small fw-semibold">মোট প্রদেয় বেতন + কমিশন</span>
      <h3 class="fw-bold text-dark mt-1 mb-0">৳{{ number_format(collect($commissionData)->sum('total_payable'), 2) }}</h3>
    </div>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">চলতি মাসের কমিশন স্টেটমেন্ট ({{ \Carbon\Carbon::now()->format('F Y') }})</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>কর্মচারীর নাম</th>
            <th>পদবী ও শাখা</th>
            <th>মূল বেতন</th>
            <th>চলতি মাসে বিক্রয় (৳)</th>
            <th>কমিশন শতকরা</th>
            <th>অর্জিত কমিশন (৳)</th>
            <th>সর্বমোট প্রদেয় (বেতন + কমিশন)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($commissionData as $row)
            <tr>
              <td class="fw-bold">{{ $row['employee']->name }}</td>
              <td>
                <span class="badge bg-light text-dark border">{{ $row['employee']->designation }}</span>
                <span class="badge bg-light text-primary border">{{ $row['employee']->branch->name ?? 'All' }}</span>
              </td>
              <td>৳{{ number_format($row['employee']->salary, 2) }}</td>
              <td class="fw-bold text-primary">৳{{ number_format($row['monthly_sales'], 2) }}</td>
              <td><span class="badge bg-success-subtle text-success">{{ (float)$row['rate'] }}%</span></td>
              <td class="fw-bold text-success">৳{{ number_format($row['commission_earned'], 2) }}</td>
              <td class="fw-bold text-dark fs-6">৳{{ number_format($row['total_payable'], 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">কোনো কর্মচারীর তথ্য পাওয়া যায়নি।</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
