@extends('layouts.app')

@section('title', 'সেন্ট্রাল ড্যাশবোর্ড - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h3 class="mb-1 fw-bold">সেন্ট্রাল বিজনেস ড্যাশবোর্ড</h3>
    <p class="text-secondary small mb-0">
      শাখা ফিল্টার: 
      @if($isAllBranches)
        <span class="badge bg-primary">সকল শাখা (All Branches)</span>
      @else
        <span class="badge bg-success">{{ $activeBranch->name ?? 'Current Branch' }}</span>
      @endif
      — আজকের তারিখ: {{ \Carbon\Carbon::now('Asia/Dhaka')->translatedFormat('d F Y') }}
    </p>
  </div>
  <div class="d-flex align-items-center flex-wrap text-nowrap gap-2 mt-2 mt-sm-0">
    <a href="{{ route('pos.index') }}" class="btn btn-success pos-quick-btn d-flex align-items-center gap-1 shadow-sm px-3">
      <i data-lucide="shopping-cart" class="icon-sm"></i> দ্রুত POS চালু করুন
    </a>
    <a href="{{ route('products.create') }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
      <i data-lucide="plus-circle" class="icon-sm"></i> নতুন পণ্য
    </a>
  </div>
</div>

<!-- STATS CARDS ROW 1 -->
<div class="row g-3 mb-4">
  <!-- Today Sales -->
  <div class="col-xl-3 col-md-6">
    <div class="card stat-card bg-white h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-secondary small fw-semibold text-uppercase mb-1">আজকের মোট বিক্রি</p>
            <h3 class="fw-bold text-primary mb-1">৳{{ number_format($todaySales, 2) }}</h3>
            <span class="small text-muted">নগদ সংগ্রহ: ৳{{ number_format($todayCashCollected, 2) }}</span>
          </div>
          <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
            <i data-lucide="trending-up" class="icon-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Today Profit -->
  <div class="col-xl-3 col-md-6">
    <div class="card stat-card bg-white h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-secondary small fw-semibold text-uppercase mb-1">আজকের নিট লাভ (Profit)</p>
            <h3 class="fw-bold text-success mb-1">৳{{ number_format($todayProfit, 2) }}</h3>
            <span class="small text-muted">ক্রয়মূল্য ও খরচ বাদে</span>
          </div>
          <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
            <i data-lucide="dollar-sign" class="icon-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Today Expense -->
  <div class="col-xl-3 col-md-6">
    <div class="card stat-card bg-white h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-secondary small fw-semibold text-uppercase mb-1">আজকের মোট খরচ</p>
            <h3 class="fw-bold text-danger mb-1">৳{{ number_format($todayExpenses, 2) }}</h3>
            <span class="small text-muted">দোকান ও অপারেশনাল খরচ</span>
          </div>
          <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3">
            <i data-lucide="file-minus" class="icon-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Customer Due -->
  <div class="col-xl-3 col-md-6">
    <div class="card stat-card bg-white h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-secondary small fw-semibold text-uppercase mb-1">কাস্টমার মোট বাকি (Due)</p>
            <h3 class="fw-bold text-warning mb-1">৳{{ number_format($totalCustomerDue, 2) }}</h3>
            <span class="small text-muted">সাপ্লায়ার পাওনা: ৳{{ number_format($totalSupplierDue, 2) }}</span>
          </div>
          <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
            <i data-lucide="alert-circle" class="icon-lg"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CHARTS & OVERVIEWS -->
<div class="row g-3 mb-4">
  <!-- 7-Day Sales Trend -->
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">গত ৭ দিনের বিক্রয় প্রবাহ (Sales Trend)</h5>
          <span class="badge bg-light text-secondary border">বিডিটি (৳)</span>
        </div>
        <div id="salesChart" style="min-height: 280px;"></div>
      </div>
    </div>
  </div>

  <!-- Quick Inventory & Highlights -->
  <div class="col-lg-4">
    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4">
        <h5 class="fw-bold mb-3">স্টক ও ইনভেন্টরি অ্যালার্ট</h5>
        
        <div class="p-3 mb-3 rounded-3 bg-light border d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-bold">মোট নিবন্ধিত পণ্য</div>
            <small class="text-secondary">সক্রিয় আইটেম সংখ্যা</small>
          </div>
          <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalProducts) }} টি</h4>
        </div>

        <div class="p-3 mb-3 rounded-3 {{ $lowStockCount > 0 ? 'bg-danger bg-opacity-10 border-danger' : 'bg-light' }} border d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-bold text-danger">লো-স্টক অ্যালার্ট</div>
            <small class="text-secondary">রি-অর্ডার লেভেলে নেমে গেছে</small>
          </div>
          <h4 class="fw-bold mb-0 text-danger">{{ $lowStockCount }} টি</h4>
        </div>

        <h6 class="fw-bold text-secondary text-uppercase small mt-4 mb-2">সর্বাধিক বিক্রিত পণ্য</h6>
        <ul class="list-group list-group-flush small">
          @forelse($topProducts as $tp)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
              <span class="text-truncate" style="max-width: 180px;">{{ $tp->product_name }}</span>
              <span class="badge bg-success-subtle text-success">{{ (int)$tp->total_qty }} বিক্রি</span>
            </li>
          @empty
            <li class="list-group-item px-0 py-2 text-muted">আজ কোনো বিক্রি হয়নি</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- RECENT SALES TABLE -->
