<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #{{ $sale->invoice_no }} - {{ $tenant->name }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Hind Siliguri', 'Inter', monospace;
      color: #000;
      background: #fff;
      margin: 0;
      padding: 10px;
    }

    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
    }

    /* 80mm & 58mm Thermal Styles */
    .receipt-container {
      width: {{ $format === '58mm' ? '54mm' : ($format === 'a4' ? '100%' : '78mm') }};
      margin: 0 auto;
      font-size: {{ $format === '58mm' ? '11px' : ($format === 'a4' ? '14px' : '13px') }};
      line-height: 1.35;
    }

    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }
    .border-top { border-top: 1px dashed #000; }
    .border-bottom { border-bottom: 1px dashed #000; }
    .py-1 { padding-top: 4px; padding-bottom: 4px; }
    .my-2 { margin-top: 8px; margin-bottom: 8px; }

    table { width: 100%; border-collapse: collapse; }
    td, th { padding: 3px 0; }

    .btn-print {
      background: #0f172a;
      color: white;
      border: none;
      padding: 8px 16px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="no-print" style="text-align: center; margin-bottom: 15px; padding: 10px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
    <button class="btn-print" onclick="window.print()">🖨️ প্রিন্ট করুন (Print Receipt)</button>
    <a href="{{ route('sales.invoice', [$sale->id, 'format' => '58mm']) }}" style="margin-left: 10px; font-size: 13px;">58mm সাইজ</a> |
    <a href="{{ route('sales.invoice', [$sale->id, 'format' => '80mm']) }}" style="font-size: 13px;">80mm সাইজ</a> |
    <a href="{{ route('sales.invoice', [$sale->id, 'format' => 'a4']) }}" style="font-size: 13px;">A4 সাইজ</a>
  </div>

  <div class="receipt-container">
    <!-- BUSINESS HEADER -->
    <div class="text-center">
      <h2 style="margin: 0; font-size: 18px;" class="fw-bold">{{ $tenant->name }}</h2>
      <div style="font-size: 11px;">{{ $sale->branch->name ?? 'Head Office' }}</div>
      <div style="font-size: 11px;">{{ $sale->branch->address ?? $tenant->address }}</div>
      <div style="font-size: 11px;">মোবাইল: {{ $sale->branch->phone ?? $tenant->phone }}</div>
      @if($tenant->bin_number)
        <div style="font-size: 11px;">BIN / ভ্যাট নং: {{ $tenant->bin_number }}</div>
      @endif
    </div>

    <div class="border-top my-2"></div>

    <!-- INVOICE INFO -->
    <div>
      <div><strong>ইনভয়েস:</strong> {{ $sale->invoice_no }}</div>
      <div><strong>তারিখ:</strong> {{ $sale->sale_date->format('d/m/Y h:i A') }}</div>
      <div><strong>ক্যাশিয়ার:</strong> {{ $sale->cashier->name ?? 'Cashier' }}</div>
      <div><strong>গ্রাহক:</strong> {{ $sale->customer->name ?? 'সাধারণ গ্রাহক (Walk-in)' }}</div>
      @if($sale->customer && $sale->customer->phone)
        <div><strong>ফোন:</strong> {{ $sale->customer->phone }}</div>
      @endif
    </div>

    <div class="border-top border-bottom my-2 py-1">
      <table>
        <thead>
          <tr>
            <th style="text-align: left;">বিবরণ</th>
            <th class="text-center" style="width: 35px;">পরিমাণ</th>
            <th class="text-end" style="width: 50px;">দর</th>
            <th class="text-end" style="width: 55px;">মোট</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->items as $item)
            <tr>
              <td style="text-align: left;">{{ $item->product_name }}</td>
              <td class="text-center">{{ (float)$item->quantity }}</td>
              <td class="text-end">{{ number_format($item->unit_price, 1) }}</td>
              <td class="text-end fw-bold">{{ number_format($item->subtotal, 1) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- TOTALS -->
    <table>
      <tr>
        <td>সাবটোটাল:</td>
        <td class="text-end">৳{{ number_format($sale->subtotal, 2) }}</td>
      </tr>
      @if($sale->discount_amount > 0)
        <tr>
          <td>ছাড় (Discount):</td>
          <td class="text-end">-৳{{ number_format($sale->discount_amount, 2) }}</td>
        </tr>
      @endif
      @if($sale->vat_amount > 0)
        <tr>
          <td>ভ্যাট (VAT):</td>
          <td class="text-end">+৳{{ number_format($sale->vat_amount, 2) }}</td>
        </tr>
      @endif
      <tr class="fw-bold" style="font-size: 14px;">
        <td class="border-top" style="padding-top: 5px;">সর্বমোট প্রদেয়:</td>
        <td class="text-end border-top" style="padding-top: 5px;">৳{{ number_format($sale->total, 2) }}</td>
      </tr>
      <tr>
        <td>পরিশোধ (Paid):</td>
        <td class="text-end fw-bold">৳{{ number_format($sale->paid, 2) }}</td>
      </tr>
      @if($sale->due > 0)
        <tr class="fw-bold text-end" style="color: red;">
          <td style="text-align: left;">বকেয়া (Due):</td>
          <td class="text-end">৳{{ number_format($sale->due, 2) }}</td>
        </tr>
      @endif
    </table>

    <!-- PAYMENT SPLIT BREAKDOWN -->
    @if($sale->payments->count() > 0)
      <div class="border-top my-2 py-1" style="font-size: 11px;">
        <div><strong>পেমেন্ট বিবরণ:</strong></div>
        @foreach($sale->payments as $p)
          <div>- {{ strtoupper($p->method) }}: ৳{{ number_format($p->amount, 2) }} {{ $p->trx_id ? '(Trx: '.$p->trx_id.')' : '' }}</div>
        @endforeach
      </div>
    @endif

    <div class="border-top my-2"></div>

    <div class="text-center" style="font-size: 11px; margin-top: 8px;">
      <div>ধন্যবাদ! আবার আসবেন।</div>
      <div style="font-size: 9px; color: #666; margin-top: 4px;">Powered by SmartPOS SaaS 🇧🇩</div>
    </div>
  </div>

  <script>
    // Auto-trigger print on page open if desired
    // window.onload = function() { window.print(); }
  </script>
</body>
</html>
