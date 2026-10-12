@extends('layouts.app')

@section('title', 'লাভ-ক্ষতি বিবরণী (P&L) - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">লাভ-ক্ষতি বিবরণী (Profit & Loss Statement)</h3>
    <p class="text-secondary small mb-0">বিক্রি, ক্রয় খরচ ও দোকান ব্যয়ের ওপর ভিত্তি করে নিট লাভ</p>
  </div>
  <div>
    <form method="GET" action="{{ route('accounting.profit-loss') }}" class="d-flex gap-2">
      <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
      <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
      <button type="submit" class="btn btn-sm btn-primary">ফিল্টার</button>
    </form>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <small class="text-secondary">মোট বিক্রয় রাজস্ব (Revenue)</small>
      <h3 class="fw-bold text-primary my-2">৳{{ number_format($totalSales, 2) }}</h3>
      <span class="small text-muted">নির্দিষ্ট সময়ের বিক্রয়</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <small class="text-secondary">মোট মোট লাভ (Gross Profit)</small>
      <h3 class="fw-bold text-success my-2">৳{{ number_format($grossProfit, 2) }}</h3>
      <span class="small text-muted">বিক্রিত পণ্যের ক্রয়মূল্য (COGS) বাদে</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
      <small class="text-secondary">প্রকৃত নিট লাভ (Net Profit)</small>
      <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} my-2">
        ৳{{ number_format($netProfit, 2) }}
      </h3>
      <span class="small text-muted">দোকানের সকল খরচ বাদ দিয়ে</span>
    </div>
  </div>
</div>

<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <h5 class="fw-bold mb-3">আর্থিক হিসাব বিবরণী বিস্তারিত</h5>
    <table class="table table-bordered align-middle">
      <tbody>
        <tr class="table-light fw-bold">
          <td>১. মোট বিক্রয় আয় (Total Sales Revenue)</td>
          <td class="text-end text-primary fs-6">৳{{ number_format($totalSales, 2) }}</td>
        </tr>
        <tr>
          <td>- বিক্রিত পণ্যের ক্রয়মূল্য (Cost of Goods Sold - COGS)</td>
          <td class="text-end text-danger">৳{{ number_format($totalCogs, 2) }}</td>
        </tr>
        <tr class="fw-bold">
          <td>= মোট মুনাফা (Gross Profit)</td>
          <td class="text-end text-success fs-6">৳{{ number_format($grossProfit, 2) }}</td>
        </tr>
        <tr class="table-light fw-bold">
          <td>২. পরিচালন খরচসমূহ (Operating Expenses)</td>
          <td class="text-end text-danger">৳{{ number_format($totalExpenses, 2) }}</td>
        </tr>
        @foreach($expenseBreakdown as $eb)
          <tr>
            <td class="ps-4 text-secondary">• {{ $eb->category->name ?? 'অন্যান্য খরচ' }}</td>
            <td class="text-end text-muted">৳{{ number_format($eb->cat_total, 2) }}</td>
          </tr>
        @endforeach
        <tr class="table-dark fw-bold fs-5">
          <td>৩. চূড়ান্ত নিট লাভ / ক্ষতি (Net Profit / Loss)</td>
          <td class="text-end {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
            ৳{{ number_format($netProfit, 2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