<div class="card bg-white border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold mb-0">সাম্প্রতিক বিক্রয় লেনদেন</h5>
      <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">সব দেখুন →</a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ইনভয়েস #</th>
            <th>শাখা</th>
            <th>কাস্টমার</th>
            <th>তারিখ ও সময়</th>
            <th>সর্বমোট</th>
            <th>পরিশোধিত</th>
            <th>বকেয়া</th>
            <th>পেমেন্ট মেথড</th>
            <th class="text-end">অ্যাকশন</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentSales as $sale)
            <tr>
              <td>
                <a href="{{ route('sales.show', $sale->id) }}" class="fw-bold text-primary">
                  {{ $sale->invoice_no }}
                </a>
              </td>
              <td><span class="badge bg-light text-dark border">{{ $sale->branch->name ?? 'HQ' }}</span></td>
              <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
              <td class="small text-muted">{{ $sale->sale_date->format('d M, h:i A') }}</td>
              <td class="fw-bold">৳{{ number_format($sale->total, 2) }}</td>
              <td class="text-success">৳{{ number_format($sale->paid, 2) }}</td>
              <td>
                @if($sale->due > 0)
                  <span class="text-danger fw-bold">৳{{ number_format($sale->due, 2) }}</span>
                @else
                  <span class="text-muted">৳০.০০</span>
                @endif
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ $sale->payment_method }}</span>
              </td>
              <td class="text-end">
                <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="প্রিন্ট রিসিট">
                  <i data-lucide="printer" class="icon-sm"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">কোনো বিক্রয় রেকর্ড পাওয়া যায়নি</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var chartOptions = {
      series: [{
        name: 'বিক্রি (টাকা)',
        data: @json($chartSales)
      }],
      chart: {
        type: 'area',
        height: 280,
        toolbar: { show: false }
      },
      colors: ['#0d9488'],
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [20, 100]
        }
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      xaxis: {
        categories: @json($chartDates)
      },
      yaxis: {
        labels: {
          formatter: function (val) { return '৳' + val; }
        }
      },
      tooltip: {
        y: {
          formatter: function (val) { return '৳' + val.toLocaleString(); }
        }
      }
    };

    if (document.querySelector("#salesChart")) {
      var chart = new ApexCharts(document.querySelector("#salesChart"), chartOptions);
      chart.render();
    }
  });
</script>
@endpush
